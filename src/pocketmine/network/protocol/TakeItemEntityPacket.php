<?php

namespace pocketmine\network\protocol;

class TakeItemEntityPacket extends PEPacket{
	const NETWORK_ID = Info::TAKE_ITEM_ENTITY_PACKET;
	const PACKET_NAME = "TAKE_ITEM_ENTITY_PACKET";

	public $target;
	public $eid;

	public function decode($playerProtocol){}

	public function encode($playerProtocol){
		$this->reset($playerProtocol);
		$this->putEntityId($this->target);
		$this->putEntityId($this->eid);
	}
}