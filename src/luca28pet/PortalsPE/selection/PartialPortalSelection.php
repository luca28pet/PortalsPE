<?php

namespace luca28pet\PortalsPE\selection;

use pocketmine\math\Vector3;

class PartialPortalSelection{

    /** @var null|Vector3 */
    protected $firstBlock;
    /** @var null|string */
    private $firstBlockFolderName;
    /** @var null|Vector3 */
    protected $secondBlock;
    /** @var null|string */
    private $secondBlockFolderName;

    public function __construct(?Vector3 $firstBlock, ?string $firstBlockFolderName, ?Vector3 $secondBlock, ?string $secondBlockFolderName){
        $this->firstBlock = $firstBlock;
        $this->firstBlockFolderName = $firstBlockFolderName;
        $this->secondBlock = $secondBlock;
        $this->secondBlockFolderName = $secondBlockFolderName;
    }

    public function getFirstBlock() : ?Vector3{
        return $this->firstBlock;
    }

    public function setFirstBlock(?Vector3 $firstBlock) : void{
        $this->firstBlock = $firstBlock;
    }

    public function getFirstBlockFolderName() : ?string{
        return $this->firstBlockFolderName;
    }

    public function setFirstBlockFolderName(?string $firstBlockFolderName) : void{
        $this->firstBlockFolderName = $firstBlockFolderName;
    }

    public function getSecondBlock() : ?Vector3{
        return $this->secondBlock;
    }

    public function setSecondBlock(?Vector3 $secondBlock) : void{
        $this->secondBlock = $secondBlock;
    }

    public function getSecondBlockFolderName() : ?string{
        return $this->secondBlockFolderName;
    }

    public function setSecondBlockFolderName(?string $secondBlockFolderName) : void{
        $this->secondBlockFolderName = $secondBlockFolderName;
    }

    public function isComplete() : bool{
        return $this->firstBlock !== null && $this->secondBlock !== null && $this->firstBlockFolderName !== null && $this->secondBlockFolderName !== null;
    }

    public function isValid() : bool{
        return $this->isComplete() && $this->firstBlockFolderName === $this->secondBlockFolderName;
    }

}