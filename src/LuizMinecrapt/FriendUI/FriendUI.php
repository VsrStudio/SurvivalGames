<?php

declare(strict_types=1);

namespace LuizMinecrapt\FriendUI;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat as TF;
use LuizMinecrapt\FriendUI\event\FriendListener;
use LuizMinecrapt\FriendUI\manager\Friend;
use LuizMinecrapt\FriendUI\page\Page;

class FriendUI extends PluginBase
{
	/** @var return */
	public $page;

	/** @var Game */
	public $data;

	/** @static instance Main */
	private static FriendUI $instance;	

	public function onEnable(): void
	{
		FriendUI::$instance = $this;

		$this->getServer()->getPluginManager()->registerEvents(new FriendListener($this),$this);

		@mkdir($this->getDataFolder() . "players/");
		foreach(glob($this->getDataFolder() . "players/*.yml") as $dataLocation)
		{
			$dataFileContents = file_get_contents($dataLocation);
			$dataYaml = yaml_parse($dataFileContents);
			$this->data[$dataYaml["username"]] = new Friend($dataYaml["username"] ?? null, $dataYaml["friend"] ?? null, $data["inbox"] ?? null);
		}

		// Page
		$this->page = new Page();
	}

	public function onDisable(): void
	{
		foreach($this->getPlayers() as $data)
		{
			$data->reload();
		}
	}

	public function getPlayer(string $player): ?Friend
	{
		return $this->data[$player] ?? null;
	}

	public function getPlayers(): array
	{
		return $this->data;
	}

	public function addPlayer(Player $player): bool
	{
		$this->data[$player->getName()] = new Friend(userName: $player->getName());
		return true;
	}

	public function getPage(): ?Page
	{
		return $this->page;
	}

	public static function getInstance(): FriendUI
	{
		return FriendUI::$instance;
	}

	public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args): bool
	{
		switch($cmd->getName())
		{
			case "friendui":
			case "friend":
				if(!($sender instanceof Player))
				{
					$sender->sendMessage("You can't run this command!");
					return true;
				}
				$this->getPage("main")->mainPage($sender);
			break;
		}
		return true;
	}

	public const TAG = TF::GRAY . "[FriendUI] " . TF::RESET;
}
