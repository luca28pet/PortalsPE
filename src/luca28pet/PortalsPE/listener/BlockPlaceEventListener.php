<?php
declare(strict_types=1);

namespace luca28pet\PortalsPE\listener;

use pocketmine\event\block\BlockPlaceEvent;

class BlockPlaceEventListener extends BaseListener{

    public function onBlockPlace(BlockPlaceEvent $event) : void{
        $ses = $this->plugin->getSessionManager()->getSession($event->getPlayer());
        if($ses !== null){
            if($ses->isSelectingFirstBlock()){
                $event->cancel();
                $ses->getSelection()->setFirstBlockWithFolderName($event->getBlock()->getPosition()->asVector3(), $event->getPlayer()->getWorld()->getFolderName());
                $event->getPlayer()->sendMessage('First pos set');
                $ses->setSelectingFirstBlock(false);
            }elseif($ses->isSelectingSecondBlock()){
                $event->cancel();
                $ses->getSelection()->setSecondBlockWithFolderName($event->getBlock()->getPosition()->asVector3(), $event->getPlayer()->getWorld()->getFolderName());
                $event->getPlayer()->sendMessage('Second pos set');
                $ses->setSelectingSecondBlock(false);
            }
        }
    }

}