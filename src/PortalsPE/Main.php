<?php

namespace PortalsPE;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\EventPriority;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\plugin\MethodEventExecutor;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase{

    public $sel1 = [];
    public $sel2 = [];
    public $pos1 = [];
    public $pos2 = [];
    public $portals;

    public function onEnable() : void{
        $this->saveDefaultConfig();
        if(!file_exists($this->getDataFolder()."portals.yml")){
            yaml_emit_file($this->getDataFolder()."portals.yml", []);
        }
        $this->portals = yaml_parse_file($this->getDataFolder()."portals.yml");
        $this->getServer()->getPluginManager()->registerEvent("pocketmine\\event\\block\\BlockBreakEvent", $listener = new EventListener($this), EventPriority::HIGHEST, new MethodEventExecutor("onBreak"), $this, true);
        $this->getServer()->getPluginManager()->registerEvent("pocketmine\\event\\block\\BlockPlaceEvent", $listener, EventPriority::HIGHEST, new MethodEventExecutor("onPlace"), $this, true);
        if($this->getConfig()->get("movement-detection") === "event"){
            $this->getServer()->getPluginManager()->registerEvent("pocketmine\\event\\player\\PlayerMoveEvent", $listener, EventPriority::MONITOR, new MethodEventExecutor("onMove"), $this, true);
        }elseif($this->getConfig()->get("movement-detection") === "task"){
            $this->getServer()->getScheduler()->scheduleRepeatingTask(new PortalTask($this), 20 * $this->getConfig()->get("task-time"));
        }else{
            $this->getLogger()->alert("Unknown movement-detection field. Please use event or task");
            $this->getServer()->getPluginManager()->disablePlugin($this);
        }
    }

    public function onDisable() : void{
        yaml_emit_file($this->getDataFolder()."portals.yml", $this->portals);
    }

    public function isInPortal(Player $player) : bool{
        $x = round($player->x);
        $y = round($player->y);
        $z = round($player->z);
        foreach($this->portals as $name => $portal){
            if(($x >= $portal["x"] and $x <= $portal["x2"]) and ($y >= $portal["y"] and $y <= $portal["y2"]) and ($z >= $portal["z"] and $z <= $portal["z2"]) and $player->getLevel()->getFolderName() === $portal["level"]){
                if($this->getConfig()->get("permission-mode") === true and !$player->hasPermission("portal.".$name)){
                    $player->sendMessage($this->getConfig()->get("message-no-perm"));
                    return false;
                }
                if(!$this->getServer()->isLevelGenerated($portal["dlevel"])){
                    $player->sendMessage($this->getConfig()->get("message-error"));
                    return false;
                }
                if(!$this->getServer()->isLevelLoaded($portal["dlevel"])){
                    if($this->getConfig()->get("auto-load") === true){
                        $this->getServer()->loadLevel($portal["dlevel"]);
                    }else{
                        $player->sendMessage($this->getConfig()->get("message-error"));
                        return false;
                    }
                }
                $player->teleport(new Position($portal["dx"], $portal["dy"], $portal["dz"], $this->getServer()->getLevelByName($portal["dlevel"])));
                $player->sendMessage($this->getConfig()->get("message-tp"));
                return true;
            }
        }
        return false;
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
        if(strtolower($command->getName()) === "portal"){
            if(!isset($args[0])){
                return false;
            }
            $subCommand = array_shift($args);
            switch($subCommand){
                case "pos1":
                    if(!($sender instanceof Player)){
                        $sender->sendMessage("Run this command in game");
                        return true;
                    }
                    if(!$sender->hasPermission("portalspe.admin")){
                        $sender->sendMessage("You don't have permission to use this command");
                        return true;
                    }
                    $this->sel1[$sender->getName()] = true;
                    $sender->sendMessage("Please place or break the first position");
                    return true;
                case "pos2":
                    if(!($sender instanceof Player)){
                        $sender->sendMessage("Run this command in game");
                        return true;
                    }
                    if(!$sender->hasPermission("portalspe.admin")){
                        $sender->sendMessage("You don't have permission to use this command");
                        return true;
                    }
                    $this->sel2[$sender->getName()] = true;
                    $sender->sendMessage("Please place or break the second position");
                    return true;
                case "create":
                    if(!($sender instanceof Player)){
                        $sender->sendMessage("Run this command in game");
                        return true;
                    }
                    if(!$sender->hasPermission("portalspe.admin")){
                        $sender->sendMessage("You don't have permission to use this command");
                        return true;
                    }
                    if(!isset($this->pos1[$sender->getName()]) or !isset($this->pos2[$sender->getName()])){
                        $sender->sendMessage("Please select both positions first");
                        return true;
                    }
                    if(!isset($args[0])){
                        $sender->sendMessage("Please specify the portal name");
                        return true;
                    }
                    if($this->pos1[$sender->getName()][3] !== $this->pos2[$sender->getName()][3]){
                        $sender->sendMessage("Positions are in different levels");
                        return true;
                    }
                    if(isset($this->portals[strtolower($args[0])])){
                        $sender->sendMessage("A portal with that name already exists");
                        return true;
                    }
                    $this->portals[strtolower($args[0])] = [
                        "x" => min($this->pos1[$sender->getName()][0], $this->pos2[$sender->getName()][0]),
                        "y" => min($this->pos1[$sender->getName()][1], $this->pos2[$sender->getName()][1]),
                        "z" => min($this->pos1[$sender->getName()][2], $this->pos2[$sender->getName()][2]),
                        "x2" => max($this->pos1[$sender->getName()][0], $this->pos2[$sender->getName()][0]),
                        "y2" => max($this->pos1[$sender->getName()][1], $this->pos2[$sender->getName()][1]),
                        "z2" => max($this->pos1[$sender->getName()][2], $this->pos2[$sender->getName()][2]),
                        "level" => $this->pos1[$sender->getName()][3],
                        "dx" => $sender->x, "dy" => $sender->y, "dz" => $sender->z, "dlevel" => $sender->getLevel()->getFolderName()
                    ];
                    yaml_emit_file($this->getDataFolder()."portals.yml", $this->portals);
                    $sender->sendMessage("Portal created");
                    unset($this->pos1[$sender->getName()]);
                    unset($this->pos2[$sender->getName()]);
                    return true;
                case "list":
                    if(!$sender->hasPermission("portalspe.admin")){
                        $sender->sendMessage("You don't have permission to use this command");
                        return true;
                    }
                    $sender->sendMessage("Portals: ".implode(", ", array_keys($this->portals)));
                    return true;
                case "delete":
                    if(!$sender->hasPermission("portalspe.admin")){
                        $sender->sendMessage("You don't have permission to use this command");
                        return true;
                    }
                    if(!isset($this->portals[strtolower($args[0])])){
                        $sender->sendMessage("A portal with that name does not exist");
                        return true;
                    }
                    unset($this->portals[strtolower($args[0])]);
                    yaml_emit_file($this->getDataFolder()."portals.yml", $this->portals);
                    $sender->sendMessage("You have deleted the portal");
                    return true;
                case "fill":
                    if(!$sender->hasPermission("portalspe.admin")){
                        $sender->sendMessage("You don't have permission to use this command");
                        return true;
                    }
                    if(!isset($args[0])){
                        $sender->sendMessage("Please specify the portal name");
                        return true;
                    }
                    if(!isset($args[1])){
                        $sender->sendMessage("Please specify the block id");
                        return true;
                    }
                    $name = strtolower($args[0]);
                    if(!isset($this->portals[$name])){
                        $sender->sendMessage("A portal with that name does not exist");
                        return true;
                    }
                    $level = $this->getServer()->getLevelByName($this->portals[$name]["level"]);
                    if($level === null){
                        $sender->sendMessage("This portal is in a world that is not loaded");
                        return true;
                    }
                    for($x = $this->portals[$name]["x"]; $x <= $this->portals[$name]["x2"]; $x++){
                        for($y = $this->portals[$name]["y"]; $y <= $this->portals[$name]["y2"]; $y++){
                            for($z = $this->portals[$name]["z"]; $z <= $this->portals[$name]["z2"]; $z++){
                                if($level->getBlockIdAt($x, $y, $z) === 0){
                                    $level->setBlockIdAt($x, $y, $z, $args[1]);
                                    if(isset($args[2])){
                                        $level->setBlockDataAt($x, $y, $z, $args[2]);
                                    }
                                }
                            }
                        }
                    }
                    $sender->sendMessage("Portal filled");
                    return true;
                default:
                    $sender->sendMessage("Strange argument ".$subCommand.".");
                    $sender->sendMessage($command->getUsage());
                    return true;
            }
        }
        return true;
    }

}