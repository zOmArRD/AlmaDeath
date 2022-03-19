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

use CortexPE\Commando\BaseSubCommand;
use jojoe77777\FormAPI\CustomForm;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\MenuIds;
use pocketmine\command\CommandSender;
use pocketmine\inventory\Inventory;
use pocketmine\Player;
use zomarrd\almadeath\AlmaDeath;
use zomarrd\almadeath\api\crates\Crate;
use zomarrd\almadeath\permission\PermissionKey;

final class AlmaCreate extends BaseSubCommand
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

        $form = new CustomForm(function (Player $player, array $data) {
            AlmaDeath::getCrateManager()->addCrate(new Crate($data['crate-name'], $data['crate-format'], (int)$data['almas-to-unlock']));
            $chest = InvMenu::create(MenuIds::TYPE_CHEST)->setName($data['crate-name'])->setInventoryCloseListener(function (Player $player, Inventory $inventory) use ($data): void {
                if (count($inventory->getContents()) !== 0) {
                    AlmaDeath::getCrateManager()->getCrate($data['crate-name'])?->setItems($inventory->getContents());
                    $player->sendMessage(PREFIX . "§aHas creado la crate {$data['crate-name']}, utiliza /alma para ver los otros comandos.");
                } else {
                    $player->sendMessage(PREFIX . "§cNo se ha podido crear la crate, debes introducir items dentro de ella.");
                }
            });

            $chest->send($player);
        });

        $form->setTitle('Crate creation form');
        $form->addInput('Name', 'Crate Name', 'AlmaBox', 'crate-name');
        $form->addInput('Almas para desbloquear', '5', '5', 'almas-to-unlock');
        $form->addInput('Crate Format', 'For FloatingText', null, 'crate-format');
        $player->sendForm($form);
    }

    protected function prepare(): void
    {
        // TODO: Implement prepare() method.
    }
}