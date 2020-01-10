<?php

namespace luca28pet\PortalsPE\flag;

use function array_search;
use function in_array;

class FlagsManager{

    public const DEFAULTS = [
        'permissionMode' => true,
        'autoload' => true,
        'commands' => [],
    ];

    /** @var array */
    private $set;

    public function __construct(array $data){
        $this->set = [];
        foreach(self::DEFAULTS as $flag => $defaultValue){
            $this->set[$flag] = $data[$flag] ?? $defaultValue;
        }
    }

    public function getPermissionMode() : bool{
        return $this->set['permissionMode'];
    }

    public function setPermissionMode(bool $mode) : void{
        $this->set['permissionMode'] = $mode;
    }

    public function getAutoLoad() : bool{
        return $this->set['autoload'];
    }

    public function setAutoLoad(bool $autoload) : void{
        $this->set['autoload'] = $autoload;
    }

    public function getCommands() : array{
        return $this->set['commands'];
    }

    public function setCommands(array $commands) : void{
        $this->set['commands'] = $commands;
    }

    public function hasCommand(string $cmd) : bool{
        return in_array($cmd, $this->set['commands'], true);
    }

    public function addCommand(string $cmd) : void{
        $this->set['commands'][] = $cmd;
    }

    public function removeCommand(string $cmd) : void{
        $key = array_search($cmd, $this->set['commands'], true);
        if($key !== false){
            unset($this->set['commands'][$key]);
        }
    }

    public function getSet() : array{
        return $this->set;
    }

    public function setSet(array $set) : void{
        $this->set = $set;
    }

}