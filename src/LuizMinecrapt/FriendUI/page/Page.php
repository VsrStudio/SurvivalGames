<?php

namespace LuizMinecrapt\FriendUI\page;

use pocketmine\Server;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat as TF;
use jojoe77777\FormAPI\{CustomForm, Form, ModalForm, SimpleForm};
use LuizMinecrapt\FriendUI\FriendUI;

class Page
{
	/** @var array Player[] */
	private $players = [];

	/** @var string Player */
	private string $target;

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
			switch($data)
			{
				case 0:
					$this->addFriendPage($player);
				break;

				case 1:
					$this->friendPage($player);
				break;

				case 2:
					$this->inboxPage($player);
				break;
			}
		});
		$form->setTitle(TF::BOLD . TF::GOLD . "Friend" .TF::AQUA."UI");
		$form->setContent("Hey, " .TF::YELLOW . $player->getName() . TF::RESET . "! Glad to see you back on FriendUI. Who will be the new friend or old buddy you'll meet today?"); 	
		$form->addButton(TF::AQUA . "Add Player\n" . TF::WHITE."»» Find new friend", 0, "textures/ui/friend1_black_outline");

		$form->addButton(TF:: AQUA . "Friend\n" . TF::WHITE. "»» " .FriendUI::getInstance()->getPlayer($player->getName())->countOnlineFriends() . " of " . FriendUI::getInstance()->getPlayer($player->getName())->countFriends() . " friends are online" , 0, "textures/ui/FriendsIcon");
		if(FriendUI::getInstance()->getPlayer($player->getName())->countInbox() > 0)
		{
			$form->addButton(TF:: AQUA . "Inbox\n" . TF::WHITE . "»» " . FriendUI::getInstance()->getPlayer($player->getName())->countInbox() . " inbox!", 0, "textures/ui/mail_icon");
		} else
		{
			$form->addButton(TF:: AQUA . "Inbox\n" . TF::WHITE . "»» No incoming inbox", 0, "textures/ui/mail_icon");
		}
		$form->addButton(TF::RED."Exit\n",0, "textures/items/compass_item");
		$form->sendToPlayer($player);
	}

	public function addFriendPage(Player $player): void
	{
		$form = new SimpleForm(function(Player $player, int $data = null){
			if($data === null)
			{
				return;
			}
			switch($data)
			{
				case 0:
					$this->viaSearch($player);
				break;

				case 1:			
				foreach(Server::getInstance()->getOnlinePlayers() as $op)
				{
					$this->players[$player->getName()][] = $op->getName();
				}		
					$this->viaPlayerOnline($player);
				break;

				case 2:					
					$this->mainPage($player);
				break;
			}
		});
		$form->setTitle(TF::BOLD . TF::GOLD . "Friend" .TF::AQUA."UI");
		$form->setContent("You can find players here, you can choose either via search or via online players");
		$form->addButton(TF::AQUA . "Via Search\n" . TF::WHITE."»» Search by player's name", 0, "textures/ui/magnifyingGlass");
		$form->addButton(TF::AQUA . "Via Player Online\n" . TF::WHITE."»» Search by online players", 0, "textures/ui/permissions_visitor_hand_hover");
		$form->addButton(TF::RED."Back\n", 0, "textures/items/compass_item");
		$form->sendToPlayer($player);
	}

	public function viaSearch(Player $player): void
	{
		$form = new CustomForm(function(Player $player, array $data = null){
			if($data === null)
			{
				$this->addFriendPage($player);
				return;
			}
			$target = Server::getInstance()->getPlayerExact($data[0]);
			if(!($target instanceof Player))
			{
				$player->sendMessage(FriendUI::TAG . TF::RED . "$data[0] not founded");
				return;
			}
			if($data[0] === $player->getName())
			{
				$player->sendMessage(FriendUI::TAG . TF::RED . "You can't add yourself to friend");
				return;
			}
			$p = FriendUI::getInstance()->getPlayer($player->getName());			
			if($p->getFriend($target->getName()) == true)
			{
				$player->sendMessage(FriendUI::TAG . TF::RED . "You already friends with $data[0]");
				return;
			}			
			if(FriendUI::getInstance()->getPlayer($target->getName())->getInbox($player->getName()) == true)
			{
				$player->sendMessage(FriendUI::TAG . TF::RED . "You already sent friend request to $data[0]");
				return;
			}
			FriendUI::getInstance()->getPlayer($target->getName())->addInbox($player->getName());
			$player->sendMessage(FriendUI::TAG . "Successfully sent friend request to " . TF::YELLOW . $target->getName());
			$target->sendMessage(FriendUI::TAG . TF::YELLOW . $player->getName() . TF::RESET . " sent you friend request!");
		});
		$form->setTitle(TF::BOLD . TF::GOLD . "Friend" .TF::AQUA."UI");
		$form->addInput("Search for a player's name based on their username:");
		$form->sendToPlayer($player);
	}

	public function viaPlayerOnline(Player $player): void
	{		
		$form = new SimpleForm(function(Player $player, int $data = null){
			if($data === null)
			{
				$this->players[$player->getName()] = [];
				return;
			}
			$onlinePlayers = $this->players[$player->getName()];
			if(!isset($onlinePlayers[$data]))
			{
				$this->players[$player->getName()] = [];
				$this->addFriendPage($player);
				return;
			}
			$targetName = $onlinePlayers[$data];
			$target = Server::getInstance()->getPlayerExact($targetName);
			if(!($target instanceof Player))
			{
				$this->players[$player->getName()] = [];
				$player->sendMessage(FriendUI::TAG . TF::RED . "$targetName is not founded");
				return;
			}
			if($targetName === $player->getName())
			{
				$this->players[$player->getName()] = [];
				$player->sendMessage(FriendUI::TAG . TF::RED . "You can't add yourself to friend");
				return;
			}
			$p = FriendUI::getInstance()->getPlayer($player->getName());			
			if($p->getFriend($target->getName()) == true)
			{
				$this->players[$player->getName()] = [];
				$player->sendMessage(FriendUI::TAG . TF::RED . "You already friends with $targetName");
				return;
			}
			if(FriendUI::getInstance()->getPlayer($target->getName())->getInbox($player->getName()) == true)
			{
				$this->players[$player->getName()] = [];
				$player->sendMessage(FriendUI::TAG . TF::RED . "You already sent friend request to $targetName");
				return;
			}			
			FriendUI::getInstance()->getPlayer($targetName)->addInbox($player->getName());
			$player->sendMessage(FriendUI::TAG . "Successfully sent friend request to " . TF::YELLOW . $targetName);
			$target->sendMessage(FriendUI::TAG . TF::YELLOW . $player->getName() . TF::RESET . " sent you friend request!");
			$this->players[$player->getName()] = [];			
		});
		$form->setTitle(TF::BOLD . TF::GOLD . "Friend" .TF::AQUA."UI");
		$onlinePlayers = Server::getInstance()->getOnlinePlayers();
		$form->setContent("There are " . count($onlinePlayers) . " players currently online");
		foreach($onlinePlayers as $op)
		{
			$form->addButton(TF::AQUA.$op->getName()."\n".TF::GREEN."»» Online", 0, "textures/ui/friend1_black_outline_2x");
		}
		$form->addButton(TF::RED."Back\n", 0, "textures/items/compass_item");
		$form->sendToPlayer($player);
	}

	public function friendPage(Player $player): void
	{
		$form = new SimpleForm(function(Player $player, int $data = null){
			if($data === null)
			{
				return;
			}
			$p = FriendUI::getInstance()->getPlayer($player->getName());
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
			$target = $inb[$data];  
	        $this->onFriend($player, $target);	
		});
		$form->setTitle(TF::BOLD . TF::GOLD . "Friend" .TF::AQUA."UI");
		$p = FriendUI::getInstance()->getPlayer($player->getName());
		if($p->isFriendEmpty() === true)
		{
			$form->setContent("You have " .  FriendUI::getInstance()->getPlayer($player->getName())->countFriends() . " friends in your friend list, meet more friends!");
			$ark = array_values($p->getFriends());
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

	public function onFriend(Player $player, string $targetName): void
	{
		$this->target = $targetName;
		$form = new SimpleForm(function(Player $player, int $data = null){
			if($data === null)
			{
				return;
			}
			switch($data)
			{
				case 0:
					$p = FriendUI::getInstance()->getPlayer($player->getName());
					$p->removeFriend($this->target);
					FriendUI::getInstance()->getPlayer($this->target)->removeFriend($player->getName());
					$player->sendMessage(FriendUI::TAG . "Successfully remove " . TF::YELLOW . $this->target . TF::RESET . " from your friend list");
				break;

				case 1:
					$this->friendPage($player);
				break;
			}
		});
		$form->setTitle(TF::BOLD . TF::GOLD . "Friend" .TF::AQUA."UI");
		$p = FriendUI::getInstance()->getPlayer($player->getName());
		$target = Server::getInstance()->getPlayerExact($targetName);
		if($target)
		{
			$form->setContent(
				"Name: $targetName\n".
				"Status: " . TF::GREEN . "Online");
		} else		
		{
			$form->setContent(
				"Name: $targetName\n".
				"Status: " . TF::RED . "Offline");
		}
		$form->addButton(TF::RED . "Remove\n" .TF::WHITE."»» Remove friend from your friend list", 0, "textures/ui/icon_trash");
		$form->addButton(TF::RED."Back\n", 0, "textures/items/compass_item");
		$form->sendToPlayer($player);
	}

	public function inboxPage(Player $player): void
	{
		$form = new SimpleForm(function(Player $player, int $data = null){
			if($data === null)
			{
				$this->mainPage($player);
				return;
			}
			$p = FriendUI::getInstance()->getPlayer($player->getName());			
			$inboxList = $p->getInboxes();
			if($inboxList == false)
			{
				$this->mainPage($player);
				return;
			}
			$inb = array_values($inboxList);
			if(!isset($inb[(int)$data]))
			{
				$this->mainPage($player);
				return;
			}				
			$targetName = $inb[(int)$data];
			FriendUI::getInstance()->getPlayer($player->getName())->addFriend($targetName);
			FriendUI::getInstance()->getPlayer($targetName)->addFriend($player->getName());
			if(Server::getInstance()->getPlayerExact($targetName))
			{
				Server::getInstance()->getPlayerExact($targetName)->sendMessage(FriendUI::TAG . TF::YELLOW . $player->getName() . TF::RESET . " accepted your friend request");
				Server::getInstance()->getPlayerExact($targetName)->sendMessage(FriendUI::TAG . "Now you are friends with " . TF::YELLOW . $player->getName());
			}
			$player->sendMessage(FriendUI::TAG . "Now you are friends with " . TF::YELLOW . "$targetName");
		});
		$form->setTitle(TF::BOLD . TF::GOLD . "Friend" .TF::AQUA."UI");
		$p = FriendUI::getInstance()->getPlayer($player->getName());
		if($p->isInboxEmpty() === true)
		{
			foreach($p->getInboxes() as $request)
			{
				$form->addButton(TF::YELLOW . "$request\n". TF::WHITE . "»» Request friend", 0, "textures/ui/friend1_black_outline_2x");
			}
		} else
		{
			$form->setContent(TF::BOLD . "»»" . TF::RED . " NO INBOX AVAILABLE");
		}
		$form->addButton(TF::RED."Back\n", 0, "textures/items/compass_item");
		$form->sendToPlayer($player);
	}
}