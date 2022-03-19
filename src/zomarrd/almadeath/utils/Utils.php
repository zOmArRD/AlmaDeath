<?php

namespace zomarrd\almadeath\utils;

use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\Server;

final class Utils
{

    public static function centerText(string $text, int $length): string
    {
        $textLength = strlen($text);
        $spaceForSides = ($length - $textLength) % 2;
        return str_repeat(' ', $spaceForSides) . $text . str_repeat(' ', $spaceForSides);
    }

    public static function vector3ToString(Vector3 $vector3): string
    {
        return $vector3->getFloorX() . ':' . $vector3->getFloorY() . ':' . $vector3->getFloorZ();
    }

    public static function stringToVector3(string $string): Vector3
    {
        $args = explode(':', $string);
        return new Vector3((int)$args[0], (int)$args[1], (int)$args[2]);
    }

    public static function isOnline(string $player): bool
    {
        return Server::getInstance()->getPlayerExact($player) instanceof Player;
    }

    public static function getPlayer(string $player): ?Player
    {
        $instance = Server::getInstance()->getPlayerExact($player);

        if (!$instance instanceof Player) {
            return null;
        }

        return $instance;
    }
}