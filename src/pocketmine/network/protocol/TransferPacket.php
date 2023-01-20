<?php

namespace pocketmine\network\protocol;

class TransferPacket extends PEPacket {
	const NETWORK_ID = Info::TRANSFER_PACKET;
	const PACKET_NAME = "TRANSFER_PACKET";

	public $address;
	public $port = 19132; //default port

	public function decode($playerProtocol){}

	public function encode($playerProtocol){
		$this->reset($playerProtocol);
		$this->putString($this->address);
		$this->putLShort($this->port);
	}
}