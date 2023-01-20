<?php

namespace pocketmine\network\protocol;

class UseItemPacket extends PEPacket{
	const NETWORK_ID = Info::USE_ITEM_PACKET;
	const PACKET_NAME = "USE_ITEM_PACKET";

	public $x;
	public $y;
	public $z;
	public $face;
	public $item;
	public $fx;
	public $fy;
	public $fz;
	public $posX;
	public $posY;
	public $posZ;
	public $hotbarSlot;
	public $interactBlockId;

	public function decode($playerProtocol){
		$this->getHeader($playerProtocol);
		$this->x = $this->getVarInt();
		$this->y = $this->getUnsignedVarInt();
		$this->z = $this->getVarInt();
		$this->interactBlockId =  $this->getUnsignedVarInt();
		$this->face = $this->getVarInt();
		$this->fx = $this->getLFloat();
		$this->fy = $this->getLFloat();
		$this->fz = $this->getLFloat();
		$this->posX = $this->getLFloat();
		$this->posY = $this->getLFloat();
		$this->posZ = $this->getLFloat();
		$this->hotbarSlot = $this->getVarInt();
		$this->item = $this->getSlot($playerProtocol);
	}

	public function encode($playerProtocol){}
}