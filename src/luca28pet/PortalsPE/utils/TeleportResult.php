<?php

namespace luca28pet\PortalsPE\utils;

use InvalidArgumentException;
use ReflectionClass;
use function in_array;

class TeleportResult{

    public const SUCCESS = 0;
    public const NO_PERM = 1;
    public const WORLD_NOT_LOADED = 2;

    /** @var int */
    private $result;

    public function __construct(int $result){
        $ref = new ReflectionClass(__CLASS__);
        if(!in_array($result, $ref->getConstants(), true)){
            throw new InvalidArgumentException('Invalid teleport result '.$result);
        }
        $this->result = $result;
    }

    public function getResult() : int{
        return $this->result;
    }

    public function setResult(int $result) : void{
        $this->result = $result;
    }

}