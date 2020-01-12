<?php

namespace luca28pet\PortalsPE\utils;

use BadMethodCallException;
use InvalidArgumentException;
use ReflectionClass;

/**
 * @method static PortalResponse SUCCESS_TP()
 * @method static PortalResponse SUCCESS_NO_TP()
 * @method static PortalResponse NO_PERM()
 * @method static PortalResponse WORLD_NOT_LOADED()
 */
class PortalResponse{

    private static $objects = [];

    public const SUCCESS_TP = 0;
    public const SUCCESS_NO_TP = 1;
    public const NO_PERM = 2;
    public const WORLD_NOT_LOADED = 3;

    /** @var int */
    private $result;

    public static function init() : void{
        $ref = new ReflectionClass(__CLASS__);
        foreach($ref->getConstants() as $c => $v){
            self::addObject($c, $v);
        }
    }

    public static function addObject(string $name, int $value) : void{
        self::$objects[$name] = new self($value);
    }

    public static function __callStatic(string $name, array $arguments) : PortalResponse{
        if(!isset(self::$objects[$name])){
            throw new BadMethodCallException(__CLASS__.' does not have constant '.$name);
        }
        if($arguments !== []){
            throw new InvalidArgumentException('Can\'t call with arguments');
        }
        return self::$objects[$name];
    }

    private function __construct(int $result){
        $this->result = $result;
    }

    public function getResult() : int{
        return $this->result;
    }

}