<?php

declare(strict_types=1);

namespace LuizMinecrapt\FriendUI\manager;

use pocketmine\Server;
use pocketmine\utils\Config;
use LuizMinecrapt\FriendUI\FriendUI;

class Friend
{
	/** @var array */
	public array $data;

	/**
	 * @param string $userName|null
	 * @param array $array|[]
	 */
	public function __construct(?string $userName = null, ?array $friend = null, ?array $inbox = null)
	{
		$this->data["username"] = $userName;
		$this->data["friend"] = $friend;
		$this->data["inbox"] = $inbox;
	}

	public function getFriendInfo(): array
	{
		$array = [];
		$array["username"] = $this->data["username"];
		if($this->isFriendEmpty() == true)
		{
			foreach($this->data["friend"] as $k => $o)
			{
				$array["friend"][$k] = $o;
			}			
		}		
		if($this->isInboxEmpty() == true)
		{
			foreach($this->data["inbox"] as $p => $l)
			{
				$array["inbox"][$p] = $l;
			}	
		}		
		return $array;
	}

	public function reload(): void
	{
		$config = new Config(FriendUI::getInstance()->getDataFolder() . "players/" . $this->data["username"] . ".yml", Config::YAML);
		$config->setAll($this->getFriendInfo());
		$config->save();
	}

	public function getUsername(): string
	{
		return $this->data["username"];
	}

	/**
	 * Returns when obtaining the entire list of friends acquired.
	 */
	public function getFriends()
	{
		if($this->isFriendEmpty() == true)
		{
			return $this->data["friend"];
		}
		return false;
	}

	public function countFriends(): int
	{
		if($this->getFriends() == true)
		{
			return count($this->getFriends());
		}
		return 0;
	}

	/**
	 * 	Return bool if target is exist
	 */
	public function getFriend(string $target): bool
	{
		if(isset($this->data["friend"][strtolower($target)]))
		{
			// exist
			return true;
		}
		return false;
	}	

	/**
	 * Return bool if empty or not
	 */
	public function isFriendEmpty(): bool
	{
		if(is_array($this->data["friend"]))
		{
			return true;
		}
		return false;
	}	

	/**
	 * Add friend
	 */
	public function addFriend(string $target): void
	{
		$this->data["friend"][strtolower($target)] = $target;
		$this->removeInbox($target);
		$this->reload();
	}

	public function removeFriend(string $target): void
	{
		if($this->getFriend($target) == true)
		{
			unset($this->data["friend"][strtolower($target)]);
			$this->reload();
		}
	}

	/**
	 * Returns when obtaining the entire list of inbox acquired.
	 */
	public function getInboxes()
	{
		if($this->isInboxEmpty() == true)
		{
			return $this->data["inbox"];
		}
		return false;
	}

	/**
	 * 	Return bool if target is exist
	 */
	public function getInbox(string $target): bool
	{
		if(isset($this->data["inbox"][strtolower($target)]))
		{
			// exist
			return true;
		}
		return false;
	}	

	/**
	 * Return bool if empty or not
	 */
	public function isInboxEmpty(): bool
	{
		if(is_array($this->data["inbox"]))
		{
			return true;
		}
		return false;		
	}	

	/**
	 * 	Add inbox
	 */
	public function addInbox(string $target)
	{
		$this->data["inbox"][strtolower($target)] = $target;
		$this->reload();	
	}

	public function removeInbox(string $target): void
	{
		if($this->getInbox($target) == true)
		{
			unset($this->data["inbox"][strtolower($target)]);
		}
	}	

	public function getOnlineFriends()
	{
		if($this->isFriendEmpty() == true)
		{
			foreach($this->getFriends() as $name => $n)
			{
				if(Server::getInstance()->getPlayerExact($n))
				{
					return array($n);
				}
			}
		}
		return false;
	}

	public function countOnlineFriends(): int
	{
		if($this->getOnlineFriends() != false)
		{
			$countOF = count($this->getOnlineFriends());
			return $countOF;
		}
		return 0;		
	}

	public function countInbox(): int
	{
		if($this->getInboxes() != false)
		{
			$countIB = count($this->getInboxes());
			return $countIB;
		}
		return 0;		
	}
}