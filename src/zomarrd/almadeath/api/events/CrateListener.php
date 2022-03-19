<?php
/*
 * Created by PhpStorm.
 *
 * User: zOmArRD
 * Date: 16/3/2022
 *
 * Copyright © 2022 zOmArRD (omar@ghostlymc.live) - All Rights Reserved.
 */
declare(strict_types=1);

namespace zomarrd\almadeath\api\events;

use Exception;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\level\Position;
use pocketmine\utils\TextFormat;
use zomarrd\almadeath\AlmaDeath;

final class CrateListener implements Listener
{
    public function handleBreak(BlockBreakEvent $event): void
    {
        $player = $event->getPlayer();
        $block = $event->getBlock();
        $manager = AlmaDeath::getCrateManager();

        if ($manager->isPlace($player)) {
            $crate = $manager->getPlace($player);
            $event->setCancelled();

            $crate->setPosition(new Position($block->getX(), $block->getY(), $block->getZ(), $block->getLevel()));
            $crate->addFloatingText();

            $player->sendMessage(PREFIX . '§eYou have created a crate at this position!');
            $manager->removePlace($player);
            return;
        }

        $crate = $manager->getCrateByPosition(new Position($block->getX(), $block->getY(), $block->getZ(), $block->getLevel()));

        if ($crate !== null) {
            $crate->setPosition(new Position(0, 0, 0, AlmaDeath::getInstance()->getServer()->getDefaultLevel()));
            $player->sendMessage(PREFIX . '§eYou have removed the crate from this position!');
            $crate->removeFloatingText();
        }
    }


    public function handleInteract(PlayerInteractEvent $event): void
    {
        $player = $event->getPlayer();
        $item = $event->getItem();
        $block = $event->getBlock();
        $manager = AlmaDeath::getCrateManager();
        $crate = $manager->getCrateByPosition(new Position($block->getX(), $block->getY(), $block->getZ(), $block->getLevel()));

        if ($crate !== null) {
            $event->setCancelled();
            var_dump($crate->getName());
            $alma_manager = AlmaDeath::getAlmasManager();

            if ($alma_manager->getSouls($player->getName()) >= $crate->getAlmasToUnLock()) {
                try {
                    $crate->giveRewards($player);
                } catch (Exception) {
                }
            } else {
                $player->sendMessage(sprintf("%s%sNecesitas {$crate->getAlmasToUnLock()} almas para abrir esta crate!", PREFIX, TextFormat::GREEN));
            }
        }
    }
}