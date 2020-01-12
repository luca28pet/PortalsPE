<?php
declare(strict_types=1);

namespace luca28pet\PortalsPE\selection;

use pocketmine\math\Vector3;

/**
 * Objects of this class are created when a player begins a selection for a portal
 */
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

    public function getFirstBlockFolderName() : ?string{
        return $this->firstBlockFolderName;
    }

    public function setFirstBlockWithFolderName(Vector3 $firstBlock, string $firstBlockFolderName) : void{
        $this->firstBlock = $firstBlock;
        $this->firstBlockFolderName = $firstBlockFolderName;
    }

    public function getSecondBlock() : ?Vector3{
        return $this->secondBlock;
    }

    public function getSecondBlockFolderName() : ?string{
        return $this->secondBlockFolderName;
    }

    public function setSecondBlockWithFolderName(?Vector3 $secondBlock, string $secondBlockFolderName) : void{
        $this->secondBlock = $secondBlock;
        $this->secondBlockFolderName = $secondBlockFolderName;
    }

    /**
     * @return bool True if the player has selected both positions, false otherwise
     */
    public function isComplete() : bool{
        return $this->firstBlock !== null && $this->secondBlock !== null && $this->firstBlockFolderName !== null && $this->secondBlockFolderName !== null;
    }

    /**
     * @return bool True if the player has selected both positions and in the same level, false otherwise
     */
    public function isValid() : bool{
        return $this->isComplete() && $this->firstBlockFolderName === $this->secondBlockFolderName;
    }

}