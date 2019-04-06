<?php

namespace ServerSystem;

/*
ワールドロード
空白の名前を_に
NoSpam
SettingMessage
資源リセット告知
矢のエンティティ削除
*/

//空白の名前を_に
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\LoginPacket;
//

//NoSpam
//use pocketmine\plugin\PluginBase;
//use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\math\Vector3;
use pocketmine\level\Postion;
use pocketmine\tile\Sign;
use pocketmine\tile\Tile;
use pocketmine\item\Item;
//

//SettingMessage
//use pocketmine\Player;
//use pocketmine\Server;
//use pocketmine\plugin\PluginBase;
//use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;

//矢のエンティティ削除
//use pocketmine\Player;
//use pocketmine\Server;
//use pocketmine\plugin\PluginBase;
//use pocketmine\event\Listener;
use pocketmine\event\entity\ProjectileHitEvent;
//use pocketmine\item\Item;
use pocketmine\entity\Arrow;
//

class main extends PluginBase implements Listener{
    
    //NoSpam
    private $array = [];
    private $count = [];
    //
	
	public function onEnable(){
	    //ワールドロード
		$server = \pocketmine\Server::getInstance();
		$worlddir = "worlds/";
		$count = 0;
		foreach (scandir($worlddir) as $value) {
			if(is_dir($worlddir . $value) && ($value !== "." && $value !== "..") ){
				$server->loadLevel($value) && $count++;
			}
		}
		$this->getLogger()->info("{$count}個のワールドを読み込みました");
		//
		
		//様々なプラグイン
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		//
	}
	
	public function onLogin(DataPacketReceiveEvent $event) {
	    //名前の空白を_に
		$packet = $event->getPacket();
		if($packet instanceof LoginPacket){
			$name = $packet->username;
			if(strpos($name, " ") !== false){
				$newname = str_replace(" ", "_", $name);
				$packet->username = $newname;
			}
		}
		//
	}
	
	public function onChat(PlayerChatEvent $event){
	        //Nospam
		$player = $event->getPlayer();
                $name = $player->getName();
                if($this->Spam2($player)){
                    $event->setCancelled();
                    $player->sendMessage("§a【運営】 §c連続での投稿は出来ません。");
                    $this->Spam($player);
                }else{
                    if(array_key_exists($name,$this->count)){
                        unset($this->count[$name]);
                    }
                }
                //
        }
    
	public function useCommand(PlayerCommandPreProcessEvent $event){
		//NoSpam
                $player = $event->getPlayer();
                $name = $player->getName();
                $msg = $event->getMessage();
                if(substr($msg,0,4) === "/me "){
			if($this->Spam2($player)){
				$event->setCancelled();
                                $player->sendMessage("§a【運営】 §c連続での投稿は出来ません。");
                                $this->Spam($player);
			}else{
				if(array_key_exists($name,$this->count)){
					unset($this->count[$name]);
				}
			}
		}elseif(substr($msg,0,5) === "./me "){
			$event->setCancelled();
                        $player->sendMessage("§a【運営】 §fスパム防止のため「./me」は使えません。");
	        }elseif(substr($msg,0,3) === "/w " || substr($msg,0,4) === "./w "){
		        if($this->Spam2($player)){
				$event->setCancelled();
                                $player->sendMessage("§a【運営】 §c連続での投稿は出来ません。");
                                $this->Spam($player);
			}else{
				if(array_key_exists($name,$this->count)){
					unset($this->count[$name]);
				}
			}
		}
		//
	}
	
	public function onTap(PlayerInteractEvent $event){
		//NoSpam
                $player = $event->getPlayer();
                $name = $player->getName();
                $block = $event->getBlock();
                $id = $block->getId();
                if($id === 63 or $id === 68){
		        $sign = $player->getLevel()->getTile(new Vector3($block->x,$block->y,$block->z));
                        if($sign instanceof Tile){
			        $text = $sign->getText();
                                $str = $text[0].$text[1].$text[2].$text[3];
                                if(strpos($str,'##') !== false){
				        preg_match("/^##(.+[^\s])/",$str,$txt);
				        if(substr($txt[1],0,3) === "me "){
					        if($this->Spam2($player)){
						        $event->setCancelled();
						        $player->sendMessage("§a【運営】 §fme看板の連打はやめてください。");
						        $this->Spam($player);
					        }else{
						        if(array_key_exists($name,$this->count)){
							        unset($this->count[$name]);
						        }
					        }
				        }
			        }
		        }
	        }
	        //
	}
    
        public function onJoin(PlayerJoinEvent $event){
		//SettingMessage
		$p = $event->getPlayer();
		$message = "§l§e%nameさんがサーバーにやってきました";
		$message_op = "§l§d権限者§f %name §eがサーバーにやってきました";
		$message = str_replace("%name", $p->getName(), $message);
		$message_op = str_replace("%name", $p->getName(), $message_op);
		if($p->isOp()){
			$event->setJoinMessage($message_op);
		}else{
			$event->setJoinMessage($message);
		}
		//
		
		//資源リセット告知
		if($event->getPlayer()->getName() == "narapon"){
		    if(date("j") == "1"){
		        $event->getPlayer()->sendMessage("§a【運営】 §c資源のリセット日です");
		    }
		}
		//
	}

	public function onQuit(PlayerQuitEvent $event){
	        //SettingMessage
		$p = $event->getPlayer();
		$message = "§l§e%nameさんがサーバーを去りました";
		$message_op = "§l§d権限者 §f%name §eがサーバーを去りました";
		$message = str_replace("%name", $p->getName(), $message);
		$message_op = str_replace("%name", $p->getName(), $message_op);
		if($p->isOp()){
			$event->setQuitMessage($message_op);
		}else{
			$event->setQuitMessage($message);
		}
		//
	}
	
	//矢のエンティティ削除
	public function Hit(ProjectileHitEvent $event){
		$entity = $event->getEntity();
		$entity->kill();
	}
	//
    
//以下関数
	
	//NoSpam
	private function Spam($player){
		$name = $player->getName();
                if(array_key_exists($name,$this->count)){
			if($this->count[$name] >= 3){
				$player->kick("§a【運営】 §cあなたはスパムと判断され、kickされました",false);//「Kicked by admin」非表示
                                $this->getServer()->broadcastMessage("§a[運営] §f".$name."はスパムと判断されたためキックされました。");
                                unset($this->array[$name]);
                                unset($this->count[$name]);
			}else{
				$this->count[$name] += 1;
			}
		}else{
			$this->count[$name] = 1;
		}
	}

	private function Spam2($player){
		$name = $player->getName();
                if(array_key_exists($name,$this->array)){
			$now = time();
                        $last = $this->array[$name];
                        $time = $now - $last;
			if($time <= 1){
				return true;
			}else{
				$now = time();
                                $this->array[$name] = $now;
                                return false;
			}
		}else{
			$now = time();
			$this->array[$name] = $now;
			return false;
		}
	}
	//
}
