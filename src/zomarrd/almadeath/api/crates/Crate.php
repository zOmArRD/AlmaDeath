<?php
/*
 * Created by PhpStorm.
 *
 * User: zOmArRD
 * Date: 12/3/2022
 *
 * Copyright © 2022 zOmArRD (omar@ghostlymc.live) - All Rights Reserved.
 */

namespace zomarrd\almadeath\api\crates;

use BlockHorizons\Fireworks\entity\FireworksRocket;
use BlockHorizons\Fireworks\item\Fireworks;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\Player;
use pocketmine\Server;
use zomarrd\almadeath\AlmaDeath;
use zomarrd\almadeath\api\entity\FloatingText;

class Crate
{
    public Position $position;

    public function __construct(private string $name, private ?string $crateFormat = null, private int $almasToUnLock = 1, private array $items = [])
    {
        if ($this->crateFormat === null) {
            $this->crateFormat = $this->getName();
        }

        $this->setPosition(new Position(0, 0, 0, Server::getInstance()->getDefaultLevel()));
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setCrateFormat(string $crateFormat): void
    {
        $this->crateFormat = $crateFormat;
    }

    public function setItems(array $items): void
    {
        $this->items = $items;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setAlmasToUnLock(int $almasToUnLock): void
    {
        $this->almasToUnLock = $almasToUnLock;
    }

    public function addFloatingText(): void
    {
        $nbt = new CompoundTag("", [new ListTag("Pos", [new DoubleTag("", $this->getPosition()->getX() + 0.50), new DoubleTag("", $this->getPosition()->getY() + 0.85), new DoubleTag("", $this->getPosition()->getZ() + 0.50)]), new ListTag("Motion", [new DoubleTag("", 0), new DoubleTag("", 0), new DoubleTag("", 0)]), new ListTag("Rotation", [new FloatTag("", 0.0), new FloatTag("", 0.0)])]);

        $nbt->setString("TextId", $this->getName());
        $entity = new FloatingText($this->getPosition()->getLevel(), $nbt);
        $format = str_replace(["{line}", "{almas}"], ["\n", $this->getAlmasToUnLock()], $this->getCrateFormat());
        $entity->setNameTag($format);
        $entity->spawnToAll();
    }

    public function getPosition(): Position
    {
        return $this->position;
    }

    public function setPosition(Position $position): void
    {
        $this->position = $position;
    }

    public function getAlmasToUnLock(): int
    {
        return $this->almasToUnLock;
    }

    public function getCrateFormat(): string
    {
        return $this->crateFormat;
    }

    public function editFloatingText(string $text): void
    {
        foreach ($this->getPosition()->getLevel()?->getEntities() as $entity) {
            if (($entity instanceof FloatingText) && $entity->getTextId() === $this->getName()) {
                $entity->setNameTag($text);
            }
        }
    }

    public function removeFloatingText(): void
    {
        foreach ($this->getPosition()->getLevel()?->getEntities() as $entity) {
            if (($entity instanceof FloatingText) && $entity->getTextId() === $this->getName()) {
                $entity->kill();
            }
        }
    }

    public function giveRewards(Player $player): void
    {
        /** @var Item $item */
        $item = $this->getItems()[array_rand($this->getItems())];

        if ($item !== null) {
            $player->getInventory()->addItem($item);
            AlmaDeath::getAlmasManager()->removeSoul($player->getName(), $this->getAlmasToUnLock());
            $fw = ItemFactory::get(ItemIds::FIREWORKS);
            if ($fw instanceof Fireworks) {
                $fw->addExplosion(Fireworks::TYPE_SMALL_SPHERE, Fireworks::COLOR_RED, "", true, true);
                $fw->setFlightDuration(1);
                $nbt = FireworksRocket::createBaseNBT($this->getPosition()->add(0.5, 1, 0.5), new Vector3(0.001, 0.05, 0.001), lcg_value() * 360, 90);
                $entity = FireworksRocket::createEntity("FireworksRocket", $player->getLevel(), $nbt, $fw);
                if ($entity instanceof FireworksRocket) {
                    $entity->spawnTo($player);
                }
            }
        }
    }

    public function getItems(): array
    {
        return $this->items;
    }
}