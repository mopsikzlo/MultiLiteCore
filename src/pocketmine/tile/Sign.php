<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 *
*/

namespace pocketmine\tile;

use pocketmine\event\block\SignChangeEvent;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use pocketmine\network\protocol\Info;

class Sign extends Spawnable {

	/**
	 * Sign constructor.
	 *
	 * @param Level       $level
	 * @param CompoundTag $nbt
	 */
	public function __construct(Level $level, CompoundTag $nbt){
		if(!isset($nbt->Text1)){
			$nbt->Text1 = new StringTag("Text1", "");
		}
		if(!isset($nbt->Text2) or !($nbt->Text2 instanceof StringTag)){
			$nbt->Text2 = new StringTag("Text2", "");
		}
		if(!isset($nbt->Text3) or !($nbt->Text3 instanceof StringTag)){
			$nbt->Text3 = new StringTag("Text3", "");
		}
		if(!isset($nbt->Text4) or !($nbt->Text4 instanceof StringTag)){
			$nbt->Text4 = new StringTag("Text4", "");
		}

		parent::__construct($level, $nbt);
	}

	public function saveNBT(){
		parent::saveNBT();
		unset($this->namedtag->Creator);
	}

	/**
	 * @param string $line1
	 * @param string $line2
	 * @param string $line3
	 * @param string $line4
	 *
	 * @return bool
	 */
	public function setText($line1 = "", $line2 = "", $line3 = "", $line4 = ""){
		$this->namedtag->Text1 = new StringTag("Text1", $line1);
		$this->namedtag->Text2 = new StringTag("Text2", $line2);
		$this->namedtag->Text3 = new StringTag("Text3", $line3);
		$this->namedtag->Text4 = new StringTag("Text4", $line4);
		$this->onChanged();

		return true;
	}

	/**
	 * @param int    $index 0-3
	 * @param string $line
	 * @param bool   $update
	 */
	public function setLine(int $index, string $line, bool $update = true){
		if($index < 0 or $index > 3){
			throw new \InvalidArgumentException("Index must be in the range 0-3!");
		}
		$this->namedtag["Text" . ($index + 1)] = $line;
		if($update){
			$this->onChanged();
		}
	}

	/**
	 * @param int $index 0-3
	 *
	 * @return string
	 */
	public function getLine(int $index) : string{
		if($index < 0 or $index > 3){
			throw new \InvalidArgumentException("Index must be in the range 0-3!");
		}
		return (string) $this->namedtag["Text" . ($index + 1)];
	}

	/**
	 * @return array
	 */
	public function getText(){
		return [
			$this->namedtag["Text1"],
			$this->namedtag["Text2"],
			$this->namedtag["Text3"],
			$this->namedtag["Text4"]
		];
	}

	/**
	 * @return CompoundTag
	 */
	public function getSpawnCompound(){
		return new CompoundTag("", [
			new StringTag("id", Tile::SIGN),
			$this->namedtag->Text1,
			$this->namedtag->Text2,
			$this->namedtag->Text3,
			$this->namedtag->Text4,
			new IntTag("x", (int) $this->x),
			new IntTag("y", (int) $this->y),
			new IntTag("z", (int) $this->z)
		]);
	}

	/**
	 * @param CompoundTag $nbt
	 * @param Player      $player
	 *
	 * @return bool
	 */
	public function updateCompoundTag(CompoundTag $nbt, Player $player) : bool{
		if($nbt["id"] !== Tile::SIGN){
			return false;
		}

		$signText = [];
		
		if ($player->getOriginalProtocol() >= Info::PROTOCOL_120) {
			$signText = explode("\n", $nbt['Text']);
			for ($i = 0; $i < 4; $i++) {
				$signText[$i] = isset($signText[$i]) ? TextFormat::clean($signText[$i], $removeFormat) : '';
			}
			unset($nbt['Text']);
		} else {
			for ($i = 0; $i < 4; $i++) {
				$signText[$i] = TextFormat::clean($nbt["Text" . ($i + 1)], $removeFormat);
			}
		}

		$ev = new SignChangeEvent($this->getBlock(), $player, $signText);

		if(!isset($this->namedtag->Creator) or $this->namedtag["Creator"] !== $player->getRawUniqueId()){
			$ev->setCancelled();
		}

		$this->level->getServer()->getPluginManager()->callEvent($ev);

		if(!$ev->isCancelled()){
			$this->setText(...$ev->getLines());
			return true;
		}else{
			return false;
		}
	}

}
