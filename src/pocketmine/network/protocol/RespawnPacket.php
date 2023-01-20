<?php

namespace pocketmine\network\protocol;

class RespawnPacket extends PEPacket{
	const NETWORK_ID = Info::RESPAWN_PACKET;
	const PACKET_NAME = "RESPAWN_PACKET";

	public $x;
	public $y;
	public $z;

	public function decode($playerProtocol){
	    $this->getHeader($playerProtocol);
		$this->x = $this->getLFloat();
		$this->y = $this->getLFloat();
		$this->z = $this->getLFloat();
	}

	public function encode($playerProtocol){
		$this->reset($playerProtocol);
		$this->putLFloat($this->x);
		$this->putLFloat($this->y);
		$this->putLFloat($this->z);
	}
}