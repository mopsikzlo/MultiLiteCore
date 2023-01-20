<?php

namespace pocketmine\network\protocol;

class ContainerClosePacket extends PEPacket{
    
	const NETWORK_ID = Info::CONTAINER_CLOSE_PACKET;
	const PACKET_NAME = "CONTAINER_CLOSE_PACKET";
	
	public $windowid;

	public function decode($playerProtocol){
	    $this->getHeader($playerProtocol);
		$this->windowid = $this->getByte();
	}

	public function encode($playerProtocol){
		$this->reset($playerProtocol);
		$this->putByte($this->windowid);
	}
}