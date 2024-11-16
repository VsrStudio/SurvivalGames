<?php

namespace VsrStudio\sg\task;

use pocketmine\scheduler\Task;
use VsrStudio\sg\manager\Game;

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
