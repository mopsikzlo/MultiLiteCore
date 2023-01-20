<?php

namespace pocketmine\network\protocol;

class RemoveEntityPacket extends PEPacket{
	const NETWORK_ID = Info::REMOVE_ENTITY_PACKET;
	const PACKET_NAME = "REMOVE_ENTITY_PACKET";

	public $eid;

	public function decode($playerProtocol){}

	public function encode($playerProtocol){
		$this->reset($playerProtocol);
		$this->putEntityId($this->eid);
	}
}