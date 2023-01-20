<?php

namespace pocketmine\network\protocol;

class RequestChunkRadiusPacket extends PEPacket{
	const NETWORK_ID = Info::REQUEST_CHUNK_RADIUS_PACKET;
	const PACKET_NAME = "REQUEST_CHUNK_RADIUS_PACKET";

	public $radius;

	public function decode($playerProtocol){
	    $this->getHeader($playerProtocol);
		$this->radius = $this->getVarInt();
	}

	public function encode($playerProtocol){}
}