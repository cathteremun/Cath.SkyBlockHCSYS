<?php

namespace SkyBlockHCSYS;

use pocketmine\entity\{Effect, EffectInstance};
use pocketmine\item\Item;
use pocketmine\network\mcpe\protocol\types\GameMode;
use pocketmine\Player;
use pocketmine\utils\{Config, Random};

class Utils {

    /**
     * Change the temperature of a generator type (biome)
     * @param string $biome
     * @param Random $random
     */
    public static function changeTemperature(string $biome, Random $random) {
        $config = new Config(Main::DIR_PATH . "config.yml");
        $current = $config->getNested($biome . ".current");
        $min = $config->getNested($biome . ".min");
        $max = $config->getNested($biome . ".max");
        $val = $random->nextRange(1, 5);
        $type = $random->nextRange(0,3);

        if($type == 1) {
            $config->setNested($biome . ".current", $current + $val);
            if($config->getNested($biome . ".current") > $max) {
                $config->setNested($biome . ".current", $max);
            }
        } elseif($type == 2) {
            $config->setNested($biome . ".current", $current - $val);
            if($config->getNested($biome . ".current") < $min) {
                $config->setNested($biome . ".current", $min);
            }
        } else {
            return;
        }
        $config->save();
        return;
    }

    /**
     * Sets effects to the player based on his health state
     * @param Player $player
     * @param int $value
     * @param int $type
     */
    public static function setPlayerEffect(Player $player, int $value, int $type) {
        if($player->getGamemode() == GameMode::CREATIVE) {
            return;
        }
        if($type === 0) {
            if($value > 65) {
                $player->addEffect(new EffectInstance(Effect::getEffect(Effect::SLOWNESS), 2400, 1, false));
                if($value > 80) {
                    $player->addEffect(new EffectInstance(Effect::getEffect(Effect::NAUSEA), 100, 0, false));
                    if($value == 100) {
                        $player->addEffect(new EffectInstance(Effect::getEffect(Effect::POISON), 20, 1, false));
                    }
                }
            }
        } elseif ($type === 1) {
            if ($value > 65) {
                $player->addEffect(new EffectInstance(Effect::getEffect(Effect::SLOWNESS), 2400, 1, false));
                if ($value > 80) {
                    $player->addEffect(new EffectInstance(Effect::getEffect(Effect::BLINDNESS), 2400, 1, false));
                    if ($value == 100) {
                        $player->addEffect(new EffectInstance(Effect::getEffect(Effect::POISON), 20, 1, false));
                    }
                }
            }
        } else {
            return;
        }
    }

    /**
     * Returns the temperature of a generator where the player is
     * @param string $biome
     * @return int
     */
    public static function getPlayerTemperature(string $biome) : int {
        $c = new Config(Main::DIR_PATH . "config.yml");
        if($c->exists($biome)) {
            return $c->getNested($biome . ".current");
        } else {
            return $c->getNested("default" . ".current");
        }
    }

    /**
     * Returns array with all necessary data
     * @param Player $player
     * @return array
     */
    public static function getPlayerData(Player $player) : array {
        $c = new Config(Main::DIR_NETWORK . "sbhcsys.yml", Config::YAML);
        if($c->getNested($player->getLowerCaseName() . ".overheat") != 0) {
            $data[0] = $c->getNested($player->getLowerCaseName() . ".overheat");
            $data[1] = 0;
        } elseif($c->getNested($player->getLowerCaseName() . ".freeze") != 0) {
            $data[0] = $c->getNested($player->getLowerCaseName() . ".freeze");
            $data[1] = 1;
        } else {
            $data[0] = 0;
            $data[1] = 2;
        }
        return $data;
    }

    /**
     * Sets player temperature in order to choose how strong the effect is
     * @param Player $player
     * @param int $val
     */
    public static function setTemp(Player $player, int $val) {
        $c = new Config(Main::DIR_NETWORK . "sbhcsys.yml", Config::YAML);

        $c->setNested($player->getLowerCaseName() . ".temperature", $val);
        $c->save();
    }

    /**
     * Sets player data
     * @param Player $player
     * @param int $val
     * @param int $type
     */
    public static function setPlayerData(Player $player, int $val, int $type) : void {
        $c = new Config(Main::DIR_NETWORK . "sbhcsys.yml", Config::YAML);
        $name = $player->getLowerCaseName();

        if($type == 0) { //normal
            if($c->getNested($name . ".overheat") != 0) {
                $c->setNested($name . ".overheat", $c->getNested($name . ".overheat") - 1);
            } elseif($c->getNested($name . ".freeze") != 0) {
                $c->setNested($name . ".freeze", $c->getNested($name . ".freeze") - 1);
            }
        }
        if($type == 1) { //overheat
            if($c->getNested($name . ".freeze") != 0) {
                $c->setNested($name . ".freeze", $c->getNested($name . ".freeze") - $val);
                if($c->getNested($name . ".freeze") > 0) {
                    $c->setNested($name . ".freeze", 0);
                }
            } elseif($c->getNested($name . ".overheat") < 100) {
                $c->setNested($name . ".overheat", $c->getNested($name . ".overheat") + $val);
                if($c->getNested($name . ".overheat") > 100) {
                    $c->setNested($name . ".overheat", 100);
                }
                /*if($c->getNested($name . ".overheat") < 0) {
                    $c->setNested($name . ".overheat", 0);
                }*/
            }
        }
        if($type == -1) { //freeze
            if($c->getNested($name . ".overheat") != 0) {
                $c->setNested($name . ".overheat", $c->getNested($name . ".overheat") - $val);
                if($c->getNested($name . ".overheat") > 0) {
                    $c->setNested($name . ".overheat", 0);
                }
            } elseif($c->getNested($name . ".freeze") < 100) {
                $c->setNested($name . ".freeze", $c->getNested($name . ".freeze") + $val);
                if($c->getNested($name . ".freeze") > 100) {
                    $c->setNested($name . ".freeze", 100);
                }
                /*if($c->getNested($name . ".freeze") < 0) {
                    $c->setNested($name . ".freeze", 0);
                }*/
            }
        }
        $c->save();
        return;
    }

    /**
     * Returns array that gives information, what armor the player wears
     * @param Player $player
     * @return array
     */
    public static function getPlayerArmor(Player $player) : array {
        $data[0] = $player->getArmorInventory()->getBoots()->getId();
        $data[1] = $player->getArmorInventory()->getLeggings()->getId();
        $data[2] = $player->getArmorInventory()->getChestplate()->getId();
        $data[3] = $player->getArmorInventory()->getHelmet()->getId();
        return $data;
    }

    /**
     * Returns a value how much the armor can tank
     * @param Player $player
     * @return int
     */
    public static function getArmorReduction(Player $player) : int {
        $armor = Utils::getPlayerArmor($player);
        $pieces = 0;
        foreach($armor as $data) {
            if($data === 317 or $data === 316 or $data === 315 or $data === 314 or $data === 751 or $data === 750 or $data === 749 or $data === 748) {
                if($data === 317 or $data === 751) {
                    $player->getArmorInventory()->setBoots(Item::get($data)->setDamage(Item::get($data)->getDamage() + 5));
                }
                if($data === 316 or $data === 750) {
                    $player->getArmorInventory()->setLeggings(Item::get($data)->setDamage(Item::get($data)->getDamage() + 5));
                }
                if($data === 315 or $data === 749) {
                    $player->getArmorInventory()->setChestplate(Item::get($data)->setDamage(Item::get($data)->getDamage() + 5));
                }
                if($data === 314 or $data === 748) {
                    $player->getArmorInventory()->setHelmet(Item::get($data)->setDamage(Item::get($data)->getDamage() + 5));
                }
                $pieces++;
            }
        }
        if($pieces === 0) {
            return 0;
        }if($pieces >= 3) {
            return 2;
        } if($pieces <= 2) {
            return 1;
        }
        return 0;
    }
}