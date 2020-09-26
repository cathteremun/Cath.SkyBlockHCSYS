<?php

namespace Gradius;

use pocketmine\level\Level;
use pocketmine\Player;
use pocketmine\utils\Config;
use pocketmine\utils\Random;

class ChangeTemp {

    public static function change() {
        $random = new Random();
        $config = new Config(Main::DIR_PATH . "config.yml");

        $config->setNested("default.current",           $random->nextRange(7, 31));
        $config->setNested("cold.current",              $random->nextRange(-10, 5));
        $config->setNested("hot.current",               $random->nextRange(33, 50));
        $config->setNested("murder-snow.current",       $random->nextRange(-50, 5));
        $config->setNested("nether.current",            $random->nextRange(49, 80));
        $config->setNested("scorched-earth.current",    $random->nextRange(-10, 60));
        $config->setNested("end.current",               $random->nextRange(-70, -30));
        $config->setNested("corruption.current",        $random->nextRange(-35 , 120));
        $config->save();
    }

    public static function getTemp(Player $player) : int {
        $generator = $player->getLevel()->getProvider()->getGenerator();
        $config = new Config(Main::DIR_PATH . "config.yml");

        switch($generator) {
            case "cold":
            case "xmas": //Christmas Update
                return $config->getNested("cold.current");
            case "hot":
                return $config->getNested("hot.current");
            case "murder-snow":
                return $config->getNested("murder-snow.current");
            case "scorched-earth":
                return $config->getNested("scorched.current");
            case "nether":
            case "hell":
            case "ni":
            case "netherisland":
                return $config->getNested("nether.current");
            case "end":
                return $config->getNested("end.current");
            case "corruption":
                return $config->getNested("corruption.current");
            default:
                return $config->getNested("default.current");
        }
    }

    public static function setTemp(Player $player, int $val) {
        $c = new Config(Main::DIR_NETWORK . "sb_temp.yml", Config::YAML);
        $name = $player->getLowerCaseName();

        $c->setNested($name . ".temperatur", $val);
        $c->save();
    }

    public static function setStats(Player $player, int $val, int $type) : void {
        $c = new Config(Main::DIR_NETWORK . "sb_temp.yml", Config::YAML);
        $name = $player->getLowerCaseName();

        if($type == 0) { //normal
            if($c->getNested($name . ".overheat") != 0) {
                $c->setNested($name . ".overheat", $c->getNested($name . ".overheat") - 1);
            } elseif($c->getNested($name . ".freeze") != 0) {
                $c->setNested($name . ".freeze", $c->getNested($name . ".freeze") - 1);
            } else {
                return;
            }
        }
        if($type == 1) { //overheat
            if($c->getNested($name . ".freeze") != 0) {
                $c->setNested($name . ".freeze", $c->getNested($name . ".freeze") - $val);
                if($c->getNested($name . ".freeze") > 0) {
                    $c->setNested($name . ".freeze", 0);
                }
            } elseif($c->getNested($name . ".overheat") < 15) {
                $c->setNested($name . ".overheat", $c->getNested($name . ".overheat") + $val);
                if($c->getNested($name . ".overheat") > 15) {
                    $c->setNested($name . ".overheat", 15);
                }
            } else {
                return;
            }
        }
        if($type == -1) { //freeze
            if($c->getNested($name . ".overheat") != 0) {
                $c->setNested($name . ".overheat", $c->getNested($name . ".overheat") - $val);
                if($c->getNested($name . ".overheat") > 0) {
                    $c->setNested($name . ".overheat", 0);
                }
            } elseif($c->getNested($name . ".freeze") < 15) {
                $c->setNested($name . ".freeze", $c->getNested($name . ".freeze") + $val);
                if($c->getNested($name . ".freeze") > 15) {
                    $c->setNested($name . ".freeze", 15);

                }
            } else {
                return;
            }
        }

        $c->save();

        return;
    }
}