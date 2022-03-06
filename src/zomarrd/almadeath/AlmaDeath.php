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

use Exception;
use muqsit\invmenu\InvMenuHandler;
use pocketmine\item\ItemFactory;
use pocketmine\plugin\PluginBase;
use pocketmine\plugin\PluginLogger;
use zomarrd\almadeath\api\AlmasManager;
use zomarrd\almadeath\api\item\AlmaItem;
use zomarrd\almadeath\commands\AlmaDeathCommand;
use zomarrd\almadeath\config\ConfigManager;

final class AlmaDeath extends PluginBase
{
    public static AlmaDeath $instance;
    public static PluginLogger $logger;
    public static AlmasManager $almasManager;

    public static function getInstance(): AlmaDeath
    {
        return self::$instance;
    }

    public function onLoad(): void
    {
        self::$instance = $this;
        self::$logger = $this->getLogger();
        self::$almasManager = new AlmasManager();

        new ConfigManager();
        ItemFactory::registerItem(new AlmaItem(), true);
    }

    public function onEnable(): void
    {
        try {
            InvMenuHandler::register(self::$instance);
        } catch (Exception $exception) {
            self::$logger->info($exception->getMessage());
        }

        self::getAlmasManager()->init();
        $this->getServer()->getCommandMap()->register('zomarrd', new AlmaDeathCommand(self::$instance));
    }

    public static function getAlmasManager(): AlmasManager
    {
        return self::$almasManager;
    }

    public function onDisable(): void
    {

        /*TODO: ???*/
        sleep(5);
    }

    public function getConfigManager(): ConfigManager
    {
        return ConfigManager::getInstance();
    }
}