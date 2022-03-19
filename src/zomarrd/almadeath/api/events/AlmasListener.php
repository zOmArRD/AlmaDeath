<?php
/*
 * Created by PhpStorm.
 *
 * User: zOmArRD
 * Date: 16/3/2022
 *
 * Copyright © 2022 zOmArRD (omar@ghostlymc.live) - All Rights Reserved.
 */
declare(strict_types=1);

namespace zomarrd\almadeath\api\events;

use Exception;
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\event\Listener;
use pocketmine\Player;
use zomarrd\almadeath\AlmaDeath;

final class AlmasListener implements Listener
{
    private array $kills = [];

    public function EntityDeathEvent(EntityDeathEvent $event): void
    {
        $cause = $event->getEntity()->getLastDamageCause();
        $player = $event->getEntity();

        if ($player instanceof Player) {
            try {
                if ($this->exist([$player->getName(), $player->getName()])) {
                    return;
                }
            } catch (Exception) {
                return;
            }

            $this->kills[$player->getName()][] = $player->getName();
            AlmaDeath::getAlmasManager()->addSoul($player->getName());
            $player->sendMessage(PREFIX . "§ase ha agregado un alma a tu cuenta!");
        }

        /*if (($player instanceof Player) && $cause instanceof EntityDamageByEntityEvent) {
            $damager = $cause->getDamager();
            if (!$damager instanceof Player) {
                return;
            }

            if ($this->exist([$player->getName(), $player->getName()])) {
                return;
            }

            $this->kills[$damager->getName()][] = $player->getName();
            AlmaDeath::getAlmasManager()->addSoul($damager->getName());
            $player->sendMessage(PREFIX . "§ase ha agregado un alma a tu cuenta!");
        }*/
    }

    public function exist(array $data): bool
    {
        foreach ($this->kills[$data[0]] as $kill) {
            if ($kill === $data[1]) {
                return true;
            }
        }
        return false;
    }
}