<?php
/*
 * Created by PhpStorm.
 *
 * User: zOmArRD
 * Date: 5/3/2022
 *
 * Copyright © 2022 zOmArRD (omar@ghostlymc.live) - All Rights Reserved.
 */
declare(strict_types=1);

namespace zomarrd\almadeath\config;

use pocketmine\utils\Config;
use RuntimeException;
use zomarrd\almadeath\AlmaDeath;

class ConfigManager
{
    public static ConfigManager $instance;
    private static Config $pluginConfig;

    public function __construct()
    {
        self::$instance = $this;
        $this->init();
    }

    public function init(): void
    {
        /** This can be erased? */
        if (!@mkdir($concurrentDirectory = $this->getDataFolder()) && !is_dir($concurrentDirectory)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }

        $this->saveResource('config.yml');
        $this->saveResource('players_almas.json');

        self::$pluginConfig = $this->getFile('config.yml');
        define('PREFIX', self::$pluginConfig->get('prefix', '§cSoul §7>> §r'));
    }

    public function getDataFolder(): string
    {
        return AlmaDeath::getInstance()->getDataFolder();
    }

    public function saveResource(string $file, bool $replace = false): void
    {
        AlmaDeath::getInstance()->saveResource($file, $replace);
    }

    public function getFile(string $file, int $type = Config::DETECT, array $default = [], &$correct = null): Config
    {
        return new Config($this->getDataFolder() . $file, $type, $default, $correct);
    }

    public static function getInstance(): ConfigManager
    {
        return self::$instance;
    }

    public static function getPluginConfig(): Config
    {
        return self::$pluginConfig;
    }
}