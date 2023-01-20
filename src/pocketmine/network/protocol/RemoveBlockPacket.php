<?php

namespace pocketmine\network\protocol;

class RemoveBlockPacket extends PEPacket{
	const NETWORK_ID = Info::REMOVE_BLOCK_PACKET;
	const PACKET_NAME = "REMOVE_BLOCK_PACKET";

	public $x;
	public $y;
	public $z;

	public function decode($playerProtocol){
	    $this->getHeader($playerProtocol);
		$this->getBlockCoords($this->x, $this->y, $this->z);
	}
	
	public function encode($playerProtocol){}
}