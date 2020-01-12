<?php
declare(strict_types=1);

namespace luca28pet\PortalsPE\command;

use luca28pet\PortalsPE\flag\FlagsManager;
use luca28pet\PortalsPE\Main;
use luca28pet\PortalsPE\Portal;
use luca28pet\PortalsPE\selection\CompletePortalSelection;
use luca28pet\PortalsPE\utils\LookingVector3;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use function array_keys;
use function array_shift;
use function implode;
use function strtolower;

class PortalCommand extends Command implements PluginIdentifiableCommand{

    /** @var Main */
    private $plugin;

    public function __construct(Main $plugin){
        parent::__construct('portal', 'Main PortalsPE commmand', '/portal <pos1|pos2|create|list|delete|flag>');
        $this->plugin = $plugin;
        $this->setPermission('portalspe.command.portal');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : bool{
        if(!($sender instanceof Player)){
            $sender->sendMessage('This command must be ran in-game');
            return true;
        }
        if(!isset($args[0])){
            $sender->sendMessage($this->getUsage());
            return true;
        }
        $subCommand = strtolower(array_shift($args));
        switch($subCommand){
            case 'pos1':
                $ses = $this->plugin->getSessionManager()->getSession($sender);
                if($ses === null){
                    $this->plugin->getSessionManager()->initSession($sender);
                    $ses = $this->plugin->getSessionManager()->getSession($sender);
                }
                $ses->setSelectingFirstBlock(true);
                $sender->sendMessage('Please break or place the first position');
                return true;
            case 'pos2':
                $ses = $this->plugin->getSessionManager()->getSession($sender);
                if($ses === null){
                    $this->plugin->getSessionManager()->initSession($sender);
                    $ses = $this->plugin->getSessionManager()->getSession($sender);
                }
                $ses->setSelectingSecondBlock(true);
                $sender->sendMessage('Please break or place the second position');
                return true;
            case 'create':
                if(!isset($args[0])){
                    $sender->sendMessage('Please state a portal name');
                    return true;
                }
                $ses = $this->plugin->getSessionManager()->getSession($sender);
                if($ses === null){
                    $sender->sendMessage('Please select both positions first with /portal pos1 and /portal pos2');
                    return true;
                }
                $selection = $ses->getSelection();
                if(!$selection->isComplete()){
                    $sender->sendMessage('Please select both positions first with /portal pos1 and /portal pos2');
                    return true;
                }
                if(!$selection->isValid()){
                    $sender->sendMessage('The positions must be in the same world');
                    return true;
                }
                $this->plugin->addPortal(
                    new Portal(
                        $args[0],
                        new CompletePortalSelection(
                            $selection->getFirstBlock(),
                            $selection->getSecondBlock(),
                            $selection->getFirstBlockFolderName()
                        ),
                        LookingVector3::fromObject($sender, $sender->yaw, $sender->pitch),
                        $sender->getLevel()->getFolderName(),
                        []
                    )
                );
                $this->plugin->savePortals();
                $sender->sendMessage('Added portal '.$args[0]);
                return true;
            case 'list':
                $sender->sendMessage('Portals: '.implode(', ', array_keys($this->plugin->getPortals())));
                return true;
            case 'delete':
                if(!isset($args[0])){
                    $sender->sendMessage('Please state portal name');
                    return true;
                }
                $portal = $this->plugin->getPortalByName($args[0]);
                if($portal === null){
                    $sender->sendMessage('Portal '.$args[0].' does not exist');
                    return true;
                }
                $this->plugin->removePortal($portal);
                $this->plugin->savePortals();
                $sender->sendMessage('Removed portal '.$args[0]);
                return true;
            case 'flag':
                if(!isset($args[0])){
                    $sender->sendMessage('Please state portal name');
                    return true;
                }
                $portalName = array_shift($args);
                $portal = $this->plugin->getPortalByName($portalName);
                if($portal === null){
                    $sender->sendMessage('Portal '.$args[0].' does not exist');
                    return true;
                }
                if(!isset($args[0])){
                    $sender->sendMessage('Please state flag name. Available flags: '.implode(', ', array_keys(FlagsManager::DEFAULTS)));
                    return true;
                }
                $flagName = strtolower(array_shift($args));
                switch($flagName){
                    case 'teleport':
                        if(!isset($args[0])){
                            $sender->sendMessage('Please state flag value. Allowed values: true, false');
                            return true;
                        }
                        $value = strtolower(array_shift($args));
                        if($value !== 'true' && $value !== 'false'){
                            $sender->sendMessage('Flag value '.$value.' not allowed for flag teleport. Allowed values: true, false');
                            return true;
                        }
                        if($value === 'true'){
                            $portal->getFlagsManager()->setTeleport(true);
                        }else{
                            $portal->getFlagsManager()->setTeleport(false);
                        }
                        $this->plugin->savePortals();
                        $sender->sendMessage('Flag teleport set to '.$value.' for portal '.$portalName);
                        return true;
                    case 'permissionmode':
                        if(!isset($args[0])){
                            $sender->sendMessage('Please state flag value. Allowed values: true, false');
                            return true;
                        }
                        $value = strtolower(array_shift($args));
                        if($value !== 'true' && $value !== 'false'){
                            $sender->sendMessage('Flag value '.$value.' not allowed for flag permissionMode. Allowed values: true, false');
                            return true;
                        }
                        if($value === 'true'){
                            $portal->getFlagsManager()->setPermissionMode(true);
                        }else{
                            $portal->getFlagsManager()->setPermissionMode(false);
                        }
                        $this->plugin->savePortals();
                        $sender->sendMessage('Flag permissionMode set to '.$value.' for portal '.$portalName);
                        return true;
                    case 'autoload':
                        if(!isset($args[0])){
                            $sender->sendMessage('Please state flag value. Allowed values: true, false');
                            return true;
                        }
                        $value = strtolower(array_shift($args));
                        if($value !== 'true' && $value !== 'false'){
                            $sender->sendMessage('Flag value '.$value.' not allowed for flag autoload. Allowed values: true, false');
                            return true;
                        }
                        if($value === 'true'){
                            $portal->getFlagsManager()->setAutoLoad(true);
                        }else{
                            $portal->getFlagsManager()->setAutoLoad(false);
                        }
                        $this->plugin->savePortals();
                        $sender->sendMessage('Flag autoload set to '.$value.' for portal '.$portalName);
                        return true;
                    case 'addcommand':
                        if(!isset($args[0])){
                            $sender->sendMessage('Please state a command to add. You can use the variables {player} and {portal}');
                            return true;
                        }
                        $cmd = implode(' ', $args);
                        $portal->getFlagsManager()->addCommand($cmd);
                        $this->plugin->savePortals();
                        $sender->sendMessage('Added command '.$cmd.' to portal '.$portalName);
                        return true;
                    case 'rmcommand':
                        if(!isset($args[0])){
                            $sender->sendMessage('Please state a command to remove. You can use the variables {player} and {portal}');
                            return true;
                        }
                        $cmd = implode(' ', $args);
                        if(!$portal->getFlagsManager()->hasCommand($cmd)){
                            $sender->sendMessage('Portal '.$portalName.' does not have command '.$cmd);
                            return true;
                        }
                        $portal->getFlagsManager()->removeCommand($cmd);
                        $this->plugin->savePortals();
                        $sender->sendMessage('Removed command '.$cmd.' from portal '.$portalName);
                        return true;
                    case 'listcommands':
                        $sender->sendMessage('Portal '.$portalName.' commands: '.implode(', ', $portal->getFlagsManager()->getCommands()));
                        return true;
                    default:
                        $sender->sendMessage('Flag '.$flagName.' does not exist. Available flags: '.implode(', ', array_keys(FlagsManager::DEFAULTS)));
                        return true;
                }
            default:
                $sender->sendMessage($this->getUsage());
                return true;
        }
    }

    public function getPlugin() : Plugin{
        return $this->plugin;
    }

}