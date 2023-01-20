<?php

namespace pocketmine\network\protocol;

class TileEventPacket extends PEPacket{
	const NETWORK_ID = Info::TILE_EVENT_PACKET;
	const PACKET_NAME = "TILE_EVENT_PACKET";

	public $x;
	public $y;
	public $z;
	public $case1;
	public $case2;

	public function decode($playerProtocol){}

	public function encode($playerProtocol){
		$this->reset($playerProtocol);
		$this->putBlockCoords($this->x, $this->y, $this->z);
		$this->putVarInt($this->case1);
		$this->putVarInt($this->case2);
	}
}