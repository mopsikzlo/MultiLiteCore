<?php

namespace pocketmine\network\protocol;

class ResourcePacksInfoPacket extends PEPacket {

	const NETWORK_ID = Info::RESOURCE_PACKS_INFO_PACKET;
	const PACKET_NAME = "RESOURCE_PACKS_INFO_PACKET";

	public $mustAccept = false; //force client to use selected resource packs
	/** @var ResourcePackInfoEntry */
	public $behaviorPackEntries = [];
	/** @var ResourcePackInfoEntry */
	public $resourcePackEntries = [];

	public function decode($playerProtocol){}

	public function encode($playerProtocol){
		$this->reset($playerProtocol);

		$this->putBool($this->mustAccept);
		$this->putLShort(count($this->behaviorPackEntries));
		foreach($this->behaviorPackEntries as $entry){
			$this->putString($entry->getPackId());
			$this->putString($entry->getPackVersion());
			$this->putLLong($entry->getPackSize());
			$this->putString(''); //TODO: encryption key
			$this->putString(''); //TODO: subpack name
		}
		$this->putLShort(count($this->resourcePackEntries));
		foreach($this->resourcePackEntries as $entry){
			$this->putString($entry->getPackId());
			$this->putString($entry->getPackVersion());
			$this->putLLong($entry->getPackSize());
			$this->putString(''); //TODO: encryption key
			$this->putString(''); //TODO: subpack name
		}
	}
}