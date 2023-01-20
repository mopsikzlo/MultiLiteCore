<?php

namespace pocketmine\network\protocol;

class BatchPacket extends PEPacket{
	const NETWORK_ID = Info::BATCH_PACKET;
	const PACKET_NAME = "BATCH_PACKET";

	public $payload;

	public function decode($playerProtocol){
		$this->payload = $this->get(strlen($this->getBuffer()) - $this->getOffset());
	}

	public function encode($playerProtocol){
		$this->setBuffer($this->payload);
	}
}