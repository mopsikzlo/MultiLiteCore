<?php

namespace pocketmine\network\protocol;

class SetEntityDataPacket extends PEPacket{
	const NETWORK_ID = Info::SET_ENTITY_DATA_PACKET;
	const PACKET_NAME = "SET_ENTITY_DATA_PACKET";

	public $eid;
	public $metadata;

	public function decode($playerProtocol){}

	public function encode($playerProtocol){
		$this->reset($playerProtocol);
		$this->putEntityId($this->eid);
		$this->putEntityMetadata($this->metadata, $playerProtocol);
	}
}