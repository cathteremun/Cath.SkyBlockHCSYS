<?php
namespace SkyBlockHCSYS\task;

use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\utils\Random;
use SkyBlockHCSYS\Utils;

class UpdateDataTask extends Task {

    public $server;

    public function __construct(Server $server) {
        $this->server = $server;
    }

    public function onRun(int $currentTick) {
        $random = new Random(mt_rand(1, 256));
        Utils::changeTemperature("default", $random);
        Utils::changeTemperature("sb-nether", $random);
        Utils::changeTemperature("sb-xmas", $random);
        //Utils::changeTemperature("sb-scorched"); //When summer is
        //Utils::changeTemperature("sb-corrupt"); // Autumn 2021

        $players = $this->server->getOnlinePlayers();
        foreach ($players as $player) {
            $playerdata = Utils::getPlayerData($player);
            $reduction = Utils::getArmorReduction($player);

            $temp = Utils::getPlayerTemperature($player->getLevel()->getProvider()->getGenerator());
            Utils::setTemp($player, $temp);

            if($temp > 30) { //light overheat
                if($temp > 38) { //medium overheat
                    if($temp > 50) { //strong overheat
                        Utils::setPlayerData($player, 4 - $reduction,1);
                        Utils::setPlayerEffect($player, $playerdata[0], $playerdata[1]);
                        return;
                    }
                    Utils::setPlayerData($player, 3 - $reduction,1);
                    Utils::setPlayerEffect($player, $playerdata[0], $playerdata[1]);
                    return;
                }
                Utils::setPlayerData($player, 1 - $reduction,1);
                Utils::setPlayerEffect($player, $playerdata[0], $playerdata[1]);
                return;
            } elseif($temp < 5) { //light freeze
                if($temp < -15) { //medium freeze
                    if($temp < -30) { //strong freeze
                        Utils::setPlayerData($player, 4 - $reduction,-1);
                        Utils::setPlayerEffect($player, $playerdata[0], $playerdata[1]);
                        return;
                    }
                    Utils::setPlayerData($player, 3 - $reduction,-1);
                    Utils::setPlayerEffect($player, $playerdata[0], $playerdata[1]);
                    return;
                }
                Utils::setPlayerData($player, 1 - $reduction,-1);
                Utils::setPlayerEffect($player, $playerdata[0], $playerdata[1]);
                return;
            } else {
                Utils::setPlayerData($player, 1, 0);
                Utils::setPlayerEffect($player, $playerdata[0], $playerdata[1]);
                return;
            }
        }
    }

}