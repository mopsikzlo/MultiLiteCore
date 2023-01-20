<?php

namespace pocketmine\network\protocol;

class MobArmorEquipmentPacket extends PEPacket{
	const NETWORK_ID = Info::MOB_ARMOR_EQUIPMENT_PACKET;
	const PACKET_NAME = "MOB_ARMOR_EQUIPMENT_PACKET";

	public $eid;
	public $slots = [];

	public function decode($playerProtocol){
	    $this->getHeader($playerProtocol);
		$this->eid = $this->getEntityId();
		$this->slots[0] = $this->getSlot($playerProtocol);
		$this->slots[1] = $this->getSlot($playerProtocol);
		$this->slots[2] = $this->getSlot($playerProtocol);
		$this->slots[3] = $this->getSlot($playerProtocol);
	}

	public function encode($playerProtocol){
		$this->reset($playerProtocol);
		$this->putEntityId($this->eid);
		$this->putSlot($this->slots[0], $playerProtocol);
		$this->putSlot($this->slots[1], $playerProtocol);
		$this->putSlot($this->slots[2], $playerProtocol);
		$this->putSlot($this->slots[3], $playerProtocol);
	}
}