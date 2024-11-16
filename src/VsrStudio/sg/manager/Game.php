<?php

namespace VsrStudio\sg\manager;

use pocketmine\Server;
use pocketmine\block\VanillaBlocks;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\player\GameMode;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\math\Vector3;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as TF;
use pocketmine\world\Position;
use pocketmine\world\World;
use pocketmine\world\sound\AnvilBreakSound;
use pocketmine\world\sound\BellRingSound;
use pocketmine\world\sound\PopSound;
use pocketmine\world\sound\PotionSplashSound;
use pocketmine\world\sound\RedstonePowerOnSound;
use pocketmine\world\sound\XpCollectSound;
use VsrStudio\sg\Main;
use VsrStudio\sg\manager\SkywarsManager;
use VsrStudio\sg\utils\Scoreboard;
use VsrStudio\sg\task\SkywarsTask;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use FilesystemIterator;

class Game
{	
	/** @var array */
	public array $matchInfo;

	/** @var Player[] */
	private array $players = [];

	/** @var array */
	private array $playerSpawn = [];	

	/** @var array */
	private array $topPlayer = [];	

	/** @var Block[] */
	public array $placedBlock = [];

	/** @var array */
	private array $playerInfo = [];

	/** @var array */
	public array $playersLeft = [];

	/** @var bool */
	private bool $start = false;

	/** @var int */
	private int $cage = 10;

	/** @var int */
	private int $countdown = 60;	

	/** @var int */
	private int $end = 10;

	/** @var int */
	private int $timer = 900;

	/** @var string */
	public string $phase = "OFFLINE";

	public function __construct(	
		?string $arenaName = null, 
		?string $worldName = null, 
		?string $hub = null, 
		?string $hubSpawn = null, 
		?int $maxPlayer = 12,
		?string $spawn1 = null,
    	?string $spawn2 = null,
    	?string $spawn3 = null,
    	?string $spawn4 = null,
    	?string $spawn5 = null,
    	?string $spawn6 = null,
    	?string $spawn7 = null,
    	?string $spawn8 = null,
    	?string $spawn9 = null,
    	?string $spawn10 = null,
    	?string $spawn11 = null,
    	?string $spawn12 = null, 
		?string $map = null, 
		?int $maxChest = 41,
		?string $C1 = null,
    	?string $C2 = null,
    	?string $C3 = null,
    	?string $C4 = null,
    	?string $C5 = null,
    	?string $C6 = null,
    	?string $C7 = null,
    	?string $C8 = null,
    	?string $C9 = null,
    	?string $C10 = null,
    	?string $C11 = null,
    	?string $C12 = null,
    	?string $C13 = null,
    	?string $C14 = null,
    	?string $C15 = null,
    	?string $C16 = null,
    	?string $C17 = null,
    	?string $C18 = null,
    	?string $C19 = null,
    	?string $C20 = null,
    	?string $C21 = null,
    	?string $C22 = null,
    	?string $C23 = null,
    	?string $C24 = null,
    	?string $C25 = null,
    	?string $C26 = null,
    	?string $C27 = null,
    	?string $C28 = null,
    	?string $C29 = null,
    	?string $C30 = null,
    	?string $C31 = null,
    	?string $C32 = null,
    	?string $C33 = null,
    	?string $C34 = null,
    	?string $C35 = null,
    	?string $C36 = null,
    	?string $C37 = null,
    	?string $C38 = null,
    	?string $C39 = null,
    	?string $C40 = null,
    	?string $C41 = null,
	){
		$this->matchInfo["arenaname"] = $arenaName;
		$this->matchInfo["worldname"] = $worldName;		
		$this->matchInfo["hub"] = $hub;
		$this->matchInfo["hubspawn"] = $hubSpawn;
		$this->matchInfo["maxplayers"] = $maxPlayer;
		$this->matchInfo["spawn-1"] = $spawn1;
    	$this->matchInfo["spawn-2"] = $spawn2;
    	$this->matchInfo["spawn-3"] = $spawn3;
    	$this->matchInfo["spawn-4"] = $spawn4;
    	$this->matchInfo["spawn-5"] = $spawn5;
    	$this->matchInfo["spawn-6"] = $spawn6;
    	$this->matchInfo["spawn-7"] = $spawn7;
    	$this->matchInfo["spawn-8"] = $spawn8;
    	$this->matchInfo["spawn-9"] = $spawn9;
    	$this->matchInfo["spawn-10"] = $spawn10;
    	$this->matchInfo["spawn-11"] = $spawn11;
    	$this->matchInfo["spawn-12"] = $spawn12;
		$this->matchInfo["map"] = $map;
		$this->matchInfo["C-1"] = $C1;
    	$this->matchInfo["C-2"] = $C2;
    	$this->matchInfo["C-3"] = $C3;
    	$this->matchInfo["C-4"] = $C4;
    	$this->matchInfo["C-5"] = $C5;
    	$this->matchInfo["C-6"] = $C6;
    	$this->matchInfo["C-7"] = $C7;
    	$this->matchInfo["C-8"] = $C8;
    	$this->matchInfo["C-9"] = $C9;
    	$this->matchInfo["C-10"] = $C10;
    	$this->matchInfo["C-11"] = $C11;
    	$this->matchInfo["C-12"] = $C12;
    	$this->matchInfo["C-13"] = $C13;
    	$this->matchInfo["C-14"] = $C14;
    	$this->matchInfo["C-15"] = $C15;
    	$this->matchInfo["C-16"] = $C16;
    	$this->matchInfo["C-17"] = $C17;
    	$this->matchInfo["C-18"] = $C18;
    	$this->matchInfo["C-19"] = $C19;
    	$this->matchInfo["C-20"] = $C20;
    	$this->matchInfo["C-21"] = $C21;
    	$this->matchInfo["C-22"] = $C22;
    	$this->matchInfo["C-23"] = $C23;
    	$this->matchInfo["C-24"] = $C24;
    	$this->matchInfo["C-25"] = $C25;
    	$this->matchInfo["C-26"] = $C26;
    	$this->matchInfo["C-27"] = $C27;
    	$this->matchInfo["C-28"] = $C28;
    	$this->matchInfo["C-29"] = $C29;
    	$this->matchInfo["C-30"] = $C30;
    	$this->matchInfo["C-31"] = $C31;
    	$this->matchInfo["C-32"] = $C32;
    	$this->matchInfo["C-33"] = $C33;
    	$this->matchInfo["C-34"] = $C34;
    	$this->matchInfo["C-35"] = $C35;
    	$this->matchInfo["C-36"] = $C36;
    	$this->matchInfo["C-37"] = $C37;
    	$this->matchInfo["C-38"] = $C38;
    	$this->matchInfo["C-39"] = $C39;
    	$this->matchInfo["C-40"] = $C40;
    	$this->matchInfo["C-41"] = $C41;		
		$this->matchInfo["maxchest"] = $maxChest;
		if ($this->isValid()) 
		{
			$this->startArena();
		}
	}

	public function isValid(): bool
	{
		if((is_string($this->matchInfo["arenaname"])) and (is_string($this->matchInfo["worldname"])) and (is_string($this->matchInfo["hub"])) and (is_string($this->matchInfo["map"])) and (is_string($this->matchInfo["hubspawn"])) and (is_string($this->matchInfo["spawn-12"])))
		{
			return true;
		}
		return false;
	}

	public function getArenaName(): string
	{
		return $this->matchInfo["arenaname"];
	}

	public function getArenaInfo(): array
	{
		$array = [];
		foreach($this->matchInfo as $k => $o)
		{
			$array[$k] = $o;	
		}
		return $array;
	}

	public function reload(): void
	{
		$config = new Config(Main::getInstance()->getDataFolder() . "arenas/" . $this->matchInfo["arenaname"] . ".yml", Config::YAML);
		$config->setAll($this->getArenaInfo());
		$config->save();
		if($this->isValid())
		{
			$this->startArena();
		}
	}

	public function editArena(Player $player): void
	{
		$player->getInventory()->clearAll();
		$player->getInventory()->setItem(1, VanillaBlocks::CHEST()->asItem()->setCustomName($this->matchInfo["arenaname"].":CHEST-SPAWN"));		
		$player->getInventory()->setItem(2, VanillaItems::WOODEN_AXE()->setCustomName($this->matchInfo["arenaname"].":PLAYER-SPAWN"));		
		$player->getInventory()->setItem(3, VanillaItems::IRON_AXE()->setCustomName($this->matchInfo["arenaname"].":HUB-SPAWN"));

		$player->getInventory()->setItem(5, VanillaBlocks::CHEST()->asItem()->setCustomName($this->matchInfo["arenaname"].":CHEST-SPAWN-RESET"));		
		$player->getInventory()->setItem(6, VanillaItems::WOODEN_AXE()->setCustomName($this->matchInfo["arenaname"].":PLAYER-SPAWN-RESET"));		
	}

	public function startArena(): void
	{
		if(!(Server::getInstance()->getWorldManager()->isWorldLoaded($this->matchInfo["worldname"])))
		{
			Server::getInstance()->getWorldManager()->loadWorld($this->matchInfo["worldname"]);
		}
		if($this->start == false)
		{
			$this->start = true;
			$this->phase = "WAITING";
			Main::getInstance()->getScheduler()->scheduleRepeatingTask(new SkywarsTask($this), 20);
		}		
	}

	public function stopArena(): void
	{	
		if($this->start == true)
		{
			$this->start = false;			
		}	
	}

	public function isStatus(bool $arena = true): bool
	{
		if($arena)
		{
			if($this->isValid() == false)
			{
				$this->stopArena();
				return false;
			}
			if($this->phase == "WAITING" || $this->phase == "COUNTDOWN")
			{
				if ($this->players >= 1) 
				{
				    return $this->start;
				} else
				{
					return $this->start;
				}	
			}
		}		
		return false;	
	}

	public function getPhase(): string
	{
		return $this->phase;
	}

	public function countPlayers(): int
	{
		if(!empty($this->players) || is_array($this->players))
		{
			return count($this->players);
		}
		return 0;
	}

	public function getMaxPlayers(): int
	{
		return $this->matchInfo["maxplayers"];
	}

	public function setChestPos(string $spawn, $pos): void
	{
		$this->matchInfo[$spawn] = $pos;
	}

	public function setSpawnPos(string $spawn, $pos): void
	{
		$this->matchInfo[$spawn] = $pos;
	}

	public function setWorld(string $world): void
	{
		$this->matchInfo["worldname"] = $world;
	}

	public function getWorld(): string
	{
		return $this->matchInfo["worldname"];
	}

	public function setHubPos($pos): void
	{
		$this->matchInfo["hubspawn"] = $pos;
	}

	public function setHubWorld(string $world): void
	{
		$this->matchInfo["hub"] = $world;
	}

	public function getHubWorld(): string
	{
		return $this->matchInfo["hub"];
	}	

	public function setMap(string $map): void
	{
		$this->matchInfo["map"] = $map;
	}

	public function getMap(): string
	{
		return $this->matchInfo["map"];
	}

	public function tick(): void
	{
		foreach($this->players as $player)
		{
			if($player->isConnected() == null)
			{
				$this->removePlayer($player);
				return;
			}			
		}
		switch($this->phase)
		{
			case "WAITING":
				foreach($this->players as $player)
				{
					if($player->isOnline())
					{
						Scoreboard::getInstance()->new($player, "SkyBlock", TF::BOLD . TF::GOLD . "SKY" . TF::YELLOW . "WARS");
						Scoreboard::getInstance()->setLine($player, 0, "      ");
						Scoreboard::getInstance()->setLine($player, 1, "Players: " .  TF::AQUA . count($this->players) . TF::RESET . "/" . TF::AQUA . "12");
						Scoreboard::getInstance()->setLine($player, 2, "     ");
						Scoreboard::getInstance()->setLine($player, 3, TF::GOLD."Waiting for players ...");
						Scoreboard::getInstance()->setLine($player, 4, "    ");
						Scoreboard::getInstance()->setLine($player, 5, "Map: ".TF::YELLOW.$this->matchInfo["map"]);
						Scoreboard::getInstance()->setLine($player, 6, "   ");
						Scoreboard::getInstance()->setLine($player, 7, "Mode: ".TF::YELLOW."Solo");
						Scoreboard::getInstance()->setLine($player, 8, "  ");
						Scoreboard::getInstance()->setLine($player, 9, Main::getInstance()->config->get("ServerIp"));
					}					
				}
			break;

			case "COUNTDOWN":
				foreach($this->players as $player)
				{
					if($player->isOnline())
					{
						Scoreboard::getInstance()->new($player, "SkyBlock", TF::BOLD . TF::GOLD . "SKY" . TF::YELLOW . "WARS");
						Scoreboard::getInstance()->setLine($player, 0, "      ");
						Scoreboard::getInstance()->setLine($player, 1, "Players: " .  TF::AQUA . count($this->players) . TF::RESET . "/" . TF::AQUA . "12");
						Scoreboard::getInstance()->setLine($player, 2, "     ");
						Scoreboard::getInstance()->setLine($player, 3, "Starting in ".TF::GREEN . $this->countdown . "s");
						Scoreboard::getInstance()->setLine($player, 4, "    ");
						Scoreboard::getInstance()->setLine($player, 5, "Map: ".TF::YELLOW.$this->matchInfo["map"]);
						Scoreboard::getInstance()->setLine($player, 6, "   ");
						Scoreboard::getInstance()->setLine($player, 7, "Mode: ".TF::YELLOW."Solo");
						Scoreboard::getInstance()->setLine($player, 8, "  ");
						Scoreboard::getInstance()->setLine($player, 9, Main::getInstance()->config->get("ServerIp"));
						if($this->countdown == 30)
						{
							$player->sendMessage("Game starts in ". TF::YELLOW . $this->countdown . TF::RESET . " seconds");
							$player->getPosition()->getWorld()->addSound($player->getPosition()->add(1, 1, 1), new PopSound);	
						}					
						if($this->countdown == 15)
						{
							$player->sendMessage("Game starts in ". TF::YELLOW . $this->countdown . TF::RESET . " seconds");
							$player->getPosition()->getWorld()->addSound($player->getPosition()->add(1, 1, 1), new PopSound);		
						}
						if($this->countdown == 10)
						{
							$player->sendMessage("Game starts in ". TF::YELLOW . $this->countdown . TF::RESET . " seconds");
							$player->getPosition()->getWorld()->addSound($player->getPosition()->add(1, 1, 1), new PopSound);		
						}
						if($this->countdown == 5 || $this->countdown == 4 || $this->countdown == 3 || $this->countdown == 2 ||$this->countdown == 1)
						{
							$player->getInventory()->clearAll();
							$player->sendTitle($this->countdown);									
							$player->sendMessage("Game starts in ". TF::YELLOW . $this->countdown . TF::RESET . " seconds");
							$player->getPosition()->getWorld()->addSound($player->getPosition()->add(1, 1, 1), new PopSound);
						}
						if($this->countdown == 0)
						{	
							$player->removeTitles();
							if($this->playerSpawn[$this->getPlayerSpawn($player)])
							{
								$explodeSpawn = explode(":", $this->matchInfo[$this->getPlayerSpawn($player)]);
								$x = $explodeSpawn[0];
								$y = $explodeSpawn[1];
								$z = $explodeSpawn[2];
								$player->teleport(new Position($x, $y, $z, Server::getInstance()->getWorldManager()->getWorldByName($this->matchInfo["worldname"])));	
							}						
							$this->phase = "CAGE";	
						}
					}						
				}
				$this->countdown--;
			break;

			case "CAGE":
				foreach($this->players as $player)
				{
					if($player->isOnline())
					{
						Scoreboard::getInstance()->new($player, "SkyBlock", TF::BOLD . TF::GOLD . "SURVIVAL" . TF::YELLOW . "GAME");
						Scoreboard::getInstance()->setLine($player, 0, "      ");
						Scoreboard::getInstance()->setLine($player, 1, "Players: " .  TF::AQUA . count($this->playersLeft) . TF::RESET . "/" . TF::AQUA . "12");
						Scoreboard::getInstance()->setLine($player, 2, "     ");
						Scoreboard::getInstance()->setLine($player, 3, "Match start in ".TF::GREEN . $this->cage . "s");
						Scoreboard::getInstance()->setLine($player, 4, "    ");
						Scoreboard::getInstance()->setLine($player, 5, "Map: ".TF::YELLOW.$this->matchInfo["map"]);
						Scoreboard::getInstance()->setLine($player, 6, "   ");
						Scoreboard::getInstance()->setLine($player, 7, "Mode: ".TF::YELLOW."Solo");
						Scoreboard::getInstance()->setLine($player, 8, "  ");
						Scoreboard::getInstance()->setLine($player, 9, Main::getInstance()->config->get("ServerIp"));
						if($this->cage == 9)
						{
							$player->sendMessage(TF::BOLD . TF::GOLD . "Goal:");
							$player->sendMessage("> The main goal is to eliminate all other players or teams and be the last player or team standing.");
							$player->sendMessage(TF::GREEN . "================================");
							$player->sendMessage(TF::BOLD . TF::RED . "Rules:");
							$player->sendMessage("> Players or teams are not allowed to form alliances with other players or teams.");
							$player->sendMessage("> There may be a grace period at the beginning of the match during which players are not allowed to leave their islands.");
							$player->sendMessage("> This is to prevent early conflicts and give players time to gather resources");
							$player->sendMessage("> Players who are eliminated may be allowed to spectate the remaining players until the end of the match.");
							$player->sendMessage(TF::GREEN . "================================");
							$player->sendMessage(TF::AQUA . "The last player or team standing is declared the winner!!");
							$player->sendMessage("Good luck!!");
						}
						if($this->cage == 7)
						{
							$this->fillChest();	
						}
						if($this->cage == 5 || $this->cage == 4 || $this->cage == 3 || $this->cage == 2 ||$this->cage == 1)
						{
							$player->sendTitle($this->cage);							
							$player->sendMessage("Match start in ". TF::YELLOW . $this->cage . TF::RESET . " seconds");
							$player->getPosition()->getWorld()->addSound($player->getPosition()->add(1, 1, 1), new PopSound);	
						}
						if($this->cage == 0)
						{											
							$this->handlePlayer($player);	
							$this->phase = "START";	
							$player->sendTitle("FIGHT");			
							$player->getPosition()->getWorld()->addSound($player->getPosition()->add(1, 1, 1), new PotionSplashSound);		
						}
					}						
				}
				$this->cage--;
			break;

			case "START":
				foreach($this->players as $player)
				{
					$minutes = floor($this->timer / 60);
					$seconds = floor($this->timer % 60);
					if($player->isOnline())
					{
						Scoreboard::getInstance()->new($player, "SkyBlock", TF::BOLD . TF::GOLD . "SKY" . TF::YELLOW . "WARS");
						Scoreboard::getInstance()->setLine($player, 0, "      ");
						Scoreboard::getInstance()->setLine($player, 1, "Players: " .  TF::AQUA . count($this->playersLeft) . TF::RESET . "/" . TF::AQUA . "12");
						Scoreboard::getInstance()->setLine($player, 2, "     ");
						if($seconds < 10)
						{
							Scoreboard::getInstance()->setLine($player, 3, "Timer: ".TF::YELLOW . $minutes . ":0" . $seconds);
						} else
						{
							Scoreboard::getInstance()->setLine($player, 3, "Timer: ".TF::YELLOW . $minutes . ":" . $seconds);
						}						
						Scoreboard::getInstance()->setLine($player, 4, "    ");
						Scoreboard::getInstance()->setLine($player, 5, "Kills: ".TF::GREEN.$this->playerInfo[strtolower($player->getName())]["kills"]);
						Scoreboard::getInstance()->setLine($player, 6, "   ");
						Scoreboard::getInstance()->setLine($player, 7, "Mode: ".TF::YELLOW."Solo");	
						Scoreboard::getInstance()->setLine($player, 8, "  ");
						Scoreboard::getInstance()->setLine($player, 9, Main::getInstance()->config->get("ServerIp"));				
					}					
				}
				// ketika player udah tinggal 1
				if(count($this->playersLeft) == 1)
				{					
					$this->phase = "FINISHED";
					foreach($this->playersLeft as $lastMan)
					{
						$lastMan->setFlying(true);
						$this->handleReward($lastMan, true);
						$lastMan->sendTitle(TF::GOLD . "VICTORY", "", -1, -10, -1);
						foreach($this->players as $player)
						{
							$player->sendMessage(TF::YELLOW . $lastMan->getName() . TF::RESET . " won the match");
							$lastMan->getPosition()->getWorld()->addSound($lastMan->getPosition()->add(1, 1, 1), new XpCollectSound);
						}
					}
				}
				// ketika gamenya seri
				if($this->timer == 0)
				{
					$this->phase = "FINISHED";
					foreach($this->playersLeft as $lastMan)
					{
						$this->handleReward($lastMan, false);
						$lastMan->setFlying(true);
						$lastMan->sendTitle(TF::GOLD . "TIE", "", -1, -10, -1);
						foreach($this->players as $player)
						{
							$player->sendMessage(TF::YELLOW . $lastMan->getName() . TF::RESET . " has tied the match");
							$lastMan->getPosition()->getWorld()->addSound($lastMan->getPosition()->add(1, 1, 1), new XpCollectSound);
						}
					}
				}
				$this->timer--;
			break;

			case "FINISHED":
				if($this->end >= 0)
				{					
					foreach($this->players as $player)
					{
						if($player->isOnline())
						{
							Scoreboard::getInstance()->new($player, "SkyBlock", TF::BOLD . TF::GOLD . "SKY" . TF::YELLOW . "WARS");
							Scoreboard::getInstance()->setLine($player, 0, "      ");
							Scoreboard::getInstance()->setLine($player, 1, "Players: " .  TF::AQUA . count($this->playersLeft) . TF::RESET . "/" . TF::AQUA . "12");
							Scoreboard::getInstance()->setLine($player, 2, "     ");
							Scoreboard::getInstance()->setLine($player, 3, "Timer: ".TF::YELLOW . "/" . ":" . "/");
							Scoreboard::getInstance()->setLine($player, 4, "    ");
							Scoreboard::getInstance()->setLine($player, 5, "Kills: ".TF::GREEN.$this->playerInfo[strtolower($player->getName())]["kills"]);
							Scoreboard::getInstance()->setLine($player, 6, "   ");
							Scoreboard::getInstance()->setLine($player, 7, "Mode: ".TF::YELLOW."Solo");	
							Scoreboard::getInstance()->setLine($player, 8, "  ");
							Scoreboard::getInstance()->setLine($player, 9, Main::getInstance()->config->get("ServerIp"));
							$player->sendPopup("Teleport you to hub in " . TF::GREEN . $this->end . "s");
						}
					}									
				}												
				if($this->end == 0)
				{												
					foreach($this->players as $player)
					{							
						if($this->end == 0)
						{						
							Scoreboard::getInstance()->checkAndRemoveScoreboard($player);
							$this->removePlayer($player);												
						}
					}
				}
				if($this->end < 0)
				{
					$this->resetArena();
				}																						
				$this->end--;
			break;
		}
	}

	public function getPlayerSpawn(Player $player){
		$playerName = $player->getName();
	    for ($i = 1; $i <= 12; $i++) {
	        $spawnKey = strtolower("spawn-$i");
	        if (isset($this->playerSpawn[$spawnKey]) && $this->playerSpawn[$spawnKey] === $playerName) {
	            return $spawnKey;
	        }
	    }	    
	    return null;
	}	

	public function setPlayerSpawn(Player $player): void{
		$playerName = $player->getName();
		for ($i = 1; $i <= 12; $i++) {
		    $spawnKey = strtolower("spawn-$i");
		    if (!isset($this->playerSpawn[$spawnKey])) {
		        $this->playerSpawn[$spawnKey] = $playerName;
		        return;
		    }
		}
	}

	public function addPlayer(Player $player): void
	{
		if(count($this->players) == 12)
		{
			return;
		}
		$this->players[strtolower($player->getName())] = $player;
		$this->playersLeft[strtolower($player->getName())] = $player;
		$this->setPlayerSpawn($player);	
		$this->playerInfo[strtolower($player->getName())] = ["kills" => 0];
		$player->setGameMode(GameMode::ADVENTURE);
		$player->getInventory()->clearAll();
		$player->getArmorInventory()->clearAll();
		$player->setHealth(20);
		$player->getHungerManager()->setFood(20);
		$player->getInventory()->setItem(8, VanillaBlocks::BED()->asItem()->setCustomName("Lobby"));
		$explodeHubSpawn = explode(":", $this->matchInfo["hubspawn"]);
		$x = $explodeHubSpawn[0];
		$y = $explodeHubSpawn[1];
		$z = $explodeHubSpawn[2];
		$player->teleport(new Position($x, $y, $z, Server::getInstance()->getWorldManager()->getWorldByName($this->matchInfo["hub"])));
		$this->joinBroadcast($player);
		if(count($this->players) >= 2)
		{
			$this->phase = "COUNTDOWN";
		}
	}

	public function removePlayer(Player $player): void
	{
		Scoreboard::getInstance()->checkAndRemoveScoreboard($player);
		unset($this->players[strtolower($player->getName())], $this->playersLeft[strtolower($player->getName())], $this->playerInfo[strtolower($player->getName())]);		
		unset($this->playerSpawn[$this->getPlayerSpawn($player)]);
		$player->setFlying(false);		
		$player->setGameMode(GameMode::SURVIVAL);
		$player->getInventory()->clearAll();
		$player->getArmorInventory()->clearAll();
		$player->setHealth(20);
		$player->getHungerManager()->setFood(20);
		$lobby = Main::getInstance()->config->get("Lobby");
		if(!(Server::getInstance()->getWorldManager()->isWorldGenerated($lobby)))
		{
			$player->teleport(Server::getInstance()->getWorldManager()->getDefaultWorld()->getSafeSpawn());
			$player->sendMessage("Lobby isn't generated yet");
			return;
		}	
		$x = Main::getInstance()->config->get("z");
		$y = Main::getInstance()->config->get("h");
		$z = Main::getInstance()->config->get("z");
		if(!(Server::getInstance()->getWorldManager()->isWorldLoaded($lobby)))
		{
			Server::getInstance()->getWorldManager()->loadWorld($lobby);
			$player->sendMessage("Lobby isn't loaded yet. its reloaded but ill teleport you to default world");
			$player->teleport(Server::getInstance()->getWorldManager()->getDefaultWorld()->getSafeSpawn());
			return;
		}	
		$player->teleport(new Position($x, $y, $z, Server::getInstance()->getWorldManager()->getWorldByName($lobby)));
	}

	public function isInGame(Player $player): bool
	{
		return isset($this->players[strtolower($player->getName())]);
	}

	public function checkArena(): void
	{
		if(count($this->players) < 2)
		{
			if($this->phase == "COUNTDOWN")
			{
				$this->phase = "WAITING";
				$this->countdown = 60;
			}
		}
	}

	public function handleDeath(Player $victim, Player $damager): void
	{			
		unset($this->playersLeft[strtolower($victim->getName())]);
		$victim->setGameMode(GameMode::SPECTATOR);
		$victim->getInventory()->clearAll();
		$victim->getArmorInventory()->clearAll();
		$victim->setHealth(20);
		$victim->getHungerManager()->setFood(20);
		$victim->getInventory()->setItem(0, VanillaItems::COMPASS()->setCustomName("Join"));		
		$victim->getInventory()->setItem(8, VanillaBlocks::BED()->asItem()->setCustomName("Lobby"));		
		$victim->sendTitle(TF::RED . "You died");
		$damager->sendTip("You killed " . TF::GREEN . $victim->getName());
		$damager->getPosition()->getWorld()->addSound($damager->getPosition()->add(1, 1, 1), new PopSound);	
		$this->handleReward($victim, false);
		$this->playerInfo[strtolower($damager->getName())]["kills"] = $this->playerInfo[strtolower($damager->getName())]["kills"] + 1;
		foreach($this->players as $players)
		{
			$players->sendMessage($victim->getName() . TF::RESET. " killed by " .TF::YELLOW . $damager->getName());
		}
	}

	public function handleReward(Player $player, bool $stats = true): void
	{
		$player->sendMessage(TF::GREEN . "================================");
		$player->sendMessage(TF::BOLD . TF::GOLD . "Match Result:");
		$player->sendMessage("> Kills: " . TF::YELLOW . $this->playerInfo[strtolower($player->getName())]["kills"]);
		$cSecond = 900 - $this->timer;
		$minutes = floor($cSecond / 60);
		$seconds = floor($cSecond % 60);
		$player->sendMessage("> Survived time: " . TF::YELLOW . "$minutes minutes $seconds seconds");
		$player->sendMessage(TF::GREEN . "================================");
		$player->sendMessage(TF::BOLD . TF::GOLD . "Match Reward:");
		$rewKills = mt_rand(30,40) * $this->playerInfo[strtolower($player->getName())]["kills"];
		$player->sendMessage("+" . $rewKills . " points - Kills");
		Main::getInstance()->getPlayerData($player)->addPoint($rewKills);
		Main::getInstance()->getPlayerData($player)->addKill($this->playerInfo[strtolower($player->getName())]["kills"]);
		if($stats == true)
		{
			Main::getInstance()->getPlayerData($player)->addPoint(200);
			$player->sendMessage("+200" . " points - Win");
		} else
		{
			Main::getInstance()->getPlayerData($player)->addPoint(20);
			$player->sendMessage("+20" . " points - Lose");
		}	
	}

	public function handlePlayer(Player $player): void
	{
		$player->setGameMode(GameMode::SURVIVAL);
		$player->getInventory()->clearAll();
		$player->getArmorInventory()->clearAll();
		$player->setHealth(20);
		$player->getHungerManager()->setFood(20);		
	}

	public function resetArena(): void
	{		
		$this->phase = "WAITING";		
		$this->countdown = 60;
		$this->cage = 7;
		$this->timer = 900;
		$this->end = 10;
		$this->players = [];
		$this->playerInfo = [];
		$this->playerSpawn = [];
		$this->playersLeft = [];
		$this->emptyChest();
		$this->clearBlocks();		
	}

	public function joinBroadcast(Player $player): void
	{
		foreach($this->players as $players)
		{
			$players->sendMessage(TF::YELLOW . $player->getName() . TF::RESET . " has joined the match [" . TF::AQUA . count($this->players) . "/12" .TF::RESET."]");
		}
	}

	public function fillChest(): void
	{
		$chestPos = ["C-1","C-2","C-3","C-4","C-5","C-6","C-7","C-8","C-9","C-10","C-11","C-12","C-13","C-14","C-15","C-16","C-17","C-18","C-19","C-20","C-21","C-22","C-23","C-24","C-25","C-26","C-27","C-28","C-29","C-30","C-31","C-32","C-33","C-34","C-35","C-36","C-37","C-38","C-39","C-40","C-41"];
		$world = Server::getInstance()->getWorldManager()->getWorldByName($this->matchInfo["worldname"]);
		foreach ($chestPos as $c) {
            if (!empty($this->matchInfo[$c])) {
                $explodeChestSpawn = explode(":", $this->matchInfo[$c]);
                $cx = $explodeChestSpawn[0];
                $cy = $explodeChestSpawn[1];
                $cz = $explodeChestSpawn[2];
                $pos = new Vector3($cx, $cy, $cz);
                $chest = $world->getTile($pos);
                    $loot = [];
                    $world->setBlockAt($pos->getX(), $pos->getY(), $pos->getZ(), VanillaBlocks::CHEST());
                    $items = [
                        ["item" => VanillaItems::IRON_SWORD(), 'chance' => 0.6],
                        ["item" => VanillaItems::IRON_PICKAXE(), 'chance' => 0.6],
                        ["item" => VanillaItems::IRON_HELMET(), 'chance' => 0.4],
                        ["item" => VanillaItems::IRON_CHESTPLATE(), 'chance' => 0.4],
                        ["item" => VanillaItems::IRON_LEGGINGS(), 'chance' => 0.4],
                        ["item" => VanillaItems::IRON_BOOTS(), 'chance' => 0.4],
                        ["item" => VanillaItems::DIAMOND_SWORD(), 'chance' => 0.1],
                        ["item" => VanillaItems::DIAMOND_HELMET(), 'chance' => 0.1],
                        ["item" => VanillaItems::DIAMOND_CHESTPLATE(), 'chance' => 0.1],
                        ["item" => VanillaItems::DIAMOND_LEGGINGS(), 'chance' => 0.1],
                        ["item" => VanillaItems::DIAMOND_BOOTS(), 'chance' => 0.1],
                        ["item" => VanillaItems::GOLDEN_APPLE(), 'chance' => 0.1],
                        ["item" => VanillaItems::STEAK()->setCount(16), 'chance' => 0.8],
                        ["item" => VanillaBlocks::CONCRETE()->asItem()->setCount(64), 'chance' => 1],
                    ];

                    // Randomly select items based on probabilities
                    foreach ($items as $itemData) {
                        if (mt_rand() / mt_getrandmax() < $itemData['chance']) {
                            $loot[] = $itemData["item"];
                        }
                    }

                    // Shuffle the loot to randomize the order
                    shuffle($loot);

                    // Add the loot to random slots in the chest
                    for ($i = 0; $i < count($loot); $i++) {
                        $slot = mt_rand(0, 26);
                        $chest->getInventory()->setItem($slot, $loot[$i]);
                    }
                
            }
        }
	}

	public function clearBlocks(): void
	{
		if($this->placedBlock !== [])
		{
			foreach($this->placedBlock as $placedBlock)
			{
				$explodePlace = explode(":", $placedBlock);
				$x = $explodePlace[0];
				$y = $explodePlace[1];
				$z = $explodePlace[2];
				Server::getInstance()->getWorldManager()->getWorldByName($this->matchInfo["worldname"])->setBlockAt($x, $y, $z, VanillaBlocks::AIR());
			}	
		}			
	}

	public function emptyChest(): void
	{
		$chestPos = ["C-1","C-2","C-3","C-4","C-5","C-6","C-7","C-8","C-9","C-10","C-11","C-12","C-13","C-14","C-15","C-16","C-17","C-18","C-19","C-20","C-21","C-22","C-23","C-24","C-25","C-26","C-27","C-28","C-29","C-30","C-31","C-32","C-33","C-34","C-35","C-36","C-37","C-38","C-39","C-40","C-41"];
		$world = Server::getInstance()->getWorldManager()->getWorldByName($this->matchInfo["worldname"]);
		foreach($chestPos as $c)
		{
			if(!empty($this->matchInfo[$c]))
			{
				$explodeChestSpawn = explode(":", $this->matchInfo[$c]);
				$cx = $explodeChestSpawn[0];
				$cy = $explodeChestSpawn[1];
				$cz = $explodeChestSpawn[2];
				$pos = new Vector3($cx, $cy, $cz);
				$chest = $world->getTile($pos);
				$world->setBlockAt($pos->getX(), $pos->getY(), $pos->getZ(), VanillaBlocks::CHEST());
				$chest->getInventory()->clearAll();
			}
		}
	}

	public function generateArena(string $arenaBy, int $randomNum): void
	{		
		for ($i = 1; $i <= 12; $i++) 
		{
		    $spawnKey = "spawn-$i";
		    $this->matchInfo[$spawnKey] = Main::getInstance()->getGame($arenaBy)->matchInfo[$spawnKey];
		}
		for ($i = 1; $i <= 41; $i++) 
		{
		    $chestKey = "C-$i";
		    $this->matchInfo[$chestKey] = Main::getInstance()->getGame($arenaBy)->matchInfo[$chestKey];
		}
		$this->matchInfo["hubspawn"] = Main::getInstance()->getGame($arenaBy)->matchInfo["hubspawn"];
		$originalWorld = Main::getInstance()->getGame($arenaBy)->matchInfo["worldname"];
		$generateWorld = $this->matchInfo["worldname"];
		mkdir(Server::getInstance()->getDataPath() . "/worlds/" . $generateWorld);
		$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(Server::getInstance()->getDataPath() . "worlds/$originalWorld", FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST);
		/** @var SplFileInfo $fileInfo */
		foreach($files as $fileInfo) {
			if($filePath = $fileInfo->getRealPath()) {
				if($fileInfo->isFile()) {
					@copy($filePath, str_replace($originalWorld, $generateWorld, $filePath));
				} else {
					@mkdir(str_replace($originalWorld, $generateWorld, $filePath));
				}
			}
		}		        
	}
}
