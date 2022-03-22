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
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\item\Item;
use pocketmine\Player;
use zomarrd\almadeath\AlmaDeath;
use zomarrd\almadeath\config\ConfigManager;

final class AlmasListener implements Listener
{
    private array $kills = [];

    public function PlayerJoinEvent(PlayerJoinEvent $event): void
    {
        $playerName = $event->getPlayer()->getName();
        try {
            if ($this->exist([$playerName, $playerName])) {
                return;
            }
        } catch (Exception) {
            $this->kills[$playerName][] = [];
        }
    }

    public function EntityDeathEvent(EntityDeathEvent $event): void
    {
        $cause = $event->getEntity()->getLastDamageCause();
        $player = $event->getEntity();

        if (($player instanceof Player) && $cause instanceof EntityDamageByEntityEvent) {
            $damager = $cause->getDamager();
            if (!$damager instanceof Player) {
                return;
            }

            if ($this->exist([$damager->getName(), $player->getName()])) {
                return;
            }

            $this->kills[$damager->getName()][] = $player->getName();

            $item = Item::get((int)AlmaDeath::$itemID[0], (int)AlmaDeath::$itemID[1]);
            $item->setCustomName(ConfigManager::getPluginConfig()->get('AlmaHead')['name']);
            $item->setLore([ConfigManager::getPluginConfig()->get('AlmaHead', "")['lore']]);
            $item->getNamedTag()->setString('alma', 'alma');
            $damager->getInventory()->addItem($item);
        }
    }

    public function exist(array $data): bool
    {
        foreach ($this->kills[$data[0]] as $kill) {
            if ($kill === $data[1]) {
                return true;
            }
        }
        return false;
    }

    public function PlayerInteractEvent(PlayerInteractEvent $event): void
    {
        $item = $event->getItem();
        $player = $event->getPlayer();
        $tag = $item->getNamedTag()->getString('alma', '');

        if ($tag === 'alma') {
            $item->setCount($item->getCount() - 1);
            $player->getInventory()->setItemInHand($item);

            AlmaDeath::getAlmasManager()->addSoul($player->getName());
            $player->sendMessage(PREFIX . "§ase ha agregado un alma a tu cuenta!");
        }
    }
}