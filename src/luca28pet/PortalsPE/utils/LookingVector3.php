<?php

namespace luca28pet\PortalsPE\utils;

use pocketmine\math\Vector3;

class LookingVector3 extends Vector3{

    /** @var float */
    public $yaw;
    /** @var float */
    public $pitch;

    /**
     * @param int|float   $x
     * @param int|float   $y
     * @param int|float   $z
     * @param float $yaw
     * @param float $pitch
     */
    public function __construct($x = 0, $y = 0, $z = 0, float $yaw = 0.0, float $pitch = 0.0){
        $this->yaw = $yaw;
        $this->pitch = $pitch;
        parent::__construct($x, $y, $z);
    }

    /**
     * @param Vector3    $pos
     * @param float      $yaw   default 0.0
     * @param float      $pitch default 0.0
     *
     * @return LookingVector3
     */
    public static function fromObject(Vector3 $pos, float $yaw = 0.0, float $pitch = 0.0) : LookingVector3{
        return new LookingVector3($pos->x, $pos->y, $pos->z, $yaw, $pitch);
    }

    /**
     * Return a LookingVector3 instance
     *
     * @return LookingVector3
     */
    public function asLookingVector3() : LookingVector3{
        return new LookingVector3($this->x, $this->y, $this->z, $this->yaw, $this->pitch);
    }

    public function getYaw() : float{
        return $this->yaw;
    }

    public function getPitch() : float{
        return $this->pitch;
    }

    public function __toString() : string{
        return "LookingVector3 (x=$this->x, y=$this->y, z=$this->z, yaw=$this->yaw, pitch=$this->pitch)";
    }

    public function equals(Vector3 $v) : bool{
        if($v instanceof LookingVector3){
            return parent::equals($v) and $v->getYaw() === $this->getYaw() and $v->getPitch() === $this->getPitch();
        }
        return parent::equals($v);
    }

}