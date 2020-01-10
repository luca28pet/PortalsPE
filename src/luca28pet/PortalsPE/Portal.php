<?php

namespace luca28pet\PortalsPE;

use luca28pet\PortalsPE\flag\FlagsManager;
use luca28pet\PortalsPE\selection\CompletePortalSelection;
use luca28pet\PortalsPE\utils\LookingVector3;
use luca28pet\PortalsPE\utils\TeleportResult;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\level\Location;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\Server;
use function str_replace;

class Portal{

    /** @var string */
    private $name;
    /** @var CompletePortalSelection */
    private $selection;
    /** @var LookingVector3 */
    private $destination;
    /** @var string */
    private $destinationFolderName;
    /** @var FlagsManager */
    private $flagsManager;

    public function __construct(string $name, CompletePortalSelection $selection, LookingVector3 $destination, string $destinationFolderName, array $flags){
        $this->name = $name;
        $this->selection = $selection;
        $this->destination = $destination;
        $this->destinationFolderName = $destinationFolderName;
        $this->flagsManager = new FlagsManager($flags);
    }

    public function getName() : string{
        return $this->name;
    }

    public function setName(string $name) : void{
        $this->name = $name;
    }

    public function getSelection() : CompletePortalSelection{
        return $this->selection;
    }

    public function setSelection(CompletePortalSelection $selection) : void{
        $this->selection = $selection;
    }

    public function getDestination() : LookingVector3{
        return $this->destination;
    }

    public function setDestination(LookingVector3 $destination) : void{
        $this->destination = $destination;
    }

    public function getDestinationFolderName() : string{
        return $this->destinationFolderName;
    }

    public function setDestinationFolderName(string $destinationFolderName) : void{
        $this->destinationFolderName = $destinationFolderName;
    }

    public function getFlagsManager() : FlagsManager{
        return $this->flagsManager;
    }

    public function setFlagsManager(FlagsManager $flagsManager) : void{
        $this->flagsManager = $flagsManager;
    }

    public function autoloadDestination(Server $server) : bool{
        return $server->loadLevel($this->destinationFolderName);
    }

    public function isInside(Position $position) : bool{
        return $this->selection->isInside($position);
    }

    public function onEnter(Player $player) : TeleportResult{
        if($this->flagsManager->getPermissionMode() && !$player->hasPermission('portalspe.portal.'.$this->name)){
            return new TeleportResult(TeleportResult::NO_PERM);
        }
        $level = $player->getServer()->getLevelByName($this->destinationFolderName);
        if($level === null){
            if($this->flagsManager->getAutoLoad()){
                if(!$this->autoloadDestination($player->getServer())){
                    return new TeleportResult(TeleportResult::WORLD_NOT_LOADED);
                }
            }else{
                return new TeleportResult(TeleportResult::WORLD_NOT_LOADED);
            }
            $level = $player->getServer()->getLevelByName($this->destinationFolderName);
        }
        $player->teleport(new Location($this->destination->x, $this->destination->y, $this->destination->z, $this->destination->yaw, $this->destination->pitch, $level));
        foreach($this->flagsManager->getCommands() as $cmd){
            $player->getServer()->dispatchCommand(new ConsoleCommandSender(), str_replace(['{player}', '{portal}'], [$player->getDisplayName(), $this->name], $cmd));
        }
        return new TeleportResult(TeleportResult::SUCCESS);
    }

    public function toArray() : array{
        return [
            'name' => $this->name,
            'selection' => $this->selection->toArray(),
            'destination' => ['x' => $this->destination->x, 'y' => $this->destination->y, 'z' => $this->destination->z, 'yaw' => $this->destination->yaw, 'pitch' => $this->destination->pitch],
            'destinationFolderName' => $this->destinationFolderName,
            'flags' => $this->flagsManager->getFlags()
        ];
    }

}