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

use muqsit\invmenu\InvMenuHandler;
use pocketmine\plugin\PluginBase;
use pocketmine\plugin\PluginLogger;
use zomarrd\almadeath\commands\AlmaDeathCommand;

final class AlmaDeath extends PluginBase
{
    public static AlmaDeath $instance;
    public static PluginLogger $logger;

    public static function getInstance(): AlmaDeath
    {
        return self::$instance;
    }

    public function onLoad(): void
    {
        self::$instance = $this;
        self::$logger = $this->getLogger();
    }

    public function onEnable(): void
    {
        try {
            InvMenuHandler::register(self::$instance);
        } catch (\Exception $exception) {
            self::$logger->info($exception->getMessage());
        }

        $this->getServer()->getCommandMap()->register('zomarrd', new AlmaDeathCommand(self::$instance));
    }

    public function onDisable(): void
    {

        /*TODO: ???*/
        sleep(5);
    }
}