<?php


namespace Ad5001\TwitterSigns\tasks;



use pocketmine\Server;


use pocketmine\schedulerPluginTask;


use pocketmine\Player;


use pocketmine\utils\Config;



use Ad5001\TwitterSigns\Main;







class RefreshTask extends PluginTask {




   public function __construct(Main $main) {


        parent::__construct($main);


        $this->main = $main;


        $this->server = $main->getServer();


    }




   public function onRun($tick) {
       
       foreach($this->main->getAccounts() as $account) {
           $account->refresh();
       }


    }




}