<?php

namespace PortalsPE;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;

class EventListener implements Listener{

    private $plugin;

    public function __construct(Main $plugin){
        $this->plugin = $plugin;
    }

    public function onMove(PlayerMoveEvent $event){
        $this->plugin->isInPortal($event->getPlayer());
    }
    
    public function onPlace(BlockPlaceEvent $event){
        if(isset($this->plugin->sel1[$event->getPlayer()->getName()])){
            $this->plugin->pos1 = [$event->getBlock()->x, $event->getBlock()->y, $event->getBlock()->z, $event->getBlock()->level->getName()];
            $event->getPlayer()->sendMessage("Position 1 set");
            unset($this->plugin->sel1);
        }elseif(isset($this->plugin->sel2[$event->getPlayer()->getName()])){
            $this->plugin->pos1 = [$event->getBlock()->x, $event->getBlock()->y, $event->getBlock()->z, $event->getBlock()->level->getName()];
            $event->getPlayer()->sendMessage("Position 2 set");
            unset($this->plugin->sel2);
        }    
    }

    public function onBreak(BlockBreakEvent $event){
        if(isset($this->plugin->sel1[$event->getPlayer()->getName()])){
            $this->plugin->pos1 = [$event->getBlock()->x, $event->getBlock()->y, $event->getBlock()->z, $event->getBlock()->level->getName()];
            $event->getPlayer()->sendMessage("Position 1 set");
            unset($this->plugin->sel1);
        }elseif(isset($this->plugin->sel2[$event->getPlayer()->getName()])){
            $this->plugin->pos1 = [$event->getBlock()->x, $event->getBlock()->y, $event->getBlock()->z, $event->getBlock()->level->getName()];
            $event->getPlayer()->sendMessage("Position 2 set");
            unset($this->plugin->sel2);
        }
    }

}