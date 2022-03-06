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

namespace zomarrd\almadeath\commands\sub;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use zomarrd\almadeath\AlmaDeath;
use zomarrd\almadeath\permission\PermissionKey;

final class AlmaList extends BaseSubCommand
{

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (count($args) !== 0 && $sender->hasPermission(PermissionKey::ALMA_COMMAND_LIST)) {
            $player = strtolower($args["player"]);
            $sender->sendMessage(PREFIX . TextFormat::GREEN . "{$args["player"]} tiene " . AlmaDeath::getAlmasManager()->getSouls($player) . " almas.");
            return;
        }

        if ($sender instanceof Player) {
            $sender->sendMessage(PREFIX . TextFormat::GREEN . "tienes " . AlmaDeath::getAlmasManager()->getSouls(strtolower($sender->getName())) . " almas.");
        }
    }

    /**
     * @throws ArgumentOrderException
     */
    protected function prepare(): void
    {
        $this->registerArgument(0, new RawStringArgument("player", true));
    }
}