<?php

namespace pocketmine\network\protocol;

class PlayerActionPacket extends PEPacket{
	const NETWORK_ID = Info::PLAYER_ACTION_PACKET;
	const PACKET_NAME = "PLAYER_ACTION_PACKET";

	public $eid;
	public $action;
	public $x;
	public $y;
	public $z;
	public $face;

	public function decode($playerProtocol){
	    $this->getHeader($playerProtocol);
		$this->eid = $this->getEntityId();
		$this->action = $this->getVarInt();
		$this->getBlockCoords($this->x, $this->y, $this->z);
		$this->face = $this->getVarInt();
	}

	public function encode($playerProtocol){
		$this->reset($playerProtocol);
		$this->putEntityId($this->eid);
		$this->putVarInt($this->action);
		$this->putBlockCoords($this->x, $this->y, $this->z);
		$this->putVarInt($this->face);
	}
}