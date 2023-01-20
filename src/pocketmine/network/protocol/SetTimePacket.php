<?php

namespace pocketmine\network\protocol;

class SetTimePacket extends PEPacket{
	const NETWORK_ID = Info::SET_TIME_PACKET;
	const PACKET_NAME = "SET_TIME_PACKET";

	public $time;
	public $started = true;

	public function decode($playerProtocol){}

	public function encode($playerProtocol){
		$this->reset($playerProtocol);
		$this->putVarInt($this->time);
		if ($playerProtocol < Info::PROTOCOL_120) {
	    	$this->putBool($this->started);
		}
	}
}