<?php

namespace pocketmine\network\protocol;

class ContainerOpenPacket extends PEPacket{
	
	const NETWORK_ID = Info::CONTAINER_OPEN_PACKET;
	const PACKET_NAME = "CONTAINER_OPEN_PACKET";

	public $windowid;
	public $type;
	public $x;
	public $y;
	public $z;
	public $entityId = -1;

	public function decode($playerProtocol){}

	public function encode($playerProtocol){
		$this->reset($playerProtocol);
		$this->putByte($this->windowid);
		$this->putByte($this->type);
		$this->putBlockCoords($this->x, $this->y, $this->z);
		$this->putEntityId($this->entityId);
	}
}