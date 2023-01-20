<?php

namespace pocketmine\network\protocol;

class InteractPacket extends PEPacket{
	
	const NETWORK_ID = Info::INTERACT_PACKET;
	const PACKET_NAME = "INTERACT_PACKET";

	const ACTION_RIGHT_CLICK = 1;
	const ACTION_LEFT_CLICK = 2;
	const ACTION_LEAVE_VEHICLE = 3;
	const ACTION_MOUSEOVER = 4;

	public $action;
	public $eid;
	public $target;

	public function decode($playerProtocol){
	    $this->getHeader($playerProtocol);
		$this->action = $this->getByte();
		$this->target = $this->getEntityId();
	}

	public function encode($playerProtocol){
		$this->reset($playerProtocol);
		$this->putByte($this->action);
		$this->putEntityId($this->target);
		if ($playerProtocol >= Info::PROTOCOL_120) {
			/** @todo do it right */
			if ($this->action == self::ACTION_MOUSEOVER) {
				$this->putLFloat(0); // position X
				$this->putLFloat(0); // position Y
				$this->putLFloat(0); // position Z
			}
		}
	}
}