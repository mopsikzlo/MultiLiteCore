<?php

namespace pocketmine\network\protocol;

class ChangeDimensionPacket extends PEPacket {

	const NETWORK_ID = Info::CHANGE_DIMENSION_PACKET;
	const PACKET_NAME = "CHANGE_DIMENSION_PACKET";

	const DIMENSION_NORMAL = 0;
	const DIMENSION_NETHER = 1;
	const DIMENSION_END = 2;

	public $dimension;

	public $x;
	public $y;
	public $z;
	public $respawn = false;

	public function decode($playerProtocol){}

	public function encode($playerProtocol){
		$this->reset($playerProtocol);
		$this->putVarInt($this->dimension);
		$this->putVector3f($this->x, $this->y, $this->z);
		$this->putBool($this->respawn);
	}
}