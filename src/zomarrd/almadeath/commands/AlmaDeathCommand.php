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

namespace zomarrd\almadeath\commands;

use CortexPE\Commando\BaseCommand;
use pocketmine\command\CommandSender;
use pocketmine\plugin\Plugin;
use zomarrd\almadeath\commands\sub\AlmaCreate;
use zomarrd\almadeath\commands\sub\AlmaEdit;
use zomarrd\almadeath\commands\sub\AlmaGive;
use zomarrd\almadeath\commands\sub\AlmaList;
use zomarrd\almadeath\commands\sub\AlmaSetPay;

final class AlmaDeathCommand extends BaseCommand
{
    public function __construct(Plugin $plugin) { parent::__construct($plugin, 'alma', 'AlmaDeath manager'); }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        $this->sendUsage();
    }

    protected function prepare(): void
    {
        $this->registerSubCommand(new AlmaCreate('create'));
        $this->registerSubCommand(new AlmaEdit('edit'));
        $this->registerSubCommand(new AlmaSetPay('setpay'));
        $this->registerSubCommand(new AlmaGive('give'));
        $this->registerSubCommand(new AlmaList('list'));
    }
}