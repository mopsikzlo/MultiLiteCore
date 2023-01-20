<?php

namespace pocketmine\network\protocol;

class SetSpawnPositionPacket extends PEPacket{
	const NETWORK_ID = Info::SET_SPAWN_POSITION_PACKET;
	const PACKET_NAME = "SET_SPAWN_POSITION_PACKET";

	const TYPE_PLAYER_SPAWN = 0;
	const TYPE_WORLD_SPAWN = 1;

	public $spawnType;
	public $x;
	public $y;
	public $z;
	public $spawnForced;

	public function decode($playerProtocol){}

	public function encode($playerProtocol){
		$this->reset($playerProtocol);
		$this->putVarInt($this->spawnType);
		$this->putBlockCoords($this->x, $this->y, $this->z);
		$this->putBool($this->spawnForced);
	}
}