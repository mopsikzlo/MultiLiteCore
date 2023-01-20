<?php

namespace pocketmine\network\protocol;

use pocketmine\utils\Color;

class MapItemDataPacket extends PEPacket {
    
	const NETWORK_ID = Info::CLIENTBOUND_MAP_ITEM_DATA_PACKET;
	const PACKET_NAME = "CLIENTBOUND_MAP_ITEM_DATA_PACKET";
	
	const BITFLAG_TEXTURE_UPDATE = 0x02;
	const BITFLAG_DECORATION_UPDATE = 0x04;
	const BITFLAG_ENTITY_UPDATE = 0x08;
	
	public $mapId;
	public $type;
	public $eids = [];
	public $scale;
	public $decorations = [];
	public $width;
	public $height;
	public $xOffset = 0;
	public $yOffset = 0;
	/** @var Color[][] */
	public $colors = [];
	/** @var int */
	public $dimensionId = 0;

	public function decode($playerProtocol){}

	public function encode($playerProtocol){
		$this->reset($playerProtocol);
		$this->putVarInt($this->mapId); //entity unique ID, signed var-int
		$type = 0;
		
		if(($eidsCount = count($this->eids)) > 0){
			$type |= self::BITFLAG_ENTITY_UPDATE;
		}
		
		if(($decorationCount = count($this->decorations)) > 0){
			$type |= self::BITFLAG_DECORATION_UPDATE;
		}
		
		if(count($this->colors) > 0){
			$type |= self::BITFLAG_TEXTURE_UPDATE;
		}
		
		$this->putUnsignedVarInt($type);
		
		if ($playerProtocol >= Info::PROTOCOL_120) {
	    	$this->putByte($this->dimensionId);
		}
		
		if(($type & self::BITFLAG_ENTITY_UPDATE) !== 0){ //TODO: find out what these are for
			$this->putUnsignedVarInt($eidsCount);
			foreach($this->eids as $eid){
				$this->putVarInt($eid);
			}
		}
		
		if(($type & (self::BITFLAG_TEXTURE_UPDATE | self::BITFLAG_DECORATION_UPDATE)) !== 0){
			$this->putByte($this->scale);
		}
		
		if(($type & self::BITFLAG_DECORATION_UPDATE) !== 0){
			$this->putUnsignedVarInt($decorationCount);
			foreach($this->decorations as $decoration){
			    if ($playerProtocol >= Info::PROTOCOL_120) {
			    	$this->putVarInt(($decoration["rot"] & 0x0f) | ($decoration["img"] << 4));
			    } else {
			    	$this->putByte($decoration["rot"]);
			    	$this->putByte($decoration["img"]);
			    }
			    
				$this->putByte($decoration["xOffset"]);
				$this->putByte($decoration["yOffset"]);
				$this->putString($decoration["label"]);
				
				assert($decoration["color"] instanceof Color);
				
				if ($playerProtocol >= Info::PROTOCOL_120) {
			    	$this->putUnsignedVarInt($decoration["color"]->toABGR());
				} else {
				    $this->putLInt($decoration["color"]->toARGB());
				}
			}
		}
		
		if(($type & self::BITFLAG_TEXTURE_UPDATE) !== 0){
			$this->putVarInt($this->width);
			$this->putVarInt($this->height);
			$this->putVarInt($this->xOffset);
			$this->putVarInt($this->yOffset);
			
			if ($playerProtocol >= Info::PROTOCOL_120) {
			    $this->putUnsignedVarInt($this->width * $this->height); //list count, but we handle it as a 2D array... thanks for the confusion mojang
			}
			
			for($y = 0; $y < $this->height; ++$y){
				for($x = 0; $x < $this->width; ++$x){
					$this->putUnsignedVarInt($this->colors[$y][$x]->toABGR());
				}
			}
		}
	}
}