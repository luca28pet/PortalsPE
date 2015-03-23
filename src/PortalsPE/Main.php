<?php

namespace PortalsPE;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class Main extends PluginBase implements Listener{

    /**@var Position[]*/
    public $pos1;
    /**@var Position[]*/
    public $pos2;
    /**@var Config;*/
    public $configuration;
    /**@var Config;*/
    public $portals;

    public function onEnable(){
        if(!is_dir($this->getDataFolder())){
            @mkdir($this->getDataFolder());
        }
        $this->configuration = new Config($this->getDataFolder()."config.yml", Config::YAML, array(
            "message-on-teleport" => "You entered the portal for %destination%",
//            "enable-permissions-settings" => "disabled",
            "message-on-insufficient-permissions" => "You haven't permissions to use this portal"
        ));
        $this->portals = new Config($this->getDataFolder()."portals.yml", Config::YAML);
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onDisable(){
        $this->configuration->save();
        $this->portals->save();
    }

    public function onCommand(CommandSender $sender, Command $command, $label, array $args){
        if(strtolower($command->getName()) === "portal"){
            if(!isset($args[0])){
                return false;
            }
            $subCommand = array_shift($args);
            switch($subCommand){
                case "pos1":
                    if(!$sender instanceof Player){
                        $sender->sendMessage("Run this command in game");
                        return true;
                    }
                    if(!$sender->hasPermission("portalspe.admin")){
                        $sender->sendMessage("You don't have permission to use this command.");
                        return true;
                    }
                    $pos = new Position($sender->getX(), $sender->getY(), $sender->getZ(), $sender->getLevel());
                    $this->pos1[$sender->getId()] = $pos;
                    $sender->sendMessage("Position 1 set:\n$pos");
                    return true;
                break;
                case "pos2":
                    if(!$sender instanceof Player){
                        $sender->sendMessage("Run this command in game");
                        return true;
                    }
                    if(!$sender->hasPermission("portalspe.admin")){
                        $sender->sendMessage("You don't have permission to use this command.");
                        return true;
                    }
                    $pos = new Position($sender->getX(), $sender->getY(), $sender->getZ(), $sender->getLevel());
                    $this->pos2[$sender->getId()] = $pos;
                    $sender->sendMessage("Position 2 set:\n$pos");
                    return true;
                break;
                case "create":
                    if(!$sender instanceof Player){
                        $sender->sendMessage("Run this command in game");
                        return true;
                    }
                    if(!$sender->hasPermission("portalspe.admin")){
                        $sender->sendMessage("You don't have permission to use this command.");
                        return true;
                    }
                    if(!isset($this->pos1[$sender->getId()]) or !isset($this->pos2[$sender->getId()])){
                        $sender->sendMessage("Please select both positions first");
                        return true;
                    }
                    if(!isset($args[0])){
                        $sender->sendMessage("Please specify the portal name.");
                        return true;
                    }
                    if($this->pos1[$sender->getId()]->getLevel()->getId() !== $this->pos2[$sender->getId()]->getLevel()->getId()){
                        $sender->sendMessage("Positions are in different levels");
                        return true;
                    }
                    if($this->portals->exists($args[0])){
                        $sender->sendMessage("A portal with that name already exists");
                        return true;
                    }
                    $portals = $this->portals->getAll();
                    $n = array_shift($args);
                    $portals[$n] = [
                        "x" => $this->pos1[$sender->getId()]->getX(), "y" => $this->pos1[$sender->getId()]->getY(), "z" => $this->pos1[$sender->getId()]->getZ(),
                        "x2" => $this->pos2[$sender->getId()]->getX(), "y2" => $this->pos2[$sender->getId()]->getY(), "z2" => $this->pos2[$sender->getId()]->getZ(),
                        "level" => $this->pos1[$sender->getId()]->getLevel()->getName(),
                        "dx" => $sender->getX(), "dy" => $sender->getY(), "dz" => $sender->getZ(), "dlevel" => $sender->getLevel()->getName()
                    ];
                    $this->portals->setAll($portals);
                    $this->portals->save();
                    $sender->sendMessage("Portal created");
                    unset($portals);
                    unset($this->pos1[$sender->getId()]);
                    unset($this->pos2[$sender->getId()]);
                    return true;
                break;
                default:
                    $sender->sendMessage("Strange argument ".$subCommand.".");
                    $sender->sendMessage($command->getUsage());
                    return true;
                break;
            }
        }
        return true;
    }

    public function onMove(PlayerMoveEvent $event){
        $portals = $this->portals->getAll();
        $x = $event->getPlayer()->getX();
        $y = $event->getPlayer()->getY();
        $z = $event->getPlayer()->getZ();
        foreach($portals as $portal){
            if($x >= min($portal['x'], $portal['x2']) and $x <= max($portal['x'], $portal['x2']) and $y >= min($portal['y'], $portal['y2']) and $y <= max($portal['y'], $portal['y2']) and $z >= min($portal['z'], $portal['z2']) and $z <= max($portal['z'], $portal['z2']) and $event->getPlayer()->getLevel()->getName() == $portal['level']){
                if(!$this->getServer()->isLevelLoaded($portal['dlevel'])){
                    $this->getServer()->loadLevel($portal['dlevel']);
                }
                $event->getPlayer()->teleport(new Position($portal['dx'], $portal['dy'], $portal['dz'], $this->getServer()->getLevelByName($portal['dlevel'])));
                $event->getPlayer()->sendMessage("You entered the portal to: ".$portal['dx'].", ".$portal['dy'].", ".$portal['dz'].", ".$portal['dlevel']."!");
            }
        }
    }

}