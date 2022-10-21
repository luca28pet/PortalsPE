<?php
declare(strict_types=1);

namespace luca28pet\PortalsPE;

use luca28pet\PortalsPE\command\PortalCommand;
use luca28pet\PortalsPE\listener\BlockBreakEventListener;
use luca28pet\PortalsPE\listener\BlockPlaceEventListener;
use luca28pet\PortalsPE\listener\PlayerMoveEventListener;
use luca28pet\PortalsPE\listener\PlayerQuitEventListener;
use luca28pet\PortalsPE\selection\CompletePortalSelection;
use luca28pet\PortalsPE\session\SessionManager;
use luca28pet\PortalsPE\task\PortalTask;
use luca28pet\PortalsPE\utils\LanguageManager;
use luca28pet\PortalsPE\utils\LookingVector3;
use luca28pet\PortalsPE\utils\PortalResponse;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use function fclose;
use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function json_decode;
use function json_encode;
use function rename;
use function stream_get_contents;
use function yaml_parse;
use const JSON_THROW_ON_ERROR;

class Main extends PluginBase{

    public const CONFIG_VERSION = 1;

    /** @var SessionManager */
    private $sessionManager;

    /** @var LanguageManager */
    private $langManager;

    /** @var  Portal[] */
    private $portals = [];

    public function onEnable() : void{
        $this->saveDefaultConfig();

        if($this->getConfig()->get('version', 0) !== self::CONFIG_VERSION){
            $this->getLogger()->alert('Your config.yml version is not supported. The old file has been renamed and a new one has been created.');
            rename($this->getDataFolder().'config.yml', $this->getDataFolder().'config.yml.old');
            $this->saveDefaultConfig();
        }

        if(file_exists($this->getDataFolder().'portals.json')){
            $data = json_decode(file_get_contents($this->getDataFolder().'portals.json'), true, 512, JSON_THROW_ON_ERROR);
            foreach($data as $name => $portalData){
                $this->portals[$name] = new Portal(
                    $name,
                    CompletePortalSelection::fromArray($portalData['selection']),
                    new LookingVector3($portalData['destination']['x'], $portalData['destination']['y'], $portalData['destination']['z'], $portalData['destination']['yaw'], $portalData['destination']['pitch']),
                    $portalData['destinationFolderName'],
                    $portalData['flags']
                );
            }
        }

        $this->sessionManager = new SessionManager();

        $defaultCfg = $this->getResource('config.yml');
        if($defaultCfg === null){
            $this->getLogger()->alert('Could not get default config.yml');
            $this->getServer()->getPluginManager()->disablePlugin($this);
            return;
        }
        $contents = stream_get_contents($defaultCfg);
        fclose($defaultCfg);
        if($contents === false){
            $this->getLogger()->alert('Could not get default config.yml');
            $this->getServer()->getPluginManager()->disablePlugin($this);
            return;
        }
        $this->langManager = new LanguageManager($this->getConfig()->get('lang'), yaml_parse($contents));

        PortalResponse::init();

        $this->getServer()->getCommandMap()->register('portalspe', new PortalCommand($this));

        $this->getServer()->getPluginManager()->registerEvents(new BlockBreakEventListener($this), $this);
        $this->getServer()->getPluginManager()->registerEvents(new BlockPlaceEventListener($this), $this);
        $this->getServer()->getPluginManager()->registerEvents(new PlayerQuitEventListener($this), $this);

        if($this->getConfig()->get('movement-detection') === 'event'){
            $this->getServer()->getPluginManager()->registerEvents(new PlayerMoveEventListener($this), $this);
        }elseif($this->getConfig()->get('movement-detection') === 'task'){
            $this->getScheduler()->scheduleRepeatingTask(new PortalTask($this), 20 * $this->getConfig()->get('task-time'));
        }else{
            $this->getLogger()->alert('Unknown movement-detection field. Please use event or task');
            $this->getServer()->getPluginManager()->disablePlugin($this);
        }
    }

    public function onDisable() : void{
        $this->portals = [];
    }

    public function checkPortal(Player $player) : void{
        foreach($this->getPortals() as $portal){
            /** @var Portal $portal */
            if($portal->isInside($player->getPosition())){
                switch($portal->onEnter($player)->getResult()){
                    case PortalResponse::SUCCESS_TP:
                    case PortalResponse::SUCCESS_NO_TP:
                        $player->sendMessage($this->langManager->getTranslation('success', ['%player' => $player->getDisplayName(), '%portal' => $portal->getName()]));
                        break;
                    case PortalResponse::NO_PERM:
                        $player->sendMessage($this->langManager->getTranslation('no-perm', ['%player' => $player->getDisplayName(), '%portal' => $portal->getName()]));
                        break;
                    case PortalResponse::WORLD_NOT_LOADED:
                        $player->sendMessage($this->langManager->getTranslation('message-error', ['%player' => $player->getDisplayName(), '%portal' => $portal->getName()]));
                        break;
                }
                break;
            }
        }
    }

    public function getSessionManager() : SessionManager{
        return $this->sessionManager;
    }

    public function setSessionManager(SessionManager $sessionManager) : void{
        $this->sessionManager = $sessionManager;
    }

    public function addPortal(Portal $portal) : void{
        $this->portals[$portal->getName()] = $portal;
    }

    public function getPortalByName(string $portalName) : ?Portal{
        return $this->portals[$portalName] ?? null;
    }

    public function removePortal(Portal $portal) : void{
        foreach($this->portals as $name => $p){
            if($p === $portal){
                unset($this->portals[$name]);
                break;
            }
        }
    }

    public function savePortals() : void{
        $data = [];
        foreach($this->portals as $name => $portal){
            $data[$name] = $portal->toArray();
        }
        file_put_contents($this->getDataFolder().'portals.json', json_encode($data, JSON_THROW_ON_ERROR));
    }

    public function getPortals() : array{
        return $this->portals;
    }

    public function setPortals(array $portals) : void{
        $this->portals = $portals;
    }

}