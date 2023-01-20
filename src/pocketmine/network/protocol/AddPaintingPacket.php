<?php

namespace pocketmine\network\protocol;

class AddPaintingPacket extends PEPacket {

	const NETWORK_ID = Info::ADD_PAINTING_PACKET;
	const PACKET_NAME = "ADD_PAINTING_PACKET";

	public $eid;
	public $x;
	public $y;
	public $z;
	public $direction;
	public $title;

	public function decode($playerProtocol){}

	public function encode($playerProtocol){
		$this->reset($playerProtocol);
		$this->putEntityId($this->eid); //EntityUniqueID
		$this->putEntityId($this->eid); //EntityRuntimeID
		$this->putBlockCoords($this->x, $this->y, $this->z);
		$this->putVarInt($this->direction);
		$this->putString($this->title);
	}
}