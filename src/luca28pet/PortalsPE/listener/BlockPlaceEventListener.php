<?php
declare(strict_types=1);

namespace luca28pet\PortalsPE\listener;

use pocketmine\event\block\BlockPlaceEvent;

class BlockPlaceEventListener extends BaseListener{

    public function onBlockPlace(BlockPlaceEvent $event) : void{
        $ses = $this->plugin->getSessionManager()->getSession($event->getPlayer());
        if($ses !== null){
            if($ses->isSelectingFirstBlock()){
                $event->setCancelled();
                $ses->getSelection()->setFirstBlockWithFolderName($event->getBlock()->asVector3(), $event->getPlayer()->getLevel()->getFolderName());
                $event->getPlayer()->sendMessage('First pos set');
                $ses->setSelectingFirstBlock(false);
            }elseif($ses->isSelectingSecondBlock()){
                $event->setCancelled();
                $ses->getSelection()->setSecondBlockWithFolderName($event->getBlock()->asVector3(), $event->getPlayer()->getLevel()->getFolderName());
                $event->getPlayer()->sendMessage('Second pos set');
                $ses->setSelectingSecondBlock(false);
            }
        }
    }

}