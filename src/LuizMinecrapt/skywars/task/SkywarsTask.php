<?php

namespace LuizMinecrapt\skywars\task;

use pocketmine\scheduler\Task;
use LuizMinecrapt\skywars\manager\Game;

class SkywarsTask extends Task
{
	/** @var Game */
	private $game;

	/**
	 * @param Game $game
	 */
	public function __construct(Game $game)
	{
		$this->game = $game;
	}

	public function onRun(): void
	{
		$this->game->tick();
	}
}