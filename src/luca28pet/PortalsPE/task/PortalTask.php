<?php


namespace luca28pet\PortalsPE\task;


use luca28pet\PortalsPE\Main;
use pocketmine\scheduler\Task;

class PortalTask extends Task{

    /** @var Main */
    private $plugin;

    public function __construct(Main $plugin){
        $this->plugin = $plugin;
    }

    public function onRun(int $currentTick) : void{
        foreach($this->plugin->getServer()->getOnlinePlayers() as $p){
            $this->plugin->checkPortal($p);
        }
    }

}