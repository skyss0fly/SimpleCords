<?php

declare(strict_types=1);

namespace SimpleCords;

use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat;

class ShowDisplayTask extends Task{
	/** @var Player */
	private $player;
	/** @var string */
	private $mode;
	/** @var int */
	private $precision;

	public function __construct(Player $player, string $mode = "popup", int $precision = 1){
		$this->player = $player;
		$this->mode = $mode;
		$this->precision = $precision;
	}

	public function onRun() : void{
		assert(!$this->player->isClosed());
		$ploc = $this->player->getLocation();
		$location = "Location: " . TextFormat::GREY . "(" . Utils::getFormattedCoords($this->precision, $ploc->getX(), $ploc->getY(), $ploc->getZ()) . ")" . TextFormat::WHITE . "\n";
		$world = "World: " . TextFormat::GRAY . $this->player->getWorld()->getDisplayName() . TextFormat::WHITE . "\n";
		$direction = "Direction: " . TextFormat::GRAY . Utils::getCompassDirection($ploc->getYaw()) . " (" . round($ploc->getYaw(), $this->precision) . ")" . TextFormat::WHITE . "\n";

		switch($this->mode){
			case "tip":
				$this->player->sendTip($location . $world . $direction);
				break;
			case "popup":
				$this->player->sendPopup($location . $world . $direction);
				break;
			default:
				break;
		}
	}

}
