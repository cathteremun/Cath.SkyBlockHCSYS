<?php
namespace Gradius;

use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\utils\Config;

class SendStatsTask extends Task {

    public $server;

    public function __construct(Server $server) {
        $this->server = $server;
    }

    public function onRun(int $currentTick) {
        $players = $this->server->getOnlinePlayers();
        foreach ($players as $player) {
            $c = new Config(Main::DIR_NETWORK . "sb_temp.yml", Config::YAML);
            $name = $player->getLowerCaseName();
            $temp = ChangeTemp::getTemp($player);

            if($c->getNested($name . ".overheat") != 0) {
                $data = "Hitzschlag: " . $c->getNested($name . ".overheat") . "/15";
                $player->sendActionBarMessage("§l§4". $temp ." °C\n§r§f" . $data);
            } elseif($c->getNested($name . ".freeze") != 0) {
                $data = "Kälteschlag: " . $c->getNested($name . ".freeze") . "/15";
                $player->sendActionBarMessage("§l§9". $temp ." °C\n§r§f" . $data);
            } else {
                $data = "Stabilisiert";
                $player->sendActionBarMessage("§l§2". $temp ." °C\n§r§f" . $data);
            }

        }
    }
}