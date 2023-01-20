<?php

namespace pocketmine\network\protocol;

class SetCommandsEnabledPacket extends PEPacket{
	const NETWORK_ID = Info::SET_COMMANDS_ENABLED_PACKET;
	const PACKET_NAME = "SET_COMMANDS_ENABLED_PACKET";

	public $enabled;

	public function decode($playerProtocol){}

	public function encode($playerProtocol){
		$this->reset($playerProtocol);
		$this->putBool($this->enabled);
	}
}