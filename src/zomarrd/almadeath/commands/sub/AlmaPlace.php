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
use zomarrd\almadeath\AlmaDeath;
use zomarrd\almadeath\permission\PermissionKey;

final class AlmaPlace extends BaseSubCommand
{

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player) {
            return;
        }

        $player = $sender;

        if (!$player->hasPermission(PermissionKey::ALMA_COMMAND_CREATE)) {
            return;
        }

        $manager = AlmaDeath::getCrateManager();

        if (!$manager->existCrate($args['crate'])) {
            $player->sendMessage(PREFIX . '§cCrate does not exist!');
            return;
        }

        if ($manager->isPlace($player)) {
            return;
        }

        $player->sendMessage(PREFIX . '§eYou must break a block which will be the crate.');
        $manager->setPlace($player, $manager->getCrate($args['crate']));
    }

    /**
     * @throws ArgumentOrderException
     */
    protected function prepare(): void
    {
        $this->registerArgument(0, new RawStringArgument('crate', false));
    }
}