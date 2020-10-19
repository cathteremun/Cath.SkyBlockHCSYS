<?php

namespace SkyBlockHCSYS;

use DateTime;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\item\Item;
use pocketmine\utils\Config;

class EventListener implements Listener {

    public function onPlayerPreLogin(PlayerPreLoginEvent $event) {
        $c = new Config(Main::DIR_NETWORK . "sbhcsys.yml", Config::YAML);

        $player = strtolower($event->getPlayer()->getName());
        $date = new DateTime("yesterday");
        $date = explode(".", $date->format("d.m.Y"));

        if(!$c->exists($player)){
            $c->set($player, null);
            $c->setNested($player.".temperature", 30);
            $c->setNested($player.".overheat", 0);
            $c->setNested($player.".freeze", 0);
            $c->setNested($player.".lastOnline", $date);
            $c->save();
        }
    }

    public function onPlayerJoin(PlayerJoinEvent $event) {
        $c = new Config(Main::DIR_NETWORK . "sbhcsys.yml", Config::YAML);

        $player = strtolower($event->getPlayer()->getName());
        $date = new DateTime("today");
        $date = explode(".", $date->format("" . "d.m.Y"));

        if($c->getNested($player.".lastOnline") != $date) {
            $c->setNested($player.".temperature", 30);
            $c->setNested($player.".overheat", 0);
            $c->setNested($player.".freeze", 0);
        }

        $c->setNested($player.".lastOnline", $date);
        $c->save();
    }

    /*public function onPlayerEat(PlayerItemConsumeEvent $event) {
        $item = $event->getItem()->getId();
        if($item === 260) {
            Utils::setPlayerData($event->getPlayer(), -5, 1);
        }
        if($item === 391 or $item === 457 or $item === 297) {
            Utils::setPlayerData($event->getPlayer(), -3, 1);
        }

        if($item === 459 or $item === 282) {
            Utils::setPlayerData($event->getPlayer(), -10, -1);
        }
        if($item === 366 or $item === 320 or $item === 373) {
            Utils::setPlayerData($event->getPlayer(), -5, -1);
        }
        if($item === 393) {
            Utils::setPlayerData($event->getPlayer(), -7, -1);
        }
    }*/
}