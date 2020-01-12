<?php
declare(strict_types=1);

namespace luca28pet\PortalsPE\listener;

use luca28pet\PortalsPE\Main;
use pocketmine\event\Listener;

class BaseListener implements Listener{

    /** @var Main */
    protected $plugin;

    public function __construct(Main $plugin){
        $this->plugin = $plugin;
    }

}