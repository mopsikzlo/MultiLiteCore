<?php

namespace pocketmine\network\protocol;

class SetDifficultyPacket extends PEPacket{
	const NETWORK_ID = Info::SET_DIFFICULTY_PACKET;
	const PACKET_NAME = "SET_DIFFICULTY_PACKET";

	public $difficulty;

	public function decode($playerProtocol){}

	public function encode($playerProtocol){
		$this->reset($playerProtocol);
		$this->putUnsignedVarInt($this->difficulty);
	}
}