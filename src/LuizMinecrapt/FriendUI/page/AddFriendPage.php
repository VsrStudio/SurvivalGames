<?php

namespace LuizMinecrapt\FriendUI\page;

use pocketmine\Server;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat as TF;
use jojoe77777\FormAPI\{CustomForm, Form, ModalForm, SimpleForm};
use LuizMinecrapt\FriendUI\FriendUI;

class AddFriendPage
{
	public function addFriendPage(Player $player): void
	{
		$form = new SimpleForm(function(Player $player, int $data = null){
			if($data == null)
			{
				return;
			}
			switch($data)
			{
				case 0:
					$this->viaSearch($player);
				break;

				case 1:
					$this->viaPlayerOnline($player);
				break;
			}
		});
		$form->addButton("Via Search");
		$form->addButton("Via Player Online");
		$form->sendToPlayer($player);
	}

	public function viaSearch(Player $player): void
	{
		$form = new CustomForm(function(Player $player, array $data = null){
			if($data == null)
			{
				return;
			}
			$target = Server::getInstance()->getPlayerExact($data[0]);
			if(!($target instanceof Player))
			{
				$player->sendMessage(TF::RED . "$data[0] not founded");
				return;
			}
			$p = FriendUI::getInstance()->getPlayer($player);
			if($p->checkFriend($data[0]) == true)
			{
				$player->sendMessage(TF::RED . "This player already exist in your friend list");
				return;
			}
			$p->addInbox($target);
		});
		$form->addInput("Via Search");
		$form->sendToPlayer($player);
	}

	public function viaPlayerOnline(Player $player): void
	{

	}
}