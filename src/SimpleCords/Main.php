<?php

declare(strict_types=1);

namespace SimpleCords;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\TaskHandler;
use pocketmine\utils\TextFormat;

class Main extends PluginBase implements Listener{
	/** @var TaskHandler[] */
	private $tasks = [];
	/** @var int */
	private $refreshRate = 1;
	/** @var string */
	private $mode = "popup";

	/** @var int */
	private $precision = 1;

	public function onEnable() : void{
		$this->refreshRate = (int) $this->getConfig()->get("refreshRate");
		if($this->refreshRate < 1){
			$this->getLogger()->warning("Refresh rate property in config.yml is less than 1. Resetting to 1");
			$this->getConfig()->set("refreshRate", 1);
			$this->getConfig()->save();
			$this->refreshRate = 1;
		}

		$this->mode = $this->getConfig()->get("displayMode", "popup");
		if($this->mode !== "tip" and $this->mode !== "popup"){
			$this->getLogger()->warning("Invalid display mode " . $this->mode . ", resetting to `popup`");
			$this->getConfig()->set("displayMode", "popup");
			$this->getConfig()->save();
			$this->mode = "popup";
		}
		$this->precision = (int) $this->getConfig()->get("precision");
		if($this->precision < 0){
			$this->getLogger()->warning("Precision property in config.yml is less than 0, using default");
			$this->getConfig()->set("precision", 1);
			$this->getConfig()->save();
			$this->precision = 1;
		}

		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	public function onDisable() : void{
		//this doesn't get cleared on plugin disable, because plugins are not removed from the PluginManager unless
		//a reload took place. However, tasks ARE cancelled on plugin disable.
		$this->tasks = [];
	}

	public function onCommand(CommandSender $sender, Command $command, string $aliasUsed, array $args) : bool{
		if($command->getName() === "coords"){
			if(!($sender instanceof Player)){
				$sender->sendMessage(TextFormat::RED . "You can't use this command in the terminal");

				return true;
			}

			if(!isset($this->tasks[$sender->getName()])){
				$this->tasks[$sender->getName()] = $this->getScheduler()->scheduleRepeatingTask(new ShowDisplayTask($sender, $this->mode, $this->precision), $this->refreshRate);
				$sender->sendMessage(TextFormat::GREEN . "coordinates has been enabled");
			}else{
				$this->stopDisplay($sender->getName());
				$sender->sendMessage(TextFormat::GREEN . "coordinates has been disabled");
			}

			return true;
		}

		return false;
	}

	private function stopDisplay(string $playerFor) : void{
		if(isset($this->tasks[$playerFor])){
			$this->tasks[$playerFor]->cancel();
			unset($this->tasks[$playerFor]);
		}
	}

	public function onPlayerQuit(PlayerQuitEvent $event) : void{
		$this->stopDisplay($event->getPlayer()->getName());
	}
}
