<?php

namespace pocketmine\network\protocol;

use pocketmine\entity\Attribute;

class UpdateAttributesPacket extends PEPacket{
	const NETWORK_ID = Info::UPDATE_ATTRIBUTES_PACKET;
	const PACKET_NAME = "UPDATE_ATTRIBUTES_PACKET";

	public $entityId;

	/** @var Attribute[] */
	public $entries = [];

	public function decode($playerProtocol){}

	public function encode($playerProtocol){
		$this->reset($playerProtocol);
		$this->putEntityId($this->entityId);
		$this->putUnsignedVarInt(count($this->entries));
		foreach($this->entries as $entry){
			$this->putLFloat($entry->getMinValue());
			$this->putLFloat($entry->getMaxValue());
			$this->putLFloat($entry->getValue());
			$this->putLFloat($entry->getDefaultValue());
			$this->putString($entry->getName());
		}
	}
}