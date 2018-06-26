<?php

namespace PortalsPE;

use pocketmine\scheduler\Task;

class PortalTask extends Task {
    
    private $plugin;
    
    public function __construct(Main $plugin){
        $this->plugin = $plugin;
    }

    public function onRun(int $currentTick){
        foreach($this->plugin->getServer()->getOnlinePlayers() as $p){
            $this->plugin->isInPortal($p);
        }
    }

}