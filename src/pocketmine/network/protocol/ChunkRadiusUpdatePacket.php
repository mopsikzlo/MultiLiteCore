<?php

namespace pocketmine\network\protocol;

class ChunkRadiusUpdatePacket extends PEPacket{
	const NETWORK_ID = Info::CHUNK_RADIUS_UPDATE_PACKET;
	const PACKET_NAME = "CHUNK_RADIUS_UPDATE_PACKET";

	public $radius;

	public function decode($playerProtocol){}

	public function encode($playerProtocol){
		$this->reset($playerProtocol);
		$this->putVarInt($this->radius);
	}
}