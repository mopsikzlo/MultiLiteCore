<?php

namespace pocketmine\network\protocol;

class ResourcePackDataInfoPacket extends PEPacket {
	const NETWORK_ID = Info::RESOURCE_PACK_DATA_INFO_PACKET;
	const PACKET_NAME = "RESOURCE_PACK_DATA_INFO_PACKET";

	public $packId;
	public $maxChunkSize;
	public $chunkCount;
	public $compressedPackSize;
	public $sha256;

	public function decode($playerProtocol){}

	public function encode($playerProtocol){
		$this->reset($playerProtocol);
		$this->putString($this->packId);
		$this->putLInt($this->maxChunkSize);
		$this->putLInt($this->chunkCount);
		$this->putLLong($this->compressedPackSize);
		$this->putString($this->sha256);
	}
}