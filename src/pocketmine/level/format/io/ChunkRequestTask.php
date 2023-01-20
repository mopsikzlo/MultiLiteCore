<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 *
*/

namespace pocketmine\level\format\io;

use pocketmine\level\format\Chunk;
use pocketmine\level\Level;

use pocketmine\utils\Binary;

use pocketmine\network\protocol\BatchPacket;
use pocketmine\network\protocol\FullChunkDataPacket;
use pocketmine\network\protocol\Info;

use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;

class ChunkRequestTask extends AsyncTask {

	protected $levelId;

	protected $chunk;
	protected $chunkX;
	protected $chunkZ;

	protected $compressionLevel;

    protected $protocols = [Info::PROTOCOL_110, Info::PROTOCOL_120];

	public function __construct(Level $level, int $chunkX, int $chunkZ, Chunk $chunk){
		$this->levelId = $level->getId();
		$this->compressionLevel = $level->getServer()->networkCompressionLevel;

		$this->chunk = $chunk->fastSerialize();
		$this->chunkX = $chunk->getX();
		$this->chunkZ = $chunk->getZ();
	}

	public function onRun(){
		$chunk = Chunk::fastDeserialize($this->chunk);

		$result = [];

		foreach ($this->protocols as $protocol) {
			$pk = new FullChunkDataPacket();
			$pk->chunkX = $this->chunkX;
			$pk->chunkZ = $this->chunkZ;
			$pk->data = $chunk->networkSerialize($protocol);
            $pk->encode($protocol);

	    	$batch = new BatchPacket();
	    	$batch->payload = zlib_encode(Binary::writeUnsignedVarInt(strlen($pk->buffer)) . $pk->buffer, ZLIB_ENCODING_DEFLATE, $this->compressionLevel);
	    	$batch->encode($protocol);

			$result[$protocol] = $batch->getBuffer();
		}

		$this->setResult($result);
	}

	public function onCompletion(Server $server){
		$level = $server->getLevel($this->levelId);
		if($level instanceof Level){
			if($this->hasResult()){
				$level->chunkRequestCallback($this->chunkX, $this->chunkZ, $this->getResult());
			}else{
				$server->getLogger()->error("Chunk request for level #" . $this->levelId . ", x=" . $this->chunkX . ", z=" . $this->chunkZ . " doesn't have any result data");
			}
		}else{
			$server->getLogger()->debug("Dropped chunk task due to level not loaded");
		}
	}

}