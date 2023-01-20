<?php

namespace pocketmine\network\protocol;

class SpawnExperienceOrbPacket extends PEPacket {
	const NETWORK_ID = Info::SPAWN_EXPERIENCE_ORB_PACKET;
	const PACKET_NAME = "SPAWN_EXPERIENCE_ORB_PACKET";

	public $x;
	public $y;
	public $z;
	public $amount;

	public function decode($playerProtocol){}

	public function encode($playerProtocol){
		$this->reset($playerProtocol);
		$this->putVector3f($this->x, $this->y, $this->z);
		$this->putVarInt($this->amount);
	}
}