<?php

namespace pocketmine\network\protocol;

class PlayerInputPacket extends PEPacket {

	const NETWORK_ID = Info::PLAYER_INPUT_PACKET;
	const PACKET_NAME = "PLAYER_INPUT_PACKET";

	public $motionX;
	public $motionY;
	public $unknownBool1;
	public $unknownBool2;

	public function decode($playerProtocol){
	    $this->getHeader($playerProtocol);
		$this->motionX = $this->getLFloat();
		$this->motionY = $this->getLFloat();
		$this->unknownBool1 = $this->getBool();
		$this->unknownBool2 = $this->getBool();
	}

	public function encode($playerProtocol){}
}