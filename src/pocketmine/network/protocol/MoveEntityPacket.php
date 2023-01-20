<?php

namespace pocketmine\network\protocol;

class MoveEntityPacket extends PEPacket{
	const NETWORK_ID = Info::MOVE_ENTITY_PACKET;
	const PACKET_NAME = "MOVE_ENTITY_PACKET";

	public $eid;
	public $x;
	public $y;
	public $z;
	public $yaw;
	public $headYaw;
	public $pitch;
	public $byte1;

	public function decode($playerProtocol){
	    $this->getHeader($playerProtocol);
		$this->eid = $this->getEntityId();
		$this->getVector3f($this->x, $this->y, $this->z);
		$this->pitch = $this->getByte() * (360.0 / 256);
		$this->yaw = $this->getByte() * (360.0 / 256);
		$this->headYaw = $this->getByte() * (360.0 / 256);
		$this->byte1 = $this->getByte();
	}

	public function encode($playerProtocol){
		$this->reset($playerProtocol);
		$this->putEntityId($this->eid);
		$this->putVector3f($this->x, $this->y, $this->z);
		$this->putByte($this->pitch / (360.0 / 256));
		$this->putByte($this->yaw / (360.0 / 256));
		$this->putByte($this->headYaw / (360.0 / 256));
		$this->putByte($this->byte1);
	}
}