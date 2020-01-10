<?php

namespace luca28pet\PortalsPE\selection;

use pocketmine\level\Position;
use pocketmine\math\Vector3;
use function max;
use function min;

class CompletePortalSelection{

    /** @var Vector3 */
    protected $firstBlock;
    /** @var Vector3 */
    protected $secondBlock;
    /** @var string */
    private $selectionFolderName;

    public function __construct(Vector3 $firstBlock, Vector3 $secondBlock, string $selectionFolderName){
        $this->firstBlock = $firstBlock;
        $this->secondBlock = $secondBlock;
        $this->selectionFolderName = $selectionFolderName;
    }

    public function getFirstBlock() : Vector3{
        return $this->firstBlock;
    }

    public function setFirstBlock(Vector3 $firstBlock) : void{
        $this->firstBlock = $firstBlock;
    }

    public function getSecondBlock() : Vector3{
        return $this->secondBlock;
    }

    public function setSecondBlock(Vector3 $secondBlock) : void{
        $this->secondBlock = $secondBlock;
    }

    public function getSelectionFolderName() : string{
        return $this->selectionFolderName;
    }

    public function setSelectionFolderName(string $selectionFolderName) : void{
        $this->selectionFolderName = $selectionFolderName;
    }

    public function isInside(Position $position) : bool{
        $position = Position::fromObject($position->floor(), $position->getLevel());
        /** @noinspection NullPointerExceptionInspection */
        return $position->isValid() && $position->getLevel()->getFolderName() === $this->selectionFolderName &&
            $position->x >= min($this->firstBlock->x, $this->secondBlock->x) && $position->x <= max($this->firstBlock->x, $this->secondBlock->x) &&
            $position->y >= min($this->firstBlock->y, $this->secondBlock->y) && $position->y <= max($this->firstBlock->y, $this->secondBlock->y) &&
            $position->z >= min($this->firstBlock->z, $this->secondBlock->z) && $position->z <= max($this->firstBlock->z, $this->secondBlock->z);
    }

    public function toArray() : array{
        return [
            'firstBlock' => ['x' => $this->firstBlock->x, 'y' => $this->firstBlock->y, 'z' => $this->firstBlock->z],
            'secondBlock' => ['x' => $this->secondBlock->x, 'y' => $this->secondBlock->y, 'z' => $this->secondBlock->z],
            'folderName' => $this->selectionFolderName
        ];
    }

    public static function fromArray(array $array) : CompletePortalSelection{
        return new CompletePortalSelection(
            new Vector3($array['firstBlock']['x'], $array['firstBlock']['y'], $array['firstBlock']['z']),
            new Vector3($array['secondBlock']['x'], $array['secondBlock']['y'], $array['secondBlock']['z']),
            $array['folderName']
        );
    }

}