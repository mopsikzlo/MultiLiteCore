<?php

namespace pocketmine\network\protocol;

class AddItemEntityPacket extends PEPacket{
    
	const NETWORK_ID = Info::ADD_ITEM_ENTITY_PACKET;
	const PACKET_NAME = "ADD_ITEM_ENTITY_PACKET";

	public $eid;
	public $item;
	public $x;
	public $y;
	public $z;
	public $speedX;
	public $speedY;
	public $speedZ;

	public function decode($playerProtocol){}

	public function encode($playerProtocol){
		$this->reset($playerProtocol);
		$this->putEntityId($this->eid); //EntityUniqueID
		$this->putEntityId($this->eid); //EntityRuntimeID
		$this->putSlot($this->item, $playerProtocol);
		$this->putVector3f($this->x, $this->y, $this->z);
		$this->putVector3f($this->speedX, $this->speedY, $this->speedZ);
	}
}