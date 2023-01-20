<?php

namespace pocketmine\network\protocol;

class SetEntityLinkPacket extends PEPacket{
	const NETWORK_ID = Info::SET_ENTITY_LINK_PACKET;
	const PACKET_NAME = "SET_ENTITY_LINK_PACKET";

	const TYPE_REMOVE = 0;
	const TYPE_RIDE = 1;
	const TYPE_PASSENGER = 2;

	public $from;
	public $to;
	public $type;

	public function decode($playerProtocol){}

	public function encode($playerProtocol){
		$this->reset($playerProtocol);
		$this->putEntityId($this->from);
		$this->putEntityId($this->to);
		$this->putByte($this->type);
	}
}