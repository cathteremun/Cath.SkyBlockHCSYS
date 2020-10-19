<?php
declare(strict_types = 1);

namespace SkyBlockHCSYS;

use pocketmine\plugin\PluginBase;
use SkyBlockHCSYS\task\{SendDataTask, UpdateDataTask};

class Main extends PluginBase {

    private static $instance = null;
    public $server;

    const DIR_PATH = "/home/cathesda/skyblock/plugin_data/Cath.SkyBlockHCSYS/";
    const DIR_NETWORK = "/home/cathesda/network/Data/";

    public static function getInstance(): ? Main{
        return self::$instance;
    }

    public function onLoad(){
        self::$instance = $this;
    }

    public function onEnable(){
        $this->saveDefaultConfig();

        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
        $this->getScheduler()->scheduleRepeatingTask(new UpdateDataTask($this->getServer()), 1200); //1200
        $this->getScheduler()->scheduleRepeatingTask(new SendDataTask($this->getServer()), 40);
    }
}