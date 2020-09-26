<?php
declare(strict_types = 1);

namespace Gradius;

use pocketmine\plugin\PluginBase;

class Main extends PluginBase {

    private static $instance = null;
    public $server;
    const DIR_PATH = "/home/cathesda/skyblock/plugin_data/Cath.SkyBlockTemp/";
    const DIR_NETWORK = "/home/cathesda/network/Data/";

    public static function getInstance(): ?Main{
        return self::$instance;
    }

    public function onLoad(){
        self::$instance = $this;
    }

    public function onEnable(){
        $this->saveDefaultConfig();

        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
        $this->getScheduler()->scheduleRepeatingTask(new GradiusTask($this->getServer()), 2 * 1200);
        $this->getScheduler()->scheduleRepeatingTask(new SendStatsTask($this->getServer()), 40);
    }
}