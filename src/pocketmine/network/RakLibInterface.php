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

namespace pocketmine\network;

use pocketmine\event\player\PlayerCreationEvent;
use pocketmine\network\Network;
use pocketmine\network\AdvancedSourceInterface;
use pocketmine\network\protocol\DataPacket;
use pocketmine\network\protocol\Info;
use pocketmine\utils\BinaryStream;
use pocketmine\Player;
use pocketmine\Server;
use raklib\protocol\EncapsulatedPacket;
use raklib\protocol\PacketReliability;
use raklib\RakLib;
use raklib\server\RakLibServer;
use raklib\server\ServerHandler;
use raklib\server\ServerInstance;

class RakLibInterface implements ServerInstance, AdvancedSourceInterface {

	/** @var Server */
	private $server;

	/** @var Network */
	private $network;

	/** @var RakLibServer */
	private $rakLib;

	/** @var Player[] */
	private $players = [];

	/** @var string[] */
	private $identifiers;

	/** @var int[] */
	private $identifiersACK = [];

	/** @var ServerHandler */
	private $interface;
	
	public $count = 0;
	public $maxcount = 31360;
	public $name = "";

	public function __construct(Server $server){
		$this->server = $server;
		$this->identifiers = [];

		$this->rakLib = new RakLibServer($this->server->getLogger(), $this->server->getLoader(), $this->server->getPort(), $this->server->getIp() === "" ? "0.0.0.0" : $this->server->getIp());
		$this->interface = new ServerHandler($this->rakLib, $this);
	}

	public function setNetwork(Network $network){
		$this->network = $network;
	}

	public function process() : void{
		while($this->interface->handlePacket()){}

		if(!$this->rakLib->isRunning() and !$this->rakLib->isShutdown()){
			throw new \Exception("RakLib Thread crashed");
		}
	}

	public function closeSession($identifier, $reason){
		if(isset($this->players[$identifier])){
			$player = $this->players[$identifier];
			unset($this->identifiers[spl_object_hash($player)]);
			unset($this->players[$identifier]);
			unset($this->identifiersACK[$identifier]);
			$player->close($player->getLeaveMessage(), $reason);
		}
	}

	public function close(Player $player, $reason = "unknown reason"){
		if(isset($this->identifiers[$h = spl_object_hash($player)])){
			unset($this->players[$this->identifiers[$h]]);
			unset($this->identifiersACK[$this->identifiers[$h]]);
			$this->interface->closeSession($this->identifiers[$h], $reason);
			unset($this->identifiers[$h]);
		}
	}

	public function shutdown(){
		$this->interface->shutdown();
	}

	public function emergencyShutdown(){
		$this->interface->emergencyShutdown();
	}

	public function openSession($identifier, $address, $port, $clientID){
		$ev = new PlayerCreationEvent($this, Player::class, Player::class, null, $address, $port);
		$this->server->getPluginManager()->callEvent($ev);
		$class = $ev->getPlayerClass();

		$player = new $class($this, $ev->getClientId(), $ev->getAddress(), $ev->getPort());
		$this->players[$identifier] = $player;
		$this->identifiersACK[$identifier] = 0;
		$this->identifiers[spl_object_hash($player)] = $identifier;
		$this->server->addPlayer($player);
	}

	public function handleEncapsulated($identifier, $buffer){
		if(isset($this->players[$identifier])){
			//get this now for blocking in case the player was closed before the exception was raised
			$player = $this->players[$identifier];
			$address = $player->getAddress();
			try{
				if($buffer !== ""){
					$pk = $this->getPacket($buffer, $player);
					if (!is_null($pk)) {
						try {
					    	$pk->decode($player->getPlayerProtocol());
						}catch(\Exception $e){
							file_put_contents("logs/" . date('Y.m.d') . "_decode_error.log", $pk->pname() . "\n" . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n", FILE_APPEND | LOCK_EX);
							return;
						}
						$player->handleDataPacket($pk);
					}
				}
			}catch(\Throwable $e){
				file_put_contents("logs/" . date('Y.m.d') . "_decode_error.log", $e->getMessage() . "\n" . $e->getTraceAsString() . "\n", FILE_APPEND | LOCK_EX);

				$player->close($player->getLeaveMessage(), "Internal server error");
				$this->interface->blockAddress($address, 5);
			}
		}
	}

	public function handleKick($identifier, $reason){
		if(isset($this->players[$identifier])){
			$player = $this->players[$identifier];
			$player->close($reason);
		}
	}

	public function blockAddress($address, $timeout = 300){
		$this->interface->blockAddress($address, $timeout);
	}

	public function unblockAddress($address){
		$this->interface->unblockAddress($address);
	}

	public function handleRaw($address, $port, $payload){
		$this->server->handlePacket($this, $address, $port, $payload);
	}

	public function sendRawPacket($address, $port, $payload){
		$this->interface->sendRaw($address, $port, $payload);
	}

	public function setCount($count, $maxcount) {
		$this->count = $count;
		$this->maxcount = $maxcount;

		$this->interface->sendOption("name",
		"MCPE;".addcslashes($this->name, ";") .";".
		(Info::CURRENT_PROTOCOL).";".
//		\pocketmine\MINECRAFT_VERSION_NETWORK.";".
		''.";".
		$this->count.";".$maxcount . ";". Server::getServerId()
		);
	}

	public function setName($name){
		if(strlen($name) > 1) {
			$this->name = $name;
		}
	}

	/**
	 * @param bool $name
	 *
	 * @return void
	 */
	public function setPortCheck($name){
		$this->interface->sendOption("portChecking", (bool) $name);
	}

	public function handleOption($option, $value){
		if($option === "bandwidth"){
			$v = unserialize($value);
			$this->network->addStatistics($v["up"], $v["down"]);
		}
	}

	private function getPacket($buffer, $player){
		$tmpStream = new BinaryStream($buffer);
		$header = $tmpStream->getUnsignedVarInt();
		$pid = $header & 0x3FF;		

		if (($data = $this->network->getPacket($pid, $player->getPlayerProtocol())) === null) {
			return null;
		}
		$data->setBuffer($buffer);

		return $data;
	}

	public function handlePing($identifier, $pingMS){
		if(isset($this->players[$identifier])){
			$player = $this->players[$identifier];
			$player->setPing($pingMS);
		}
	}
	
	public function putReadyPacket($player, $buffer) {
		if (isset($this->identifiers[$h = spl_object_hash($player)])) {	
			$pk = new EncapsulatedPacket();
			$pk->buffer = $buffer;
			$pk->reliability = 3;	
			$this->interface->sendEncapsulated($this->identifiers[$h], $pk, RakLib::PRIORITY_NORMAL);			
		}
	}
	
	public function putPacket($player, $buffer) {
		if (isset($this->identifiers[$h = spl_object_hash($player)])) {
			$pk = new EncapsulatedPacket();
			$pk->buffer = $buffer;
			$pk->reliability = 3;
			$flag = RakLib::FLAG_NEED_ZLIB;
			$this->interface->sendEncapsulated($this->identifiers[$h], $pk,  RakLib::PRIORITY_NORMAL | $flag);
		}
	}
}