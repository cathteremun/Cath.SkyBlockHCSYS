<?php

namespace Gradius;

use DateTime;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\utils\Config;

class EventListener implements Listener {

    public function onPlayerPreLogin(PlayerPreLoginEvent $event) {
        $c = new Config(Main::DIR_NETWORK . "sb_temp.yml", Config::YAML);

        $player = strtolower($event->getPlayer()->getName());
        $date = new DateTime("yesterday");
        $date = explode(".", $date->format("d.m.Y"));

        if(!$c->exists($player) || $c->getNested($player.".new") != false){
            $c->set($player, null);
            $c->setNested($player.".new", false);
            $c->setNested($player.".temperatur", 30);
            $c->setNested($player.".overheat", 0);
            $c->setNested($player.".freeze", 0);
            $c->setNested($player.".lastOnline", $date);
            $c->save();
        }
    }

    public function onPlayerJoin(PlayerJoinEvent $event) {
        $c = new Config(Main::DIR_NETWORK . "sb_temp.yml", Config::YAML);

        $player = strtolower($event->getPlayer()->getName());
        $date = new DateTime("today");
        $date = explode(".", $date->format("d.m.Y"));

        if($c->getNested($player.".lastOnline") != $date) {
            $c->setNested($player.".overheat", 0);
            $c->setNested($player.".freeze", 0);
        }

        $c->setNested($player.".lastOnline", $date);
        $c->save();
    }

}