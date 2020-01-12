<?php

namespace luca28pet\PortalsPE\utils;

use BadMethodCallException;
use InvalidArgumentException;
use ReflectionClass;
use function in_array;

/**
 * @method static PortalResponse SUCCESS_TP()
 * @method static PortalResponse SUCCESS_NO_TP()
 * @method static PortalResponse NO_PERM()
 * @method static PortalResponse WORLD_NOT_LOADED()
 */
class PortalResponse{

    private static $responses = [];

    public const SUCCESS_TP = 0;
    public const SUCCESS_NO_TP = 1;
    public const NO_PERM = 2;
    public const WORLD_NOT_LOADED = 3;

    /** @var int */
    private $result;

    public static function init() : void{
        $ref = new ReflectionClass(__CLASS__);
        foreach($ref->getConstants() as $c => $v){
            self::addResponse($c, $v);
        }
    }

    public static function addResponse(string $name, int $value) : void{
        self::$responses[$name] = new PortalResponse($value);
    }

    public static function __callStatic(string $name, array $arguments) : PortalResponse{
        if(!in_array($name, self::$responses, true)){
            throw new BadMethodCallException('Invalid teleport result '.$name);
        }
        if($arguments !== []){
            throw new InvalidArgumentException('Can\'t call with arguments');
        }
        return self::$responses[$name];
    }

    private function __construct(int $result){
        $this->result = $result;
    }

    public function getResult() : int{
        return $this->result;
    }

}