<?php

namespace pocketmine\network\protocol;

use pocketmine\entity\Attribute;

class AddEntityPacket extends PEPacket {

	const NETWORK_ID = Info::ADD_ENTITY_PACKET;
	const PACKET_NAME = "ADD_ENTITY_PACKET";

	public $eid;
	public $type;
	public $x;
	public $y;
	public $z;
	public $speedX = 0.0;
	public $speedY = 0.0;
	public $speedZ = 0.0;
	public $yaw = 0.0;
	public $pitch = 0.0;
	/** @var Attribute[] */
	public $attributes = [];
	public $metadata = [];
	public $links = [];

	/**
	 *
	 */
	public function decode($playerProtocol){}

	/**
	 *
	 */
	public function encode($playerProtocol){
		$this->reset($playerProtocol);
		$this->putEntityId($this->eid); //EntityUniqueID - TODO: verify this
		$this->putEntityId($this->eid);
		$this->putUnsignedVarInt($this->type);
		$this->putVector3f($this->x, $this->y, $this->z);
		$this->putVector3f($this->speedX, $this->speedY, $this->speedZ);
		$this->putLFloat($this->pitch);
		$this->putLFloat($this->yaw);
		$this->putUnsignedVarInt(count($this->attributes));
		foreach($this->attributes as $entry){
			$this->putString($entry->getName());
			$this->putLFloat($entry->getMinValue());
			$this->putLFloat($entry->getValue());
			$this->putLFloat($entry->getMaxValue());
		}
		$this->putEntityMetadata($this->metadata, $playerProtocol);
		$this->putUnsignedVarInt(count($this->links));
		foreach($this->links as $link){
			$this->putEntityId($link[0]);
			$this->putEntityId($link[1]);
			$this->putByte($link[2]);
		}
	}
}