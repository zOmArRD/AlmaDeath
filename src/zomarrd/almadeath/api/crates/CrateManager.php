<?php
/*
 * Created by PhpStorm.
 *
 * User: zOmArRD
 * Date: 13/3/2022
 *
 * Copyright © 2022 zOmArRD (omar@ghostlymc.live) - All Rights Reserved.
 */
declare(strict_types=1);

namespace zomarrd\almadeath\api\crates;

use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\Config;
use zomarrd\almadeath\AlmaDeath;
use zomarrd\almadeath\utils\Utils;

class CrateManager
{
    /** @var Crate[] */
    public array $crates = [];
    /** @var array */
    public array $place = [];
    private Config $config;

    public function init(): void
    {
        $this->config = new Config(AlmaDeath::getInstance()->getDataFolder() . 'crates.yml', Config::YAML);
        $items = [];

        foreach ($this->config->getAll() as $crate => $data) {
            if (isset($data['items'])) {
                foreach ($data['items'] as $slot => $item) {
                    $items[$crate][$slot] = $this->jsonDeserialize($item);
                }
            }
            $this->addCrate(new Crate($crate, $data['crateFormat'], $data['almasToUnLock'], $items[$crate]));
            $this->getCrate($crate)?->setPosition(new Position(Utils::stringToVector3($data['position'])->getX(), Utils::stringToVector3($data['position'])->getY(), Utils::stringToVector3($data['position'])->getZ(), Server::getInstance()->getDefaultLevel()));
        }
    }

    /**
     * @param array $json
     *
     * @return Item
     */
    public function jsonDeserialize(array $json): Item
    {
        $item = ItemFactory::get($json['id'], $json['meta']);
        $item->setCount($json['count']);

        if ($json['customName'] !== '') {
            $item->setCustomName($json['customName']);
        }

        # Edit lore
        $lore = [];

        foreach ($json['lore'] as $string) {
            $lore[] = $string;
        }

        $item->setLore($lore);

        if (isset($json['enchantments'])) {
            foreach ($json['enchantments'] as $key => $data) {
                /** @var ?Enchantment $enchantment */
                $enchantment = Enchantment::getEnchantmentByName($data['name']);

                if ($enchantment !== null) {
                    $item->addEnchantment(new EnchantmentInstance($enchantment, $data['level']));
                }
            }
        }

        return $item;
    }

    public function addCrate(Crate $crate): void
    {
        $this->crates[strtolower($crate->getName())] = $crate;
    }

    public function getCrate(string $crate): ?Crate
    {
        if (!$this->existCrate($crate)) {
            return null;
        }

        return $this->crates[strtolower($crate)];
    }

    public function existCrate(?string $name): bool
    {
        return isset($this->crates[strtolower($name)]);
    }

    public function save(): void
    {
        $config = $this->getConfig();
        $data = [];
        $items = [];

        foreach ($this->getCrates() as $crate) {
            $crate_name = $crate->getName();
            foreach ($crate->getItems() as $slot => $item) {
                $items[$crate_name][$slot] = $this->jsonSerialize($item);
            }

            $data[$crate_name] = ['almasToUnLock' => $crate->getAlmasToUnLock(), 'crateFormat' => $crate->getCrateFormat(), 'position' => Utils::vector3ToString($crate->getPosition()), 'items' => $items[$crate_name]];
        }

        $config->setAll($data);
        $config->save();
    }

    public function getConfig(): Config
    {
        return $this->config;
    }

    public function getCrates(): array
    {
        return $this->crates;
    }

    public function jsonSerialize(Item $item): array
    {
        $data = [];

        $data['id'] = $item->getId();
        $data['meta'] = $item->getDamage();
        $data['count'] = $item->getCount();
        $data['name'] = $item->getVanillaName();
        $data['customName'] = $item->getCustomName();
        $data['lore'] = $item->getLore();

        foreach ($item->getEnchantments() as $enchantment) {
            $name = $enchantment->getType()->getName();
            $data['enchantments'][] = ['name' => $name, 'level' => $enchantment->getLevel()];
        }

        return $data;
    }

    public function getPlace(Player $player): Crate
    {
        return $this->place[$player->getName()];
    }

    public function setPlace(Player $player, Crate $crate): void
    {
        $this->place[$player->getName()] = $crate;
    }

    public function removePlace(Player $player): void
    {
        if (!$this->isPlace($player)) {
            return;
        }

        unset($this->place[$player->getName()]);
    }

    public function isPlace(Player $player): bool
    {
        return isset($this->place[$player->getName()]);
    }

    public function getCrateByPosition(Position $position): ?Crate
    {
        foreach ($this->getCrates() as $crate) {
            if ($crate->getPosition()->equals($position)) {
                return $crate;
            }
        }
        return null;
    }
}