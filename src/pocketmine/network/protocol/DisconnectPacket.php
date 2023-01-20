<?php

namespace pocketmine\network\protocol;

use pocketmine\utils\BinaryStream;

class DisconnectPacket extends PEPacket {
	const NETWORK_ID = Info::DISCONNECT_PACKET;
	const PACKET_NAME = "DISCONNECT_PACKET";
	
	public $hideDisconnectionScreen = false;
	public $message;

	public function reset($playerProtocol = 0) {
		if (isset(self::$packetsIds[$playerProtocol])) {
			parent::reset($playerProtocol);
		} else {
			BinaryStream::reset();
			$this->putByte(Info::DISCONNECT_PACKET);
		}
	}

	public function decode($playerProtocol){
	    $this->getHeader($playerProtocol);
		$this->hideDisconnectionScreen = $this->getBool();
		$this->message = $this->getString();
	}

	public function encode($playerProtocol){
		$this->reset($playerProtocol);
		$this->putBool($this->hideDisconnectionScreen);
		if(!$this->hideDisconnectionScreen){
			$this->putString($this->message);
		}
	}
}