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

/**
 * Network-related classes
 */

namespace pocketmine\network;

use pocketmine\network\protocol\DataPacket;
use pocketmine\Player;

/**
 * Classes that implement this interface will be able to be attached to players
 */
interface SourceInterface {

	/**
	 * Terminates the connection
	 *
	 * @param Player $player
	 * @param string $reason
	 *
	 */
	public function close(Player $player, $reason = "unknown reason");

	/**
	 * @param string $name
	 */
	public function setName($name);

	/**
	 * Called every tick to process events on the interface.
	 */
	public function process() : void;

	public function shutdown();

	public function emergencyShutdown();

}