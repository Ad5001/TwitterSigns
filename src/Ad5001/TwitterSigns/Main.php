<?php


namespace Ad5001\TwitterSigns;


use pocketmine\command\CommandSender;


use pocketmine\command\Command;


use pocketmine\event\Listener;


use pocketmine\plugin\PluginBase;


use pocketmine\Server;


use pocketmine\level\Location;


use pocketmine\Player;


use Ad5001\TwitterSigns\tasks\RefreshTask;






class Main extends PluginBase implements Listener {




   public function onEnable(){


        $this->getServer()->getPluginManager()->registerEvents($this, $this);


        $this->getServer()->getScheduler()->scheduleRepeatingTask(new RefreshTask($this), 20*60*30); // Don't try to make it faster. There is no point + it might be recogized as a DDoS. Also, Twitter allows me only 15 persons per 1/4 hour :'(

        
        $this->accounts = [];
    }


    public function getAccount(string $username) {
        return isset($this->accounts[$username]) ? $this->accounts[$username] : null;
    }



    public function onSignChange(\pocketmine\event\block\SignChangeEvent $event) {
        if($event->getLine(2) == "twitter") {
            if(is_null($this->getAccount($event->getLine(1)))) {
                $this->accounts[$event->getLine(1)] = new TwitterAccount($this, $event->getLine(1));
            }
            $this->accounts[$event->getLine(1)]->addSign(new Location($event->getBlock()->x, $event->getBlock()->y, $event->getBlock()->z, $event->getBlock()->getLevel()));
        }
    }



    public function onLevelLoad(\pocketmine\event\level\LevelLoadEvent $event) {
        foreach ($event->getLevel()->getTiles() as $tile) {
            if($tile instanceof \pocketmine\tile\Sign) {
                if($tile->getText()[2] == "twitter") {
                    if(is_null($this->getAccount($tile->getText()[1]))) {
                        $this->accounts[$tile->getText()[1]] = new TwitterAccount($this, $tile->getText()[1]);
                    }
                    $this->accounts[$tile->getText()[1]]->addSign(new Location($tile->x, $tile->y, $tile->z, $event->getLevel()));
                }
            }
        }
    }



    public function getAccounts() {
        return $this->accounts;
    }


}