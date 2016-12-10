<?php


namespace Ad5001\TwitterSigns\tasks;



use pocketmine\Server;


use pocketmine\scheduler\AsyncTask;


use pocketmine\Player;


use pocketmine\utils\Config;



use Ad5001\TwitterSigns\Main;







class RefreshTaskAsync extends AsyncTask {
	
	
	
	
	public function __construct(string $temppath) {
		
		$this->temppath = $temppath;
		
	}
	
	
	
	
	public function onRun() {

        $yaml = yaml_parse(file_get_contents($this->temppath));
		
		foreach($yaml as $account => $count) {
			if(!is_int($count)) {
				$ch = curl_init("https://twitter.com/" . $account);
				curl_setopt($ch, CURLOPT_HTTPHEADER, ["User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:12.0) Gecko/20100101 Firefox/12.0 PocketMine-MP"]);
				curl_setopt($ch, CURLOPT_AUTOREFERER, true);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
				curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
				curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, (int) 40);
				curl_setopt($ch, CURLOPT_TIMEOUT, (int) 40);
				$ret = curl_exec($ch);
				curl_close($ch);
				$ret = explode("\n", $ret);
				for ($i = 1100; $i < 7144; $i++) {
					unset($ret[$i]);
				}
				$ret = $ret[89];
				
				if(strpos($ret, '      <input type="hidden" id="init-data" class="json-data" value="') !== false) {
					
					$ret = substr($ret, 67);
					
					$ret = substr($ret, 0, strlen($ret) - 2);
					
					$ret = str_ireplace("&quot;", '"', $ret);
					
					$ret = json_decode($ret, true);

                    $yaml[$account] = $ret["profile_user"]["followers_count"];
				}
			}


            file_put_contents($this->temppath, yaml_emit($yaml));
			
			
		}
		
		
		
		
	}
}	