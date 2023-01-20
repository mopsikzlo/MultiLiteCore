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

namespace pocketmine\network\protocol;

#include <rules/DataPacket.h>

use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\utils\BinaryStream;
use pocketmine\utils\Utils;
use pocketmine\utils\MetadataConvertor;

abstract class DataPacket extends BinaryStream {

	const NETWORK_ID = 0;
	const PACKET_NAME = "DATA_PACKET";

	public $isEncoded = false;

	protected static $packetsIds = [];

	/**
	 * @return int
	 */
	public function pid(){
		return $this::NETWORK_ID;
	}

	public function getName(){
		return $this::PACKET_NAME;
	}

	public function pname(){
		return $this::PACKET_NAME;
	}

	/**
	 * @return $this
	 */
	public function clean(){
		$this->buffer = null;
		$this->isEncoded = false;
		$this->offset = 0;

		return $this;
	}

	/**
	 * @return array
	 */
	public function __debugInfo(){
		$data = [];
		foreach($this as $k => $v){
			if($k === "buffer"){
				$data[$k] = bin2hex($v);
			}elseif(is_string($v) or (is_object($v) and method_exists($v, "__toString"))){
				$data[$k] = Utils::printable((string) $v);
			}else{
				$data[$k] = $v;
			}
		}

		return $data;
	}

	/**
	 * @param array $metadata
	 * @param int $playerProtocol
	 */
	public function putEntityMetadata(array $metadata, int $playerProtocol){
        $metadata = MetadataConvertor::updateMeta($metadata, $playerProtocol);
		$this->putUnsignedVarInt(count($metadata));
		foreach($metadata as $key => $d){
			$this->putUnsignedVarInt($key); //data key
			$this->putUnsignedVarInt($d[0]); //data type
			switch($d[0]){
				case Entity::DATA_TYPE_BYTE:
					$this->putByte($d[1]);
					break;
				case Entity::DATA_TYPE_SHORT:
					$this->putLShort($d[1]); //SIGNED short!
					break;
				case Entity::DATA_TYPE_INT:
					$this->putVarInt($d[1]);
					break;
				case Entity::DATA_TYPE_FLOAT:
					$this->putLFloat($d[1]);
					break;
				case Entity::DATA_TYPE_STRING:
					$this->putString($d[1]);
					break;
				case Entity::DATA_TYPE_SLOT:
					//TODO: change this implementation (use objects)
					$this->putSlot(Item::get($d[1][0], $d[1][2], $d[1][1]), $playerProtocol); //ID, damage, count
					break;
				case Entity::DATA_TYPE_POS:
					//TODO: change this implementation (use objects)
					$this->putVarInt($d[1][0]); //x
					$this->putVarInt($d[1][1]); //y (SIGNED)
					$this->putVarInt($d[1][2]); //z
					break;
				case Entity::DATA_TYPE_LONG:
					$this->putVarInt($d[1]); //TODO: varint64 support
					break;
				case Entity::DATA_TYPE_VECTOR3F:
					//TODO: change this implementation (use objects)
					$this->putVector3f($d[1][0], $d[1][1], $d[1][2]); //x, y, z
			}
		}
	}

	public static function initPackets() {
		$oClass = new \ReflectionClass ('pocketmine\network\protocol\Info110');
		self::$packetsIds[Info::PROTOCOL_110] = $oClass->getConstants();
		$oClass = new \ReflectionClass ('pocketmine\network\protocol\Info120');
		self::$packetsIds[Info::PROTOCOL_120] = $oClass->getConstants();
	}
}