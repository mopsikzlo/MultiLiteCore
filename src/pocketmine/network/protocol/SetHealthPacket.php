<?php

namespace pocketmine\network\protocol;

class SetHealthPacket extends PEPacket{	
	const NETWORK_ID =  Info::SET_HEALTH_PACKET;
	const PACKET_NAME = "SET_HEALTH_PACKET";

	public $health;

	public function decode($playerProtocol){}

	public function encode($playerProtocol){
		$this->reset($playerProtocol);
		$this->putVarInt($this->health);
	}
}