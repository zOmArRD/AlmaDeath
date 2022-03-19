<?php
/*
 * Created by PhpStorm.
 *
 * User: zOmArRD
 * Date: 6/3/2022
 *
 * Copyright © 2022 zOmArRD (omar@ghostlymc.live) - All Rights Reserved.
 */
declare(strict_types=1);

namespace zomarrd\almadeath\api\item;

use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\Player;
use zomarrd\almadeath\AlmaDeath;
use zomarrd\almadeath\config\ConfigManager;

final class AlmaItem extends Item
{
    public function __construct()
    {
        $id = explode(":", ConfigManager::getPluginConfig()->get('AlmaHead', "397:3")['itemID']);
        parent::__construct((int)$id[0], (int)$id[1], ConfigManager::getPluginConfig()->get('AlmaHead')['name']);
        $this->setCustomName(ConfigManager::getPluginConfig()->get('AlmaHead')['name']);
        $this->setLore([ConfigManager::getPluginConfig()->get('AlmaHead', "")['lore']]);
    }

    public function getMaxStackSize(): int
    {
        return 120;
    }

    public function onActivate(Player $player, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector): bool
    {
        $item = $player->getInventory()->getItemInHand();

        $item->setCount($item->getCount() - 1);
        $player->getInventory()->setItemInHand($item);

        AlmaDeath::getAlmasManager()->addSoul($player->getName());
        $player->sendMessage(PREFIX . "§ase ha agregado un alma a tu cuenta!");
        return false;
    }
}