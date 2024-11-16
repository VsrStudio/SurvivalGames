<?php

namespace LuizMinecrapt\skywars\task;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat as TF;
use LuizMinecrapt\skywars\Main;

class ItemHubTask extends Task
{
	/** @var Player */
	private $player;

	/** @var int */
	private int $cdLobby = 5;
	private int $cdJoin = 5;

	/** 
	 * @param Player $player 
	 */
	public function __construct(Player $player)
	{
		$this->player = $player;
	}

	public function onRun(): void
	{
		if(!($this->player instanceof Player))
		{
			$this->getHandler()->cancel();
			return;
		}
		if($this->player->isOnline())
		{
			$itemInHand = $this->player->getInventory()->getItemInHand()->getName();
			if($game = Main::getInstance()->getPlayer($this->player))
			{			
				if($game->getPhase() == "START" || $game->getPhase() == "FINISHED")
				{
					if($itemInHand == "Join")
					{
						if($this->cdJoin == 5 || $this->cdJoin == 4 || $this->cdJoin == 3 || $this->cdJoin == 2 ||$this->cdJoin == 1)
						{
							$this->player->sendMessage("Join a new match in " . TF::YELLOW . $this->cdJoin);
						}
						if($this->cdJoin == 0)
						{
							$this->cdJoin = 5;
							if($myGame = Main::getInstance()->getPlayer($this->player))
							{
								$myGame->removePlayer($this->player);
							}
							foreach(Main::getInstance()->getGames() as $game)
							{
								if(Main::getInstance()->getPlayer($this->player))
								{
									$this->player->sendMessage(Main::TAG. "You already in matches");
									return;
								}
								if($game->isStatus(true))
								{
									$game->addPlayer($this->player);
									return;
								} 
							}

						}	
						$this->cdJoin--;					
					} else
					{
						$this->cdJoin = 5;
					}
					if($itemInHand == "Lobby")
					{
						if($this->cdLobby == 5 || $this->cdLobby == 4 || $this->cdLobby == 3 || $this->cdLobby == 2 ||$this->cdLobby == 1)
						{
							$this->player->sendMessage("Teleporting you to lobby in " . TF::YELLOW . $this->cdLobby);
						}
						if($this->cdLobby == 0)
						{
							$this->cdLobby = 5;
							$game->removePlayer($this->player);
							$game->checkArena();
						}
						$this->cdLobby--;
					}else
					{
						$this->cdLobby = 5;
					}						
				}				
			}				
		}else
		{
			$this->getHandler()->cancel();
		}
	}
}