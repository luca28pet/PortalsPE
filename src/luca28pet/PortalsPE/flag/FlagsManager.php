<?php

namespace luca28pet\PortalsPE\flag;

use function array_search;
use function in_array;

class FlagsManager{

    public const DEFAULTS = [
        'teleport' => true,
        'permissionMode' => true,
        'autoload' => true,
        'commands' => [],
    ];

    /** @var array */
    private $flags;

    public function __construct(array $data){
        $this->flags = [];
        foreach($data as $flag => $value){
            $this->flags[$flag] = $value;
        }
        foreach(self::DEFAULTS as $flag => $defaultValue){
            if(!isset($this->flags[$flag])){
                $this->flags[$flag] = $defaultValue;
            }
        }
    }

    public function getTeleport() : bool{
        return $this->flags['teleport'];
    }

    public function setTeleport(bool $tp) : void{
        $this->flags['teleport'] = $tp;
    }

    public function getPermissionMode() : bool{
        return $this->flags['permissionMode'];
    }

    public function setPermissionMode(bool $mode) : void{
        $this->flags['permissionMode'] = $mode;
    }

    public function getAutoLoad() : bool{
        return $this->flags['autoload'];
    }

    public function setAutoLoad(bool $autoload) : void{
        $this->flags['autoload'] = $autoload;
    }

    public function getCommands() : array{
        return $this->flags['commands'];
    }

    public function setCommands(array $commands) : void{
        $this->flags['commands'] = $commands;
    }

    public function hasCommand(string $cmd) : bool{
        return in_array($cmd, $this->flags['commands'], true);
    }

    public function addCommand(string $cmd) : void{
        $this->flags['commands'][] = $cmd;
    }

    public function removeCommand(string $cmd) : void{
        $key = array_search($cmd, $this->flags['commands'], true);
        if($key !== false){
            unset($this->flags['commands'][$key]);
        }
    }

    public function getFlags() : array{
        return $this->flags;
    }

    public function setFlags(array $flags) : void{
        $this->flags = $flags;
    }

}