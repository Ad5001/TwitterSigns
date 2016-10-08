<?php


namespace Ad5001\TwitterSigns;


use pocketmine\command\CommandSender;


use pocketmine\command\Command;


use pocketmine\event\Listener;


use pocketmine\plugin\PluginBase;


use pocketmine\Server;


use pocketmine\level\Position;


use pocketmine\Player;


use Ad5001\TwitterSigns\tasks\RefreshTask;






class Main extends PluginBase implements Listener {




   public function onEnable(){


        $this->getServer()->getPluginManager()->registerEvents($this, $this);


        $this->getServer()->getScheduler()->scheduleRepeatingTask(new RefreshTask($this), 20*20); // Don't try to make it faster. There is no point + it might be recogized as a DDoS.

        
        $this->accounts = [];
    }


    public function getAccount(string $username) {
        return isset($this->accounts[$username]) ? $this->accounts[$username] : null;
    }



    public function onSignChange(\pocketmine\event\block\SignChangeEvent $event) {
        if($event->getLine(0) == "twitter") {
            if(is_null($this->getAccount($event->getLine(1)))) {
                $this->accounts[$event->getLine(1)] = new TwitterAccount($this, $event->getLine(1));
            }
            $this->accounts[$event->getLine(1)]->addSign(new Position($event->getBlock()->x, $event->getBlock()->y, $event->getBlock()->z, $event->getBlock()->getLevel()));
        }
    }



    public function onLevelLoad(\pocketmine\event\level\LevelLoadEvent $event) {
        
		if(!is_dir($this->getServer()->getFilePath() . "worlds/" . $event->getLevel()->getFolderName() . "/plugins_blocks")) {
			@mkdir($this->getServer()->getFilePath() . "worlds/" . $event->getLevel()->getFolderName() . "/plugins_blocks");
		}
		if(!is_dir($this->getServer()->getFilePath() . "worlds/" . $event->getLevel()->getFolderName() . "/plugins_blocks/Ad5001")) {
			@mkdir($this->getServer()->getFilePath() . "worlds/" . $event->getLevel()->getFolderName() . "/plugins_blocks/Ad5001");
		}
		if(!file_exists($this->getServer()->getFilePath() . "worlds/" . $event->getLevel()->getFolderName() . "/plugins_blocks/Ad5001/TwitterSigns.json")) {
			file_put_contents($this->getServer()->getFilePath() . "worlds/" . $event->getLevel()->getFolderName() . "/plugins_blocks/Ad5001/TwitterSigns.json", "{}");
		}
		$cfg = new Config($this->getServer()->getFilePath() . "worlds/" . $event->getLevel()->getFolderName() . "/plugins_blocks/Ad5001/TwitterSigns.json");
        foreach ($cfg->getAll() as $posarr => $username) {
            list($x, $y, $z) = explode("@", $posarr);
            $tile = $event->getLevel()->getTile(new \pocketmine\math\Vector3($x, $y, $z));
            if($tile instanceof \pocketmine\tile\Sign) {
                if(is_null($this->getAccount($tile->getText()[1]))) {
                    $this->accounts[$tile->getText()[1]] = new TwitterAccount($this, $username);
                }
                $this->accounts[$tile->getText()[1]]->addSign(new Position($tile->x, $tile->y, $tile->z, $event->getLevel()));
            }
        }
    }



    public function onBlockBreak(\pocketmine\event \block\BlockBreakEvent $event) {
        
		if(!is_dir($this->getServer()->getFilePath() . "worlds/" . $event->getLevel()->getFolderName() . "/plugins_blocks")) {
			@mkdir($this->getServer()->getFilePath() . "worlds/" . $event->getLevel()->getFolderName() . "/plugins_blocks");
		}
		if(!is_dir($this->getServer()->getFilePath() . "worlds/" . $event->getLevel()->getFolderName() . "/plugins_blocks/Ad5001")) {
			@mkdir($this->getServer()->getFilePath() . "worlds/" . $event->getLevel()->getFolderName() . "/plugins_blocks/Ad5001");
		}
		if(!file_exists($this->getServer()->getFilePath() . "worlds/" . $event->getLevel()->getFolderName() . "/plugins_blocks/Ad5001/TwitterSigns.json")) {
			file_put_contents($this->getServer()->getFilePath() . "worlds/" . $event->getLevel()->getFolderName() . "/plugins_blocks/Ad5001/TwitterSigns.json", "{}");
		}
		$cfg = new Config($this->getServer()->getFilePath() . "worlds/" . $event->getLevel()->getFolderName() . "/plugins_blocks/Ad5001/TwitterSigns.json");
        $tile = $event->getLevel()->getTile($event->getBlock());
        if($tile->getText()[0] == "twitter" or $tile->getText()[0] == "§o§f§l[§r§l§bTwitterSigns§o§f]") {
            $username = preg_replace("/^§(\d|[a-f])@(\.+?)$/", "$2", $text);
            $this->accounts[$username]->rmSign(new Position($tile->x, $tile->y, $tile->z, $event->getBlock()->getLevel()));
        }

    }



    public function onDisable() {
        $this->getLogger()->info("Saving signs...");
        foreach ($this->accounts as $acc) {
            $acc->save();
        }
        $this->getLogger()->info("Saved all signs.");
    }



    public function getAccounts() {
        return $this->accounts;
    }


}