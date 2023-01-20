<?php

namespace pocketmine\network\protocol;

class ContainerSetSlotPacket extends PEPacket{
    
	const NETWORK_ID = Info::CONTAINER_SET_SLOT_PACKET;
	const PACKET_NAME = "CONTAINER_SET_SLOT_PACKET";

	public $windowid;
	public $slot;
	/** @var Item */
	public $item;
	public $hotbarSlot = 0;
	public $selectSlot = 0;

	public function decode($playerProtocol){
	    $this->getHeader($playerProtocol);
		$this->windowid = $this->getByte();
		$this->slot = $this->getVarInt();
		$this->hotbarSlot = $this->getVarInt();
		$this->item = $this->getSlot($playerProtocol);
		$this->selectSlot = $this->getByte();
	}

	public function encode($playerProtocol){
		$this->reset($playerProtocol);
		$this->putByte($this->windowid);
		$this->putVarInt($this->slot);
		$this->putVarInt($this->hotbarSlot);
		$this->putSlot($this->item, $playerProtocol);
		$this->putByte($this->selectSlot);
	}
}