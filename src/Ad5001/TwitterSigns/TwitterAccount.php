<?php


namespace Ad5001\TwitterSigns;



use pocketmine\Server;


use pocketmine\Player;



use Ad5001\TwitterSigns\Main;







class TwitterAccount {




   public function __construct(Main $main, string $username) {


        $this->main = $main;


        $this->username = $username;


        $this->server = $main->getServer();


        $this->signs = [];


        $this->refresh();


    }



    public function addSign(\pocketmine\level\Location $loc) {

        $this->signs[(string) $loc] = $loc->getLevel()->getTile($loc);

        $color = $this->signs[(string) $loc]->getText()[3];

        $this->signs[(string) $loc]->setText("§o§b§l[§r§l§bTwitterSigns§o]", "§" . \pocketmine\utils\TextFormat::$color . "@" . $this->username(), \pocketmine\utils\TextFormat::$color . "Followers: $this->followers", "");

    }



    public function rmSign(\pocketmine\level\Location $loc) {

        if(isset($this->signs[(string) $loc])) {

            unset($this->signs[(string) $loc]);

            return true;

        }

        return false;

    }



    public function refresh() {
        $ret = Utils::getURL("https://twitter.com/" . $_GET["username"]);
    $ret = explode("\n", $ret);
    for($i = 1100; $i < 7144; $i++) {
        unset($ret[$i]);
    }
    $ret = $ret[88];
	
	if(strpos($ret, '      <input type="hidden" id="init-data" class="json-data" value="') !== false) {
		
		$ret = substr($ret, 67);
		
		$ret = substr($ret, 0, strlen($ret) - 2);
		
		$ret = str_ireplace("&quot;", '"', $ret);
		
		$ret = json_decode($ret, true);
		
		$this->followers = $ret["profile_user"]["followers_count"];
		
	}
	else {
		
		echo "Not valid profile.";
		
	}
        foreach($this->signs as $sign) {

            $text = $sign->getText()[2];

            $color = preg_replace("/^§(\d|[a-f])Followers: (\d+)$/", "$1", $text);

            $sign->setText($sign->getText()[0], $sign->getText()[1] . "§$color" . "Followers: $this->followers");

        }
    }




}