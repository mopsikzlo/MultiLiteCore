<?php

namespace pocketmine\network\protocol;

class StartGamePacket extends PEPacket{
	const NETWORK_ID = Info::START_GAME_PACKET;
	const PACKET_NAME = "START_GAME_PACKET";

	public $entityUniqueId;
	public $entityRuntimeId;
	public $playerGamemode;
	public $x;
	public $y;
	public $z;
	public $pitch;
	public $yaw;
	public $seed;
	public $dimension;
	public $generator = 1; //default infinite - 0 old, 1 infinite, 2 flat
	public $worldGamemode;
	public $difficulty;
	public $spawnX;
	public $spawnY;
	public $spawnZ;
	public $hasAchievementsDisabled = 1;
	public $dayCycleStopTime = -1; //-1 = not stopped, any positive value = stopped at that time
	public $eduMode = 0;
	public $rainLevel;
	public $lightningLevel;
	public $commandsEnabled;
	public $isTexturePacksRequired = 0;
	public $levelId = "";
	public $worldName;
	public $premiumWorldTemplateId = "";

	public static $defaultRules = [
		['name' => 'naturalregeneration', 'type' => 1, 'value' => 0],
		['name' => 'showcoordinates', 'type' => 1, 'value' => 1]
	];

	public function decode($playerProtocol){}

	public function encode($playerProtocol){
		$this->reset($playerProtocol);
		$this->putEntityId($this->entityUniqueId); //EntityUniqueID
		$this->putEntityId($this->entityRuntimeId); //EntityRuntimeID
		$this->putVarInt($this->playerGamemode); //client gamemode, other field is world gamemode
		$this->putVector3f($this->x, $this->y, $this->z);
		$this->putLFloat($this->pitch);
		$this->putLFloat($this->yaw);
		$this->putVarInt($this->seed);
		$this->putVarInt($this->dimension);
		$this->putVarInt($this->generator);
		$this->putVarInt($this->worldGamemode);
		$this->putVarInt($this->difficulty);
		$this->putBlockCoords($this->spawnX, $this->spawnY, $this->spawnZ);
		$this->putBool($this->hasAchievementsDisabled);
		$this->putVarInt($this->dayCycleStopTime);
		$this->putBool($this->eduMode);
		$this->putLFloat($this->rainLevel);
		$this->putLFloat($this->lightningLevel);
		if ($playerProtocol >= Info::PROTOCOL_120) {
			$this->putBool(true); // is multiplayer game
			$this->putBool(true); // Broadcast to LAN?
			$this->putBool(true); // Broadcast to XBL?
		}
		$this->putBool($this->commandsEnabled);
		$this->putBool($this->isTexturePacksRequired);
		if ($playerProtocol >= Info::PROTOCOL_120) {
			$this->putUnsignedVarInt(count(self::$defaultRules)); // rules count
			foreach (self::$defaultRules as $rule) {
				$this->putString($rule['name']);
				$this->putUnsignedVarInt($rule['type']);
				switch ($rule['type']) {
					case 1:
						$this->putByte($rule['value']);
						break;
					case 2:
						$this->putVarInt($rule['value']);
						break;
					case 3:
						$this->putLFloat($rule['value']);
						break;
				}	
			}
			
			$this->putBool(false); // is bonus chest enabled
			$this->putBool(false); // is start with map enabled
			$this->putBool(false); // has trust players enabled
			$this->putVarInt(0); // permission level
			$this->putVarInt(4); // game publish setting
			$this->putLInt(0); // server chunk tick range
			$this->putBool(false); // can platform broadcast
			$this->putVarInt(0); // Broadcast mode
			$this->putBool(false); // XBL Broadcast intent
			// level settings end
			$this->putString('3138ee93-4a4a-479b-8dca-65ca5399e075'); // level id (random UUID)
			$this->putString(''); // level name
			$this->putString(''); // template pack id
			$this->putBool(false); // is trial?
	    	
	        $this->putLong(0); // current level time
	        $this->putVarInt(0); // enchantment seed
		} else {
	    	$this->putUnsignedVarInt(0); //TODO: gamerules
	    	$this->putString($this->levelId);
	    	$this->putString($this->worldName);
		    $this->putString($this->premiumWorldTemplateId);
		}
	}
}