<?php

namespace pocketmine\network\protocol;

class AnimatePacket extends PEPacket {
	const NETWORK_ID = Info::ANIMATE_PACKET;
	const PACKET_NAME = "ANIMATE_PACKET";

	const ACTION_SWING_ARM = 1;
	const ACTION_STOP_SLEEP = 3;
	const ACTION_CRITICAL_HIT = 4;

	public $action;
	public $eid;
	public $float;

	public function decode($playerProtocol){
	    $this->getHeader($playerProtocol);
		$this->action = $this->getVarInt();
		$this->eid = $this->getEntityId();
		if($this->float & 0x80){
			$this->float = $this->getLFloat();
		}
	}

	public function encode($playerProtocol){
		$this->reset($playerProtocol);
		$this->putVarInt($this->action);
		$this->putEntityId($this->eid);
		if($this->float & 0x80){
			$this->putLFloat($this->float);
		}
	}
}