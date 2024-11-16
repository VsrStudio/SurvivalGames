<?php

namespace LuizMinecrapt\FriendUI\event;

use pocketmine\Server;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use LuizMinecrapt\FriendUI\FriendUI;

class FriendListener implements Listener
{
	public function __construct(FriendUI $plugin)
	{

	}
	
	public function onJoin(PlayerJoinEvent $event): void
	{
		$player = $event->getPlayer();	
		if(!file_exists(FriendUI::getInstance()->getDataFolder() . "players/" . $player->getName() . ".yml"))
		{
			FriendUI::getInstance()->addPlayer($player);
			FriendUI::getInstance()->getPlayer($player->getName())->reload();	
		}
	}
}