<?php
declare(strict_types=1);

namespace luca28pet\PortalsPE;

use luca28pet\PortalsPE\flag\FlagsManager;
use luca28pet\PortalsPE\selection\CompletePortalSelection;
use luca28pet\PortalsPE\utils\LookingVector3;
use luca28pet\PortalsPE\utils\PortalResponse;
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

    public function onEnter(Player $player) : PortalResponse{
        if($this->flagsManager->getPermissionMode() && !$player->hasPermission('portalspe.portal.'.$this->name)){
            return PortalResponse::NO_PERM();
        }

        if($this->flagsManager->getTeleport()){
            $level = $player->getServer()->getLevelByName($this->destinationFolderName);
            if($level === null){
                if($this->flagsManager->getAutoLoad()){
                    if(!$this->autoloadDestination($player->getServer())){
                        return PortalResponse::WORLD_NOT_LOADED();
                    }
                }else{
                    return PortalResponse::WORLD_NOT_LOADED();
                }
                $level = $player->getServer()->getLevelByName($this->destinationFolderName);
            }
            $player->teleport(new Location($this->destination->x, $this->destination->y, $this->destination->z, $this->destination->yaw, $this->destination->pitch, $level));
            foreach($this->flagsManager->getCommands() as $cmd){
                $player->getServer()->dispatchCommand(new ConsoleCommandSender(), str_replace(['{player}', '{portal}'], [$player->getName(), $this->name], $cmd));
            }
            return PortalResponse::SUCCESS_TP();
        }

        foreach($this->flagsManager->getCommands() as $cmd){
            $player->getServer()->dispatchCommand(new ConsoleCommandSender(), str_replace(['{player}', '{portal}'], [$player->getName(), $this->name], $cmd));
        }
        return PortalResponse::SUCCESS_NO_TP();
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