<?php
namespace SkyBlockHCSYS\task;

use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\utils\Config;
use SkyBlockHCSYS\Main;
use SkyBlockHCSYS\Utils;

class SendDataTask extends Task {

    public $server;

    public function __construct(Server $server) {
        $this->server = $server;
    }

    public function onRun(int $currentTick) {
        $players = $this->server->getOnlinePlayers();
        foreach ($players as $player) {
            $c = new Config(Main::DIR_NETWORK . "sbhcsys.yml", Config::YAML);
            $name = $player->getLowerCaseName();
            $temp = Utils::getPlayerTemperature($player->getLevel()->getProvider()->getGenerator());

            if($c->getNested($name . ".overheat") != 0 or $temp > 30) {
                $data = "Hitzschlag: " . $c->getNested($name . ".overheat") . "/100";
            } elseif($c->getNested($name . ".freeze") != 0 or $temp < 5) {
                $data = "Kälteschlag: " . $c->getNested($name . ".freeze") . "/100";
            } else {
                $data = "Stabilisiert";
            }

            if($temp > 30) {
                $player->sendActionBarMessage("§l§4". $temp ." °C\n§r§f" . $data);
            } elseif($temp < 5) {
                $player->sendActionBarMessage("§l§9". $temp ." °C\n§r§f" . $data);
            } else {
                $player->sendActionBarMessage("§l§2". $temp ." °C\n§r§f" . $data);
            }

        }
    }
}