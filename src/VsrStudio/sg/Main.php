<?php

declare(strict_types=1);

namespace VsrStudio\sg;

use pocketmine\Server;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as TF;
use VsrStudio\sg\event\EventListener;
use VsrStudio\sg\task\SkywarsTask;
use VsrStudio\sg\manager\Game;
use VsrStudio\sg\manager\Player as PlayerData;
use VsrStudio\sg\page\Page;
use LuizMinecrapt\FriendUI\FriendUI;

class Main extends PluginBase
{
	/** @var Config */
	public $config;

	/** @var FriendUI */
	public $friend;

	/** @var return Page */
	public $page;

	/** @var Game[] */
	private array $game = [];

	/** @var Player[] */
	private array $playerData = [];

	/** @static instance Main */
	private static Main $instance;	

	/** @static string */
	public const TAG = TF::GRAY."[SurvivalGame] " .TF::RESET;

	/** @static int */
	public const CONFIG_VERSION = 3;

	public function onEnable(): void
	{
		Main::$instance = $this;

		// Event
		$this->getServer()->getPluginManager()->registerEvents(new EventListener($this),$this);

		// Class
		$this->page = new Page();
		if($this->getServer()->getPluginManager()->getPlugin("FriendUI") == null)
		{
			$this->getLogger()->critical("Depend is missing \"FriendUI\" download now on SHFPS youtube channel");
		}

		// Config				
		@mkdir($this->getDataFolder() . "old/");
		$this->saveResource("config.yml");
		$this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
		if(!file_exists($this->getDataFolder() . "config.yml"))
		{
			$this->getLogger()->critical("Creating config for you ...");
			$this->saveResource("config.yml");
		}
		if($this->config->get("Config-Version") !== self::CONFIG_VERSION){
			if(file_exists($this->getDataFolder() . "config.yml"))
			{
				rename($this->getDataFolder() . "config.yml", $this->getDataFolder() . "old/" . $this->config->get("Config-Version") . "_config.yml");
			}			
			$this->getLogger()->critical(TF::RED . "Your config version is old, making latest version");
			$this->saveResource("config.yml");
		}
		@mkdir($this->getDataFolder() . "arenas/");
		@mkdir($this->getDataFolder() . "players/");
        foreach (glob($this->getDataFolder() . "arenas/*.yml") as $location) {
		    $fileContents = file_get_contents($location);
		    $yaml = yaml_parse($fileContents);	    
		    $worldName = explode(":", $yaml["worldname"]);
		    if($worldName !== null)
		    {
		    	$this->getServer()->getWorldManager()->loadWorld($yaml["worldname"]);
		    }
		    $this->game[$yaml["arenaname"]] = new Game(
		    	$yaml["arenaname"],
		    	$yaml["worldname"], 		    	
		    	$yaml["hub"],
		    	$yaml["hubspawn"],
		    	$yaml["maxplayers"],
		    	$yaml["spawn-1"],
		    	$yaml["spawn-2"],
		    	$yaml["spawn-3"],
		    	$yaml["spawn-4"],
		    	$yaml["spawn-5"],
		    	$yaml["spawn-6"],
		    	$yaml["spawn-7"],
		    	$yaml["spawn-8"],
		    	$yaml["spawn-9"],
		    	$yaml["spawn-10"],
		    	$yaml["spawn-11"],
		    	$yaml["spawn-12"],
				$yaml["map"],		    	
				$yaml["maxchest"],
				$yaml["C-1"],
		    	$yaml["C-2"],
		    	$yaml["C-3"],
		    	$yaml["C-4"],
		    	$yaml["C-5"],
		    	$yaml["C-6"],
		    	$yaml["C-7"],
		    	$yaml["C-8"],
		    	$yaml["C-9"],
		    	$yaml["C-10"],
		    	$yaml["C-11"],
		    	$yaml["C-12"],
		    	$yaml["C-13"],
		    	$yaml["C-14"],
		    	$yaml["C-15"],
		    	$yaml["C-16"],
		    	$yaml["C-17"],
		    	$yaml["C-18"],
		    	$yaml["C-19"],
		    	$yaml["C-20"],
		    	$yaml["C-21"],
		    	$yaml["C-22"],
		    	$yaml["C-23"],
		    	$yaml["C-24"],
		    	$yaml["C-25"],
		    	$yaml["C-26"],
		    	$yaml["C-27"],
		    	$yaml["C-28"],
		    	$yaml["C-29"],
		    	$yaml["C-30"],
		    	$yaml["C-31"],
		    	$yaml["C-32"],
		    	$yaml["C-33"],
		    	$yaml["C-34"],
		    	$yaml["C-35"],
		    	$yaml["C-36"],
		    	$yaml["C-37"],
		    	$yaml["C-38"],
		    	$yaml["C-39"],
		    	$yaml["C-40"],
		    	$yaml["C-41"]);		    
		}
		foreach(glob($this->getDataFolder() . "players/*.yml") as $playersData)
		{
			$fileContentsData = file_get_contents($playersData);
		    $yamlData = yaml_parse($fileContentsData);	
		    $this->playerData[$yamlData["name"]] = new PlayerData($yamlData["name"], $yamlData["points"], $yamlData["kills"]);
		}				
	}

	public function createArena(string $arenaName, string $worldName, string $hub, string $map): bool
	{
		$this->game[$arenaName] = new Game(arenaName: $arenaName, worldName: $worldName, hub: $hub, map: $map);
		return true;
	}

	/*public function getArenaByInt(int $int)
	{
		foreach($this->getGames() as $game)
		{
			$g = array($game->getArenaName());
			if(isset($g[$int]))
			{
				return $this->getGame($g[$int]) || $g = []; 
			} else
			{
				return null;
			}
		}
	}*/

	public function getGame(string $arenaName): ?Game
	{
		return $this->game[$arenaName] ?? null;
	}

	public function getGames(): array
	{
		return $this->game;
	}

	public function getPlayer(Player $player): ?Game
	{
		foreach($this->getGames() as $game)
		{
			if($game->isInGame($player))
			{
				return $game;
			}
		}
		return null;
	}

	public function createPlayerData(string $playerName, ?int $points = 0, ?int $kills = 0): bool
	{
		$this->playerData[$playerName] = new PlayerData($playerName, $points, $kills);
		return true;
	}

	public function getPlayerData(?Player $player): ?PlayerData
	{

		$playerName = $player->getName();
		return $this->playerData[$playerName] ?? null;	
	}

	public function getPlayersData(): array
	{
		return $this->playerData;
	}

	public function getFriend(): ?FriendUI
	{
		if($this->getServer()->getPluginManager()->getPlugin("FriendUI"))
		{
			return FriendUI::getInstance();
		}
		return null;
	}

	public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args): bool
	{
		$listCmd = ["/survivalgame add", 
					"/survivalgame reload",
					"/survivalgame list",
					"/survivalgame setlobby",
					"/survivalgame edit <arenaname>",					
					"/survivalgame search <arenaname>",
					"/survivalgame generate <arenaname>",
					"/survivalgame remove <arenaname>",
					];
		switch($cmd->getName())
		{
			case "survivalgame":				
				$name = $sender->getName();
				if(isset($args[0]))
				{
					$subcommand = strtolower($args[0]);
					switch($subcommand)
					{						
						case "help":
							if($this->getServer()->isOp($sender->getName()))
							{
								foreach($listCmd as $smd)
								{
									$sender->sendMessage(Main::TAG . $smd);
								}
							}								
						break;
						case "add":		
							if($this->getServer()->isOp($sender->getName()))
							{
								$this->page->addPage($sender);								
							}					
						break;

						case "edit":
							if($this->getServer()->isOp($sender->getName()))
							{
								if(empty($args[1]))
								{
									$sender->sendMessage(Main::TAG . "/survivalgame edit <arenaname>");
									return true;
								}
								if($this->getGame($args[1]) == null)
								{
									$sender->sendMessage(Main::TAG . "$args[1] not founded");
									return true;
								}
								$game = $this->getGame($args[1]);
								$game->editArena($sender);
								if(!($this->getServer()->getWorldManager()->isWorldLoaded($game->getWorld())))
								{
									$this->getServer()->getWorldManager()->loadWorld($game->getWorld());
								} else{
									$sender->teleport($this->getServer()->getWorldManager()->getWorldByName($game->getWorld())->getSafeSpawn());

								}
								if($this->getServer()->getWorldManager()->isWorldGenerated($game->getWorld()))
								{
									$sender->teleport($this->getServer()->getWorldManager()->getWorldByName($game->getWorld())->getSafeSpawn());
								}	
							}
						break;

						case "reload":
							if($this->getServer()->isOp($sender->getName()))
							{
								foreach($this->getGames() as $game)
								{
									$game->reload();
								}
								foreach($this->getPlayersData() as $data)
								{
									$data->reload();
								}
								$sender->sendMessage(Main::TAG . "Successfully reloaded");
							}						
						break;

						case "search":
							if($this->getServer()->isOp($sender->getName()))
							{
								if(empty($args[1]))
								{
									$sender->sendMessage(Main::TAG . "/survivalgame search <arenaname>");
									return true;
								}
								if($this->getGame($args[1]) == null)
								{
									$sender->sendMessage(Main::TAG . "$args[1] not founded");
									return true;
								}
								$game = $this->getGame($args[1]);
								$sender->sendMessage(Main::TAG . "survivalgame name: $args[1]");
								$sender->sendMessage(Main::TAG . "Map: " . $game->getMap());
								$sender->sendMessage(Main::TAG . "Max player: 12");
								$sender->sendMessage(Main::TAG . "Hub: " . $game->getHubWorld());
								$sender->sendMessage(Main::TAG . "World name: " . $game->getWorld());		
								if($game->isValid() == true)
								{
									$sender->sendMessage(Main::TAG . "Ready");
								} else
								{
									$sender->sendMessage(Main::TAG . "Not ready");								
								}	
							}									
						break;

						case "generate":
							if($this->getServer()->isOp($sender->getName()))
							{
								if(empty($args[1]))
								{
									$sender->sendMessage(Main::TAG . "/survivalgame generate <arenaname>");
									return true;
								}
								if($this->getGame($args[1]) == null)
								{
									$sender->sendMessage(Main::TAG . "$args[1] not founded");
									return true;
								}
								$randomNum = mt_rand(1, 5000);
								$this->createArena($this->getGame($args[1])->matchInfo["arenaname"] . "_$randomNum", $this->getGame($args[1])->matchInfo["worldname"] . "_$randomNum", $this->getGame($args[1])->matchInfo["hub"] . "_$randomNum", $this->getGame($args[1])->matchInfo["map"]);
								$this->getGame($args[1] . "_$randomNum")->generateArena($args[1], $randomNum);
								$sender->sendMessage(Main::TAG . "Successfully generated new arena $args[1]_$randomNum");
							}		
						break;

						case "remove":
							if($this->getServer()->isOp($sender->getName()))
							{
								if(empty($args[1]))
								{
									$sender->sendMessage(Main::TAG . "/survivalgame remove <arenaname>");
									return true;
								}
								if($this->getGame("$args[1]") == null)
								{
									$sender->sendMessage(Main::TAG . "$args[1] not founded");
									return true;
								}
								if(file_exists($this->getDataFolder() . "arenas/$args[1].yml"))
								{
									unlink($this->getDataFolder() . "arenas/$args[1].yml");
									$sender->sendMessage(Main::TAG . "Successfully removed $args[1]");
								}	
							}																					
						break;	

						case "list":
							if($this->getServer()->isOp($sender->getName()))
							{
								foreach($this->getGames() as $game)
								{
									$sender->sendMessage(Main::TAG . $game->matchInfo["arenaname"]);
								}	
							}								
						break;

						case "setlobby":
							$pos = $sender->getPosition();
							$x = $pos->getX();
							$y = $pos->getY();
							$z = $pos->getZ();
							$world = $pos->getWorld()->getFolderName();
							$this->config->set("x", "$x");
							$this->config->set("h", "$y");
							$this->config->set("z", "$z");
							$this->config->set("Lobby", "$world");
							$this->config->save();
							$sender->sendMessage(Main::TAG . "Lobby spawn moved to $world, $x, $y, $z");
						break;						
					}
				} else
				{
					$this->page->mainPage($sender);
				}
			break;
		}
		return true;
	}

	public function onDisable(): void
	{
		// save data
		foreach($this->getPlayersData() as $data)
		{
			$data->reload();
		}
		// avoid crash when generate world
		foreach($this->getGames() as $game)
		{
			$game->emptyChest();
			$game->clearBlocks();
		}

	}

	public static function getInstance(): Main
	{
		return Main::$instance;
	}
}
