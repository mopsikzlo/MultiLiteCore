<?php

namespace pocketmine\network\protocol;

class FullChunkDataPacket extends PEPacket{
	const NETWORK_ID = Info::FULL_CHUNK_DATA_PACKET;
	const PACKET_NAME = "FULL_CHUNK_DATA_PACKET";

	public $chunkX;
	public $chunkZ;
	public $data;

	public function decode($playerProtocol){}

	public function encode($playerProtocol){
		$this->reset($playerProtocol);
		$this->putVarInt($this->chunkX);
		$this->putVarInt($this->chunkZ);
		$this->putString($this->data);
	}
}