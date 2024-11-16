<?php

namespace VsrStudio\sg\event;

use pocketmine\Server;
use pocketmine\block\Chest;
use pocketmine\block\VanillaBlocks;
use pocketmine\event\Listener;
use pocketmine\event\block\BlockEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\world\ChunkLoadEvent;
use pocketmine\item\VanillaItems;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player as P;
use pocketmine\player\GameMode;
use pocketmine\utils\TextFormat as TF;
use pocketmine\math\Vector3;
use pocketmine\world\Position;
use VsrStudio\sg\Main;
use VsrStudio\sg\task\ItemHubTask;

class EventListener implements Listener
{
	/** @var Main */
	private $plugin;

	public function __construct(Main $plugin)
	{
		$this->plugin = $plugin;
	}
 
	public function onJoin(PlayerJoinEvent $event): void
	{
		$player = $event->getPlayer();	
		if(!file_exists(Main::getInstance()->getDataFolder() . "players/" . $player->getName() . ".yml"))
		{
			Main::getInstance()->createPlayerData($player->getName());
		}
		Main::getInstance()->getScheduler()->scheduleRepeatingTask(new ItemHubTask($player), 20);
	}

	public function onQuit(PlayerQuitEvent $event): void
	{
		$player = $event->getPlayer();
		if($game = Main::getInstance()->getPlayer($player))
		{
			$data = Main::getInstance()->getPlayerData($player);
			$data->reload();
			if($game->phase == "WAITING" || $game->phase == "COUNTDOWN" || $game->phase == "CAGE" || $game->phase == "START" || $game->phase == "FINISHED")
			{
				$game->removePlayer($player);
				$game->checkArena();
			}
		}
	}

	public function onBreak(BlockBreakEvent $event)
	{
		$player = $event->getPlayer();
		$getWandName = $player->getInventory()->getItemInHand()->getName();	
		$explodeWand = explode(":", $getWandName);
		$arena = Main::getInstance()->getGame($explodeWand[0]);
		if(!($arena == null))
		{				
			$event->cancel();
		}
		if($game = Main::getInstance()->getPlayer($player))
		{
			if($game->phase == "WAITING" || $game->phase == "COUNTDOWN" || $game->phase == "CAGE" || $game->phase == "FINISHED")
			{
				$event->cancel();
				return;
			}
			$pos = $event->getBlock()->getPosition()->asVector3();
			$x = $pos->getX();
			$y = $pos->getY();
			$z = $pos->getZ();
			if(!isset($game->placedBlock["$x:$y:$z"]))
			{
				$player->sendMessage(TF::RED . "You can't break this block");
				$event->cancel();
				return;
			}
		}	
	}

	public function onPlace(BlockPlaceEvent $event)
	{
		$player = $event->getPlayer();
		$getWandName = $player->getInventory()->getItemInHand()->getName();	
		if($game = Main::getInstance()->getPlayer($player))
		{
			if($game->phase == "WAITING" || $game->phase == "COUNTDOWN" || $game->phase == "CAGE" || $game->phase == "FINISHED")
			{
				$player->sendMessage(TF::RED . "You can't place block here");
				$event->cancel();
				return;
			}
			$pos = $event->getTransaction()->getBlocks()->current();
			$x = $pos[0];
			$y = $pos[1];
			$z = $pos[2];
			$game->placedBlock["$x:$y:$z"] = "$x:$y:$z";
		}
	}	

	public function onDeath(EntityDamageEvent $event): void
	{
		$player = $event->getEntity();
		if(!($player instanceof P))
		{
			return;
		}
		
		if($game = Main::getInstance()->getPlayer($player))
		{
			if($event->getCause() == EntityDamageEvent::CAUSE_FALL)
			{
				$event->cancel();
				return;
			}						
			if($game->phase == "WAITING" || $game->phase == "COUNTDOWN" || $game->phase == "CAGE" || $game->phase == "FINISHED")
			{
				$event->cancel();
				return;
			}
			if($game->phase == "START")
			{
				if($event->getCause() == EntityDamageEvent::CAUSE_VOID)
				{
					$event->cancel();
					unset($game->playersLeft[strtolower($player->getName())]);				
					$player->setGameMode(GameMode::SPECTATOR);
					$player->getInventory()->clearAll();
					$player->getArmorInventory()->clearAll();
					$player->setHealth(20);
					$player->getHungerManager()->setFood(20);
					$player->getInventory()->setItem(8, VanillaBlocks::BED()->asItem()->setCustomName("Lobby"));		
					$player->getInventory()->setItem(1, VanillaItems::COMPASS()->setCustomName("Join"));		
					$player->sendTitle(TF::RED . "You died");
					$player->sendMessage(Main::TAG . Main::TAG . $player->getName() . TF::RESET. " died by falling into the " .TF::YELLOW . "void");
					foreach($game->playersLeft as $pl)
					{
						$player->teleport(Position::fromObject($pl->getPosition()->asPosition(), Server::getInstance()->getWorldManager()->getWorldByName($pl->getPosition()->getWorld()->getFolderName())));
					}
					return;
				}
				$damager = $event->getDamager();			
				if($event->getBaseDamage() >= $player->getHealth())
				{									
					$game->handleDeath($player, $damager);
					$event->cancel();
				}		
			}					
		}
	}

	public function onInteract(PlayerInteractEvent $event)
	{
		$player = $event->getPlayer();
		$getWandName = $player->getInventory()->getItemInHand()->getName();	
		if($game = Main::getInstance()->getPlayer($player))
		{
			if($game->phase == "WAITING" || $game->phase == "COUNTDOWN" || $game->phase == "CAGE")
			{
				if($player->getInventory()->getItemInHand()->getCustomName() === "Lobby")
				{
					$game->removePlayer($player);					
					$game->checkArena();
					$player->sendMessage(Main::TAG . "You left the match");
					$event->cancel();
					return;
				}	
				$event->cancel();			
			}			
		}			
		$explodeWand = explode(":", $getWandName);
		$arena = Main::getInstance()->getGame($explodeWand[0]);
		if(!($arena == null))
		{				
			if($explodeWand[1] === "PLAYER-SPAWN")
			{				
				if(!empty($arena->matchInfo["spawn-12"]))
				{
					$player->sendMessage(Main::TAG . "All spawn have already been setted!");
					return;
				}
				$pos = $event->getBlock()->getPosition()->asVector3();
				$x = $pos->getX();
				$y = $pos->getY();
				$z = $pos->getZ();
				for ($i = 1; $i <= 12; $i++) 
				{
				    $spawnKey = "spawn-$i";
				    if (empty($arena->matchInfo[$spawnKey])) 
				    {
				        $arena->setSpawnPos($spawnKey, "$x:$y:$z");
				        $player->sendMessage(Main::TAG . "Player Spawn $i: $x, $y, $z");
				        return;
				    }
				}
			}
			if($explodeWand[1] === "PLAYER-SPAWN-RESET")
			{				
				for ($i = 1; $i <= 12; $i++) 
				{
				    $spawnKey = "spawn-$i";
				    if (!empty($arena->matchInfo[$spawnKey])) 
				    {
				        $arena->setSpawnPos($spawnKey, null);
				        $player->sendMessage(Main::TAG . "Succesfully reset player spawn $i");
				    }
				}

			}
			if($explodeWand[1] === "CHEST-SPAWN")
			{					
				if(!($event->getBlock() instanceof Chest))
				{
					$player->sendMessage(Main::TAG . "C.H.E.S.T");
					return;
				}
				if(!empty($arena->matchInfo["C-41"]))
				{
					$player->sendMessage(Main::TAG . "All spawn have already been setted!");
					return;
				}
				$pos = $event->getBlock()->getPosition()->asVector3();
				$x = $pos->getX();
				$y = $pos->getY();
				$z = $pos->getZ();
				for ($i = 1; $i <= 41; $i++) 
				{
				    $chestKey = "C-$i";
				    if (empty($arena->matchInfo[$chestKey])) 
				    {
				        $arena->setChestPos($chestKey, "$x:$y:$z");
				        $player->sendMessage(Main::TAG . "Chest Spawn $i: $x, $y, $z"); 
				        return;
				    }
				}
			}
			if($explodeWand[1] === "CHEST-SPAWN-RESET")
			{					
				for ($i = 1; $i <= 41; $i++) 
				{
				    $chestKey = "C-$i";
				    if (!empty($arena->matchInfo[$chestKey])) 
				    {
				        $arena->setChestPos($chestKey, null);
				        $player->sendMessage(Main::TAG . "Succesfully reset chest spawn $i");
				    }
				}
			}
			if($explodeWand[1] === "HUB-SPAWN")
			{									
				$pos = $event->getBlock()->getPosition()->asVector3();
				$x = $pos->getX();
				$y = $pos->getY();
				$z = $pos->getZ();
				$arena->setHubPos("$x:$y:$z");
				$player->sendMessage(Main::TAG . "Hub Spawn: $x, $y, $z");  			
			}
			$event->cancel();
		}
	}
}
