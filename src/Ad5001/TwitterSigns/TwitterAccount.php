<?php


namespace Ad5001\TwitterSigns;



use pocketmine\Server;


use pocketmine\Player;


use pocketmine\utils\Utils;

use pocketmine\utils\Config;

use Ad5001\TwitterSigns\Main;







class TwitterAccount {
	
	
	
	
	public function __construct(Main $main, string $username) {
		
		
		$this->main = $main;
		
		
		$this->username = $username;
		
		
		$this->server = $main->getServer();
		
		$this->followers = "";
		
		$this->signs = [];
		
		
	}
	
	
	
	public function addSign(\pocketmine\level\Position $loc) {
		
		$this->main->getLogger()->info("Adding signs at $loc");
		
		$this->signs[(string) $loc] = $loc->level->getTile($loc);
		
		$this->refresh($this->signs[(string) $loc]->getText()[3]);
		
	}
	
	
	public function save() {
		foreach ($this->signs as $loc => $tile) {
			$levelname = preg_replace("/^Position\(level=(.+?),x=(\d+),y=(\d+),z=(\d+)\)$/", "$1", $loc);
			$x = preg_replace("/^Position\(level=(.+?),x=(\d+),y=(\d+),z=(\d+)\)$/", "$2", $loc);
			$y = preg_replace("/^Position\(level=(.+?),x=(\d+),y=(\d+),z=(\d+)\)$/", "$3", $loc);
			$z = preg_replace("/^Position\(level=(.+?),x=(\d+),y=(\d+),z=(\d+)\)$/", "$4", $loc);
			if(!is_dir($this->server->getFilePath() . "worlds/" . $levelname . "/plugins_blocks")) {
				@mkdir($this->server->getFilePath() . "worlds/" . $levelname . "/plugins_blocks");
			}
			if(!is_dir($this->server->getFilePath() . "worlds/" . $levelname . "/plugins_blocks/Ad5001")) {
				@mkdir($this->server->getFilePath() . "worlds/" . $levelname . "/plugins_blocks/Ad5001");
			}
			if(!file_exists($this->server->getFilePath() . "worlds/" . $levelname . "/plugins_blocks/Ad5001/TwitterSigns.json")) {
				file_put_contents($this->server->getFilePath() . "worlds/" . $levelname . "/plugins_blocks/Ad5001/TwitterSigns.json", "{}");
			}
			$cfg = new Config($this->server->getFilePath() . "worlds/" . $levelname . "/plugins_blocks/Ad5001/TwitterSigns.json");
			$cfg->set("$x@$y@$z", $this->username);
			$cfg->save();
		}
		$this->main->getLogger()->info("Saved signs of $this->username.");
	}
	
	
	
	public function rmSign(\pocketmine\level\Position $loc) {
		
		if(isset($this->signs[(string) $loc])) {
			
			unset($this->signs[(string) $loc]);
			
			return true;
			
		}
		
		return false;
		
	}
	
	
	
	public function refresh($color = null) {
		$cfg = new Config($this->main->getDataFolder() . "tmp", Config::YAML);
		if($cfg->exists($this->username)) {
			if(is_int($cfg->get($this->username))) {
				$this->followers = $cfg->get($this->username);
				$cfg->set($this->username, "ToGet");
			}
		} else {
			$cfg->set($this->username, "ToGet");
		}
        $cfg->save();
		if(is_int($this->followers)) {
			foreach($this->signs as $sign) {
				
				$text = $sign->getText()[2];
				
				if(is_null($color) or is_bool($color) or strlen($color) == 0 or !in_array($color, [1, 2, 3, 4, 5, 6, 7, 8, 9, 0, "a", "b", "c", "d", "e", "f"])) {
					
					$color = preg_replace("/^§(\d|[a-f])Followers: (\d+)$/", "$1", $text);
					
					if(is_null($color) or is_bool($color) or strlen($color) == 0 or !in_array($color, [1, 2, 3, 4, 5, 6, 7, 8, 9, 0, "a", "b", "c", "d", "e", "f"])) {
						// 						If it's still not a valid color
                     $color = "b";
                    }
                }
                $sign->setText("§o§f§l[§r§l§bTwitterSigns§o§f]", "§$color@" . $this->username, "§$color"."Followers: $this->followers");
            }
        }
    }
}