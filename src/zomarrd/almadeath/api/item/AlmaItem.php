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
use pocketmine\block\BlockIds;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\math\Vector3;
use pocketmine\Player;
use zomarrd\almadeath\AlmaDeath;
use zomarrd\almadeath\api\AlmasManager;
use zomarrd\almadeath\config\ConfigManager;

final class AlmaItem extends Item
{
    public function __construct() {
        $id = explode(":", ConfigManager::getPluginConfig()->get('AlmaHead', "397:3")['itemID']);
        parent::__construct((int)$id[0], (int)$id[1], "§cAlma Head");
        $this->setCustomName("§cAlma Head");
        $this->setLore([ConfigManager::getPluginConfig()->get('AlmaHead', "")['lore']]);
    }

    public function getMaxStackSize(): int
    {
        return 120;
    }

    public function onActivate(Player $player, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector): bool
    {
        var_dump(1);
        $item = $player->getInventory()->getItemInHand();

        if ($item->getCount() === 1) {
            $player->getInventory()->setItemInHand(ItemFactory::get(BlockIds::AIR));
        } else {
            $item->setCount($item->getCount() - 1);
        }

        $this->setCount($this->getCount() - 1);
        AlmaDeath::getAlmasManager()->addSoul($player->getName());
        $player->sendMessage(PREFIX . "§ase ha agregado un alma a tu cuenta!");
        return false;
    }
}