<?php
/*
 * Created by PhpStorm.
 *
 * User: zOmArRD
 * Date: 4/3/2022
 *
 * Copyright © 2022 zOmArRD (omar@ghostlymc.live) - All Rights Reserved.
 */
declare(strict_types=1);

namespace zomarrd\almadeath;

use BlockHorizons\Fireworks\entity\FireworksRocket;
use BlockHorizons\Fireworks\item\Fireworks;
use Exception;
use muqsit\invmenu\InvMenuHandler;
use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\plugin\PluginBase;
use pocketmine\plugin\PluginLogger;
use zomarrd\almadeath\api\AlmasManager;
use zomarrd\almadeath\api\crates\CrateManager;
use zomarrd\almadeath\api\entity\FloatingText;
use zomarrd\almadeath\api\events\AlmasListener;
use zomarrd\almadeath\api\events\CrateListener;
use zomarrd\almadeath\api\item\AlmaItem;
use zomarrd\almadeath\commands\AlmaDeathCommand;
use zomarrd\almadeath\config\ConfigManager;

final class AlmaDeath extends PluginBase
{
    public static AlmaDeath $instance;
    public static PluginLogger $logger;
    public static AlmasManager $almasManager;
    public static CrateManager $crateManager;

    public static function getInstance(): AlmaDeath
    {
        return self::$instance;
    }

    public function onLoad(): void
    {
        self::$instance = $this;
        self::$logger = $this->getLogger();
        self::$almasManager = new AlmasManager();
        self::$crateManager = new CrateManager();

        new ConfigManager();
        Entity::registerEntity(FloatingText::class, true);
        ItemFactory::registerItem(new AlmaItem(), true);
    }

    public function onEnable(): void
    {
        try {
            InvMenuHandler::register(self::$instance);
        } catch (Exception $exception) {
            self::$logger->info($exception->getMessage());
        }

        ItemFactory::registerItem(new Fireworks(), true);
        Item::initCreativeItems(); //will load firework rockets from pocketmine's resources folder
        if (!Entity::registerEntity(FireworksRocket::class, false, ["FireworksRocket", "minecraft:fireworks_rocket"])) {
            $this->getLogger()->error("Failed to register FireworksRocket entity with savename 'FireworksRocket'");
        }

        self::getAlmasManager()->init();
        self::getCrateManager()->init();

        $this->getServer()->getPluginManager()->registerEvents(new CrateListener(), $this);
        $this->getServer()->getPluginManager()->registerEvents(new AlmasListener(), $this);
        $this->getServer()->getCommandMap()->register('zomarrd', new AlmaDeathCommand(self::$instance));
    }

    public static function getAlmasManager(): AlmasManager
    {
        return self::$almasManager;
    }

    public static function getCrateManager(): CrateManager
    {
        return self::$crateManager;
    }

    public function onDisable(): void
    {
        self::getCrateManager()->save();
        sleep(1);
    }

    public function getConfigManager(): ConfigManager
    {
        return ConfigManager::getInstance();
    }
}