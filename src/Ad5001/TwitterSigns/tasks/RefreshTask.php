<?php


namespace Ad5001\TwitterSigns\tasks;



use pocketmine\Server;


use pocketmine\scheduler\PluginTask;


use pocketmine\Player;


use pocketmine\utils\Config;



use Ad5001\TwitterSigns\Main;







class RefreshTask extends PluginTask {




   public function __construct(Main $main) {


        parent::__construct($main);


        $this->main = $main;


        $this->server = $main->getServer();


        $this->sessions1 = [];


        $this->sessions2 = [];


    }




   public function onRun($tick) {
       
       foreach($this->main->getAccounts() as $account) {
           $account->refresh();
       }

       $this->server->getScheduler()->scheduleAsyncTask(new RefreshTaskAsync($this->main->getDataFolder() . "tmp"));


    }




}