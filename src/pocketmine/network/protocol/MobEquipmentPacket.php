<?php

namespace pocketmine\network\protocol;

class MobEquipmentPacket extends PEPacket{
	const NETWORK_ID = Info::MOB_EQUIPMENT_PACKET;
	const PACKET_NAME = "MOB_EQUIPMENT_PACKET";

	public $eid;
	public $item;
	public $slot;
	public $selectedSlot;
	public $windowId;

	public function decode($playerProtocol){
	    $this->getHeader($playerProtocol);
		$this->eid = $this->getEntityId(); //EntityRuntimeID
		$this->item = $this->getSlot($playerProtocol);
		$this->slot = $this->getByte();
		$this->selectedSlot = $this->getByte();
		$this->windowId = $this->getByte();
	}

	public function encode($playerProtocol){
		$this->reset($playerProtocol);
		$this->putEntityId($this->eid); //EntityRuntimeID
		$this->putSlot($this->item, $playerProtocol);
		$this->putByte($this->slot);
		$this->putByte($this->selectedSlot);
		$this->putByte($this->windowId);
	}
}