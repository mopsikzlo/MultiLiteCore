<?php

namespace pocketmine\network\protocol;

class HurtArmorPacket extends PEPacket{
	const NETWORK_ID = Info::HURT_ARMOR_PACKET;
	const PACKET_NAME = "HURT_ARMOR_PACKET";

	public $health;

	public function decode($playerProtocol){}

	public function encode($playerProtocol){
		$this->reset($playerProtocol);
		$this->putVarInt($this->health);
	}
}