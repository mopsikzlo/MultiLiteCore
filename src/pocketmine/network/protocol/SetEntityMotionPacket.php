<?php

namespace pocketmine\network\protocol;

class SetEntityMotionPacket extends PEPacket{
	const NETWORK_ID = Info::SET_ENTITY_MOTION_PACKET;
	const PACKET_NAME = "SET_ENTITY_MOTION_PACKET";

	public $eid;
	public $motionX;
	public $motionY;
	public $motionZ;

	/**
	 * @return $this
	 */
	public function clean(){
		$this->entities = [];

		return parent::clean();
	}

	public function decode($playerProtocol){}

	public function encode($playerProtocol){
		$this->reset($playerProtocol);
		$this->putEntityId($this->eid);
		$this->putVector3f($this->motionX, $this->motionY, $this->motionZ);
	}
}