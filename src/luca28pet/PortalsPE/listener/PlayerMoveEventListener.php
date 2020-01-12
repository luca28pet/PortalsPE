<?php
declare(strict_types=1);

namespace luca28pet\PortalsPE\listener;

use pocketmine\event\player\PlayerMoveEvent;

class PlayerMoveEventListener extends BaseListener{

    /**
     * @param PlayerMoveEvent $event
     * @priority MONITOR
     * @ignoreCancelled true
     */
    public function onPlayerMove(PlayerMoveEvent $event) : void{
        if($event->getFrom()->distanceSquared($event->getTo()) > 0.1){
            $this->plugin->checkPortal($event->getPlayer());
        }
    }
}