<?php

namespace pocketmine\network\protocol;

class TileEntityDataPacket extends PEPacket{
	const NETWORK_ID = Info::TILE_ENTITY_DATA_PACKET;
	const PACKET_NAME = "TILE_ENTITY_DATA_PACKET";

	public $x;
	public $y;
	public $z;
	public $namedtag;

	public function decode($playerProtocol){
	    $this->getHeader($playerProtocol);
		$this->getBlockCoords($this->x, $this->y, $this->z);
		$this->namedtag = $this->get(strlen($this->getBuffer()) - $this->getOffset());
	}

	public function encode($playerProtocol){
		$this->reset($playerProtocol);
		$this->putBlockCoords($this->x, $this->y, $this->z);
		$this->put($this->namedtag);
	}
}