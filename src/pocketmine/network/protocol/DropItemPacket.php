<?php

namespace pocketmine\network\protocol;

class DropItemPacket extends PEPacket{
	const NETWORK_ID = Info::DROP_ITEM_PACKET;
	const PACKET_NAME = "DROP_ITEM_PACKET";

	public $type;
	/** @var Item */
	public $item;

	public function decode($playerProtocol){
	    $this->getHeader($playerProtocol);
		$this->type = $this->getByte();
		$this->item = $this->getSlot($playerProtocol);
	}

	public function encode($playerProtocol){}
}