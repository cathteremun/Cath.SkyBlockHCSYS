<?php
namespace Gradius;

use pocketmine\scheduler\Task;
use pocketmine\Server;

class GradiusTask extends Task {

    public $server;

    public function __construct(Server $server) {
        $this->server = $server;
    }

    public function onRun(int $currentTick) {
        ChangeTemp::change();
        $players = $this->server->getOnlinePlayers();
        foreach ($players as $player) {
            $temp = ChangeTemp::getTemp($player);
            ChangeTemp::setTemp($player, $temp);

            if($temp > 28) { //light overheat
                if($temp > 38) { //medium overheat
                    if($temp > 50) { //strong overheat
                        ChangeTemp::setStats($player, 3, 1);
                        return;
                    }
                    ChangeTemp::setStats($player, 2, 1);
                    return;
                }
                ChangeTemp::setStats($player, 1, 1);
                return;
            } else if($temp < 5) { //light freeze
                if($temp < -15) { //medium freeze
                    if($temp < -30) { //strong freeze
                        ChangeTemp::setStats($player, 3, -1);
                        return;
                    }
                    ChangeTemp::setStats($player, 2, -1);
                    return;
                }
                ChangeTemp::setStats($player, 1, -1);
                return;
            } else {
                ChangeTemp::setStats($player, 1, 0);
                return;
            }
        }
    }

}