<?php

namespace PortalsPE;

use pocketmine\scheduler\PluginTask;

class PortalTask extends PluginTask{
    
    private $plugin;
    
    public function __construct(Main $plugin){
        parent::__construct($plugin);
        $this->plugin = $plugin;
    }

    public function onRun(int $currentTick){
        foreach($this->plugin->getServer()->getOnlinePlayers() as $p){
            $this->plugin->isInPortal($p);
        }
    }

}