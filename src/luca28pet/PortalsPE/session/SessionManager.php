<?php
declare(strict_types=1);

namespace luca28pet\PortalsPE\session;

use luca28pet\PortalsPE\selection\PartialPortalSelection;
use pocketmine\Player;

class SessionManager{

    /** @var PlayerSession[] */
    private $sessions = [];

    public function initSession(Player $player) : void{
        $this->sessions[$player->getName()] = new PlayerSession(
            $player,
            new PartialPortalSelection(null, null, null, null),
            false,
            false
        );
    }

    public function getSession(Player $player) : ?PlayerSession{
        return $this->sessions[$player->getName()] ?? null;
    }

    public function removeSession(Player $player) : void{
        $ses = $this->getSession($player);
        if($ses !== null){
            $ses->close();
            unset($this->sessions[$player->getName()]);
        }
    }

    public function getSessions() : array{
        return $this->sessions;
    }

    public function setSessions(array $sessions) : void{
        $this->sessions = $sessions;
    }

}