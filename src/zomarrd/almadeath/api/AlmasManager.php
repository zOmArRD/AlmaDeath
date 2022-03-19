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

namespace zomarrd\almadeath\api;

use pocketmine\utils\Config;
use zomarrd\almadeath\config\ConfigManager;

class AlmasManager
{
    private Config $almasData;

    public function init(): void
    {
        $this->almasData = ConfigManager::getInstance()->getFile('players_almas.json', Config::JSON);
    }

    public function addSoul(string $player): void
    {
        $this->getAlmasData()->set(strtolower($player), $this->getSouls($player) + 1);
        $this->getAlmasData()->save();
    }

    public function getAlmasData(): Config
    {
        return $this->almasData;
    }

    public function getSouls(string $player): int
    {
        return $this->getAlmasData()->get(strtolower($player), 0);
    }

    public function removeSoul(string $player, int $quantity = 1): void
    {
        $this->getAlmasData()->set(strtolower($player), $this->getSouls($player) - $quantity);
        $this->getAlmasData()->save();
    }
}