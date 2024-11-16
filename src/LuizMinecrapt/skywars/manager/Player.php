<?php

namespace LuizMinecrapt\skywars\manager;

use pocketmine\Server;
use pocketmine\utils\Config;
use LuizMinecrapt\skywars\Main;

class Player
{
	/** @var array */
	private array $playerData;
	
	public function __construct(?string $name, ?int $points = 0, ?int $kills = 0)
	{
		$this->playerData["name"] = $name;
		$this->playerData["points"] = $points;
		$this->playerData["kills"] = $kills;
	}

	public function getDataInfo(): array
	{
		$array = [];
		foreach($this->playerData as $k => $data)
		{
			$array[$k] = $data;
		}
		return $array;
	}

	public function reload(): void
	{
		$config = new Config(Main::getInstance()->getDataFolder() . "players/" . $this->playerData["name"] . ".yml", Config::YAML);
		$config->setAll($this->getDataInfo());
		$config->save();
	}

	public function getName(): string
	{
		return $this->playerData["name"];
	}

	public function addPoint(int $point)
	{			
		$this->playerData["points"] = $this->playerData["points"] + $point;
	}

	public function getPoint(): int
	{
		return $this->playerData["points"];
	}

	public function addKill(int $kill)
	{
		$this->playerData["kills"] = $this->playerData["kills"] + $kill;
	}

	public function getKill(): int
	{
		return $this->playerData["kills"];
	}
}