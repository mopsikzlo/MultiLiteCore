<?php

namespace pocketmine\network\protocol;

class ContainerSetDataPacket extends PEPacket{
	const NETWORK_ID = Info::CONTAINER_SET_DATA_PACKET;
	const PACKET_NAME = "CONTAINER_SET_DATA_PACKET";

	public $windowid;
	public $property;
	public $value;

	public function decode($playerProtocol){}

	public function encode($playerProtocol){
		$this->reset($playerProtocol);
		$this->putByte($this->windowid);
		$this->putVarInt($this->property);
		$this->putVarInt($this->value);
	}
}