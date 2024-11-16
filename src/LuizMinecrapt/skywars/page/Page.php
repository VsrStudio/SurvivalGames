<?php

namespace LuizMinecrapt\skywars\page;

use pocketmine\Server;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat as TF;
use jojoe77777\FormAPI\{CustomForm, Form, ModalForm, SimpleForm};
use LuizMinecrapt\skywars\Main;

class Page
{
	/** @var array Game */
	public array $games = [];

	/** @var Player */
	private $target;

	public function __construct()
	{
	}

	public function mainPage(Player $player): void
	{
		$form = new SimpleForm(function(Player $player, int $data = null){
			if($data === null)
			{
				return;
			}
			$name = $player->getName();
			switch($data)
			{
				case 0:
					$this->leaderBoardPage($player);
				break;
				case 1:
					$this->playPage($player);			
				break;
				case 2:
					if(Main::getInstance()->getFriend() != null)
					{
						$this->friendPage($player);
					} else{
						$player->sendMessage(Main::TAG . TF::AQUA . "This feature is not available");
						if($player->isOp())
						{
							$player->sendMessage(Main::TAG . TF::AQUA . "Get the depend on SHFPS youtube");							
						}
					}
				break;
			}
		});
		$form->setTitle(TF::BOLD.TF::GOLD."SKY".TF::AQUA . "WARS");
		$form->setContent("Hello " . TF::YELLOW . $player->getName() . TF::WHITE . ", Welcome" .TF::BLUE . " to " .TF::WHITE . "SkyWars, the place of action-packed adventures" . TF::GOLD . " and " .TF::WHITE . "excitement! Come " . TF::BLUE . " on" .TF::WHITE . "," . TF::BLUE . " join in" .TF::WHITE . TF::GOLD . " and ". TF::WHITE . " experience the thrill" . TF::BLUE . " of " .TF::WHITE . "soaring high" . TF::GOLD . " and " .TF::WHITE . "collecting" . TF::BLUE . " as " .TF::WHITE . "many points" . TF::BLUE . " as ". TF::WHITE . "you can. Enjoy your time" . TF::GOLD . " and " .TF::WHITE . " may you become the ruler" . TF::BLUE . " of ". TF::WHITE . "the skies!");
		$form->addButton(TF::AQUA."Leaderboard\n".TF::RESET.TF::WHITE."»» Play and survive in the sky",0, "textures/ui/world_glyph_color_2x_black_outline");
		$form->addButton(TF::AQUA."Play\n".TF::RESET.TF::WHITE."»» Play and survive in the sky",0, "textures/items/iron_axe");
		$form->addButton(TF:: AQUA . "Friend\n" . TF::WHITE. "»» Play with your friends" , 0, "textures/ui/FriendsIcon");
		$form->addButton(TF::RED."Exit");
		$form->sendToPlayer($player);
	}

	public function leaderBoardPage(Player $player): void
	{
		$form = new CustomForm(function(Player $player, array $data = null){
			if($data === null)
			{
				return;
			}
			$this->mainPage($player);		
		});
		$form->setTitle(TF::BOLD.TF::GOLD."SKY".TF::AQUA . "WARS");
		$playersData = Main::getInstance()->getPlayersData();
		$arrayPoints = array();
		foreach ($playersData as $data) {						
		    $arrayPoints[$data->getName()] = $data->getPoint();
		}
		arsort($arrayPoints);
		$sortedKeys = array_keys($arrayPoints);
		$findMyRank = array_search($player->getName(), $sortedKeys);	
		$form->addLabel("== [YOUR STATS] ==");		
		$form->addLabel("> Your Rank: " . TF::GREEN . $findMyRank + 1);
		$myData = Main::getInstance()->getPlayerData($player);
		$form->addLabel("> Your Points: " . TF::AQUA . $myData->getPoint());
		$form->addLabel("> Your Kills: " . TF::GOLD . $myData->getKill());
		$form->addLabel("  ");
		$form->addLabel("== [SKYWARS LEADERBOARD] ==");
		$form->addLabel("Top Points:");
		if(isset($sortedKeys[0]))
		{
			$form->addLabel(TF::GRAY . "<" .TF::BOLD .  TF::YELLOW . "1" . TF::RESET . TF::BOLD . TF::GRAY . "> ". TF::YELLOW . $sortedKeys[0] . TF::RESET . TF::GRAY . ": ".$arrayPoints[$sortedKeys[0]]." Points ⦁"); //1
		}
		if(isset($sortedKeys[1]))
		{
			$form->addLabel(TF::GRAY . "<" .TF::BOLD .  TF::WHITE . "2" . TF::RESET . TF::GRAY . "> " . TF::WHITE . $sortedKeys[1] . TF::GRAY . ": ".$arrayPoints[$sortedKeys[1]]." Points ⦁"); //2
		}
		if(isset($sortedKeys[2]))
		{
			$form->addLabel(TF::GRAY . "<" .TF::BOLD .  TF::GOLD . "3" . TF::RESET . TF::GRAY . "> " . TF::GOLD . $sortedKeys[2]. TF::GRAY . ": ".$arrayPoints[$sortedKeys[2]]." Points ⦁"); //3
		}				
		for($i = 4; $i <= 10; $i++)
		{
			if(isset($sortedKeys[$i]))
			{
				$form->addLabel(TF::GRAY . "<" .TF::BOLD .  TF::GRAY . "$i" . TF::RESET . TF::GRAY . "> " . TF::GRAY . $sortedKeys[$i] . ": ".$arrayPoints[$sortedKeys[$i]]." Points ⦁"); //1..10
			}
		}
		$form->sendToPlayer($player);
	}

	public function playPage(Player $player): void
	{
		$form = new SimpleForm(function(Player $player, int $data = null){
			if($data === null)
			{
				return;
			}
			$name = $player->getName();
			switch($data)
			{
				case 0:
					foreach(Main::getInstance()->getGames() as $game)
					{
						if(Main::getInstance()->getPlayer($player))
						{
							$player->sendMessage(Main::TAG. "You already in matches");
							return;
						}
						if($game->isStatus(true))
						{
							$game->addPlayer($player);
							return;
						} else
						{
							$player->sendMessage(Main::TAG . "No arena available, maybe try again later");
						}
						return;
					}					
				break;

				case 1:
					$this->mainPage($player);
				break;
			}
		});
		$form->setTitle(TF::BOLD.TF::GOLD."SKY".TF::AQUA . "WARS");
		$form->addButton(TF::AQUA."Join Random\n".TF::RESET.TF::WHITE."»» Join random and survive in the sky",0, "textures/items/totem");
		//$form->addButton(TF::AQUA."Join\n".TF::RESET.TF::WHITE."»» Join random and survive in the sky",0, "textures/items/compass_item");
		$form->addButton(TF::RED."Back\n", 0, "textures/items/compass_item");
		$form->sendToPlayer($player);
	}

	/*public function joinPage(Player $player): void
	{
		$form = new SimpleForm(function(Player $player, int $data = null){
			if($data === null)
			{
				return;
			}
			$name = $player->getName();
			if($p = Main::getInstance()->getPlayer($player))
			{
				$player->sendMessage(Main::TAG. "You already in matches");
				return;
			}
			if(Main::getInstance()->getArenaByInt($data) == null)
			{
				$this->playPage($player);
				return;
			}
			if($game = Main::getInstance()->getArenaByInt($data))
			{				
				if($game->getPhase() == "OFFLINE")
				{
					$this->joinPage($player);
				} else if($game->getPhase() == "WAITING" || $game->getPhase() == "COUNTDOWN")
				{								
					if($game->isStatus(true))
					{
						$game->addPlayer($player);
						return;
					} else
					{
						$player->sendMessage(Main::TAG . "No arena available, maybe try again later");
					}
				} else if($game->getPhase() == "CAGE" || $game->getPhase() == "START" || $game->getPhase() == "FINISHED")
				{
					$form->addButton($game->getMap() . " | " . $game->countArenaPlayers() . "\n" . "Started");
				}
			}
		});
		$form->setTitle(TF::BOLD.TF::YELLOW."SKYLAND");
		foreach(Main::getInstance()->getGames() as $game)
		{
			if($game->getPhase() == "OFFLINE")
			{
				$form->addButton(TF::AQUA . $game->getMap() . TF::WHITE . " | " . TF::YELLOW .$game->countPlayers() . "/" . $game->getMaxPlayers() . "\n" . TF::RED . "OFFLINE");
			} else if($game->getPhase() == "WAITING" || $game->getPhase() == "COUNTDOWN")
			{
				$form->addButton(TF::AQUA . $game->getMap() . TF::WHITE . " | " . TF::YELLOW .$game->countPlayers() . "/" . $game->getMaxPlayers() .  "\n" .TF::GOLD. "Waiting for players");
			} else if($game->getPhase() == "CAGE" || $game->getPhase() == "START" || $game->getPhase() == "FINISHED")
			{
				$form->addButton(TF::AQUA . $game->getMap() . TF::WHITE . " | " . TF::YELLOW .$game->countPlayers() . "/" . $game->getMaxPlayers() . "\n" .TF::GREEN. "Started");
			}			
		}
		$form->addButton(TF::RED."Back");
		$form->sendToPlayer($player);
	}*/

	public function friendPage(Player $player): void
	{
		$form = new SimpleForm(function(Player $player, int $data = null){
			if($data === null)
			{
				return;
			}
			$p = Main::getInstance()->getFriend()->getPlayer($player->getName());
			$inboxList = $p->getFriends();
			if($inboxList == false)
			{
				$this->mainPage($player);
				return;
			}
			$inb = array_values($inboxList);
			if(!isset($inb[$data]))
			{
				$this->mainPage($player);
				return;
			}
			$targetName = $inb[$data];  
			if($target = Server::getInstance()->getPlayerExact($targetName))
			{
				$this->onFriend($player, $target);
			} else
			{
				$player->sendMessage(Main::TAG . TF::RED . "Player is offline");
				return;
			}
		});
		$form->setTitle(TF::BOLD.TF::GOLD."SKY".TF::AQUA . "WARS");
		$getFriend = Main::getInstance()->getFriend()->getPlayer($player->getName());		
		if($getFriend->isFriendEmpty() === true)
		{
			$ark = array_values($getFriend->getFriends());
			$form->setContent("You have " .  $getFriend->countFriends() . " friends in your friend list, meet more friends!");
			$ark = array_values($getFriend->getFriends());
			foreach($ark as $friend)
			{
				if(Server::getInstance()->getPlayerExact($friend))
				{
					$form->addButton(TF::YELLOW . "$friend\n". TF::GREEN . "»» Online", 0, "textures/ui/friend1_black_outline_2x");
				} else
				{
					$form->addButton(TF::YELLOW . "$friend\n". TF::RED . "»» Offline", 0, "textures/ui/friend_glyph_desaturated");
				}			
			}
		} else
		{
			$form->setContent(TF::BOLD . "»» " . TF::RED . "NO FRIENDS AVAILABLE");
		}
		$form->addButton(TF::RED."Back\n", 0, "textures/items/compass_item");
		$form->sendToPlayer($player);
	}

	public function onFriend(Player $player, Player $target): void
	{
		$this->target = $target;
		$form = new SimpleForm(function(Player $player, int $data = null){
			if($data === null)
			{
				return;
			}
			switch($data)
			{
				case 0:
					if(Main::getInstance()->getPlayer($player))
					{
						$player->sendMessage(Main::TAG. "You already in matches");
						return;
					}
					$game = Main::getInstance()->getPlayer($this->target);
					if($game == null)
					{
						$player->sendMessage(Main::TAG . TF::YELLOW . $this->target->getName() . TF::RESET . " doesn't join any matches");
						return;
					}
					if($game->isStatus(true))
					{
						$game->addPlayer($player);
						return;
					} else
					{
						$player->sendMessage(Main::TAG . "Arena is started or not available, try again later");
					}
				break;

				case 1:
					$this->mainPage($player);
				break;
			}
		});
		$form->setTitle(TF::BOLD.TF::GOLD."SKY".TF::AQUA . "WARS");
		$fgame = Main::getInstance()->getPlayer($target);
		if($fgame !== null)
		{
			if($fgame->getPhase() == "OFFLINE")
			{
				$form->addButton(TF::AQUA . $fgame->getMap() . TF::WHITE . " | " . TF::YELLOW .$fgame->countPlayers() . "/" . $fgame->getMaxPlayers() . "\n" . TF::RED . "OFFLINE", 0, "textures/ui/friend_glyph_desaturated");
			} else if($fgame->getPhase() == "WAITING" || $fgame->getPhase() == "COUNTDOWN")
			{
				$form->addButton(TF::AQUA . $fgame->getMap() . TF::WHITE . " | " . TF::YELLOW .$fgame->countPlayers() . "/" . $fgame->getMaxPlayers() .  "\n" .TF::GOLD. "Waiting for players", 0, "textures/ui/friend_glyph_desaturated");
			} else if($fgame->getPhase() == "CAGE" || $fgame->getPhase() == "START" || $fgame->getPhase() == "FINISHED")
			{
				$form->addButton(TF::AQUA . $fgame->getMap() . TF::WHITE . " | " . TF::YELLOW .$fgame->countPlayers() . "/" . $fgame->getMaxPlayers() . "\n" .TF::GREEN. "Started", 0, "textures/ui/friend1_black_outline_2x");
			}	
		} else
		{	
			$form->addButton(TF::RED . "-- Not Playing --");
		}			
		$form->addButton(TF::RED."Back\n", 0, "textures/items/compass_item");
		$form->sendToPlayer($player);
	}

	public function addPage(Player $player): void
	{
		$form = new CustomForm(function(Player $player, array $data = null){
			if($data === null)
			{
				return;
			}
			$name = $player->getName();
			if($data[0] === null)
			{
				$player->sendMessage(Main::TAG . "You must fill all of it");
				return;
			}

			if($data[1] === null)
			{
				$player->sendMessage(Main::TAG . "You must fill all of it");
				return;
			}

			if($data[2] === null)
			{
				$player->sendMessage(Main::TAG . "You must fill all of it");
				return;
			}

			if($data[3] === null)
			{
				$player->sendMessage(Main::TAG . "You must fill all of it");
				return;
			}
			Main::getInstance()->createArena($data[0], $data[3], $data[2], $data[1]);
			$game = Main::getInstance()->getGame($data[0]);
			$game->editArena($player);
			if(!(Server::getInstance()->getWorldManager()->isWorldLoaded($data[3])))
			{
				Server::getInstance()->getWorldManager()->loadWorld($data[3]);
			} else{
				$player->teleport(Server::getInstance()->getWorldManager()->getWorldByName($data[3])->getSafeSpawn());

			}
			if(Server::getInstance()->getWorldManager()->isWorldGenerated($data[3]))
			{
				$player->teleport(Server::getInstance()->getWorldManager()->getWorldByName($data[3])->getSafeSpawn());
			}			
			$player->sendMessage(Main::TAG . "Skywars name: $data[0]");
			$player->sendMessage(Main::TAG . "Map: $data[1]");
			$player->sendMessage(Main::TAG . "Max player: 12");
			$player->sendMessage(Main::TAG . "Max chest: 41");
			$player->sendMessage(Main::TAG . "Hub: $data[2]");
			$player->sendMessage(Main::TAG . "World name: $data[3]");
			$player->sendMessage(Main::TAG . "Successfully created arena with the name $data[0]");
		});
		$form->setTitle(TF::BOLD.TF::GOLD."SKY".TF::AQUA . "WARS");
		$form->addInput("Skywars name:");
		$form->addInput("Map:");		
		$form->addInput("Hub name:");
		$form->addInput("World name:");
		$form->sendToPlayer($player);
	}
}