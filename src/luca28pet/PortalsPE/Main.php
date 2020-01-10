<?php

namespace luca28pet\PortalsPE;

use luca28pet\PortalsPE\command\PortalCommand;
use luca28pet\PortalsPE\listener\BlockBreakEventListener;
use luca28pet\PortalsPE\listener\BlockPlaceEventListener;
use luca28pet\PortalsPE\listener\PlayerMoveEventListener;
use luca28pet\PortalsPE\listener\PlayerQuitEventListener;
use luca28pet\PortalsPE\selection\CompletePortalSelection;
use luca28pet\PortalsPE\session\SessionManager;
use luca28pet\PortalsPE\task\PortalTask;
use luca28pet\PortalsPE\utils\LookingVector3;
use luca28pet\PortalsPE\utils\TeleportResult;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function json_decode;
use function json_encode;
use const JSON_THROW_ON_ERROR;

class Main extends PluginBase{

    /** @var SessionManager */
    private $sessionManager;

    /** @var  Portal[] */
    private $portals = [];

    public function onEnable() : void{
        $this->saveDefaultConfig();

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
            if($portal->isInside($player)){
                switch($portal->onEnter($player)->getResult()){
                    case TeleportResult::SUCCESS:
                        $player->sendMessage($this->getConfig()->get('message-tp'));
                        break;
                    case TeleportResult::NO_PERM:
                        $player->sendMessage($this->getConfig()->get('message-no-perm'));
                        break;
                    case TeleportResult::WORLD_NOT_LOADED:
                        $player->sendMessage($this->getConfig()->get('message-error'));
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