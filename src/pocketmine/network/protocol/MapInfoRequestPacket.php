<?php

namespace pocketmine\network\protocol;

class MapInfoRequestPacket extends PEPacket {

	const NETWORK_ID = Info::MAP_INFO_REQUEST_PACKET;
	const PACKET_NAME = "MAP_INFO_REQUEST_PACKET";

	public $uuid;

	public function decode($playerProtocol){
	    $this->getHeader($playerProtocol);
		$this->uuid = $this->getEntityId();
	}

	public function encode($playerProtocol){
		$this->reset($playerProtocol);
		$this->putEntityId($this->uuid);
	}
}