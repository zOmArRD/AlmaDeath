<?php
/*
 * Created by PhpStorm.
 *
 * User: zOmArRD
 * Date: 9/3/2022
 *
 * Copyright © 2022 zOmArRD (omar@ghostlymc.live) - All Rights Reserved.
 */
declare(strict_types=1);

namespace zomarrd\almadeath\api\entity;

use pocketmine\entity\Entity;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;

final class FloatingText extends Entity
{
    public const NETWORK_ID = self::BAT;
    public $canCollide = false;
    public $keepMovement = true;
    public $width = 0.001;
    public $height = 0.001;
    public $eyeHeight = 0.001;
    protected $gravity = 0.0;
    protected $drag = 0.0;
    protected ?string $textId;

    public function __construct(Level $level, CompoundTag $nbt)
    {
        parent::__construct($level, $nbt);
        $this->setScale(0.00001);
        $this->setNameTagAlwaysVisible(true);
        $this->setNameTagVisible(true);
    }

    public function canBeMovedByCurrents(): bool
    {
        return false;
    }

    public function getTextId(): ?string
    {
        return $this->textId;
    }

    public function setTextId(string $textId): void
    {
        $this->textId = $textId;
    }

    public function saveNBT(): void
    {
        $this->namedtag->setString("id", $this->getSaveId(), true);

        if ($this->getNameTag() !== "") {
            $this->namedtag->setString("CustomName", $this->getNameTag());
            $this->namedtag->setByte("CustomNameVisible", $this->isNameTagVisible() ? 1 : 0);
        } else {
            $this->namedtag->removeTag("CustomName", "CustomNameVisible");
        }

        $this->namedtag->setTag(new ListTag("Pos", [new DoubleTag("", $this->x), new DoubleTag("", $this->y), new DoubleTag("", $this->z)]));
        $this->namedtag->setTag(new ListTag("Motion", [new DoubleTag("", $this->motion->x), new DoubleTag("", $this->motion->y), new DoubleTag("", $this->motion->z)]));
        $this->namedtag->setTag(new ListTag("Rotation", [new FloatTag("", $this->yaw), new FloatTag("", $this->pitch)]));

        $this->namedtag->setFloat("FallDistance", $this->fallDistance);
        $this->namedtag->setShort("Fire", $this->fireTicks);
        $this->namedtag->setShort("Air", $this->propertyManager->getShort(self::DATA_AIR));
        $this->namedtag->setByte("OnGround", $this->onGround ? 1 : 0);
        $this->namedtag->setByte("Invulnerable", 1);
        $this->namedtag->setString("TextId", $this->textId);
    }

    protected function initEntity(): void
    {
        parent::initEntity();
        $this->textId = $this->namedtag->getString("TextId");
    }
}