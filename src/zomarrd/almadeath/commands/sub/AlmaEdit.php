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
use jojoe77777\FormAPI\CustomForm;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use zomarrd\almadeath\AlmaDeath;
use zomarrd\almadeath\permission\PermissionKey;

final class AlmaEdit extends BaseSubCommand
{

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player) {
            return;
        }

        if (!$sender->hasPermission(PermissionKey::ALMA_COMMAND_EDIT)) {
            return;
        }
        $manager = AlmaDeath::getCrateManager();
        $crateName = $args['crate'];
        $player = $sender;

        if (!$manager->existCrate($crateName)) {
            $player->sendMessage(PREFIX . '§cCrate does not exist!');
            return;
        }

        $crate = $manager->getCrate($crateName);

        $form = new CustomForm(function (Player $player, $data) use ($crate): void {
            if (!isset($data)) {
                return;
            }

            if ($data['almas-to-unlock'] !== null) {
                $crate->setAlmasToUnLock((int)$data["almas-to-unlock"]);
                $crate->removeFloatingText();
                $crate->addFloatingText();
            }

            if ($data['floating-text'] !== null) {
                $crate->editFloatingText($data["floating-text"]);
            }

        });
        $form->setTitle("Edit $crateName crate");
        $form->addInput('FloatingText', 'new floating text', null, 'floating-text');
        $form->addInput('Almas para desbloquear', '5', null, 'almas-to-unlock');
        $player->sendForm($form);
    }

    /**
     * @throws ArgumentOrderException
     */
    protected function prepare(): void
    {
        $this->registerArgument(0, new RawStringArgument('crate', false));
    }
}