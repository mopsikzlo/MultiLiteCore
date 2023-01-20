<?php

namespace pocketmine\network\protocol;

class AddPlayerPacket extends PEPacket{
    
	const NETWORK_ID = Info::ADD_PLAYER_PACKET;
	const PACKET_NAME = "ADD_PLAYER_PACKET";

	public $uuid;
	public $username;
	public $eid;
	public $x;
	public $y;
	public $z;
	public $speedX;
	public $speedY;
	public $speedZ;
	public $pitch;
	public $headYaw;
	public $yaw;
	public $item;
	public $metadata = [];
	
	public $flags = 0;
	public $commandPermission = 0;
	public $actionPermissions = 0;//AdventureSettingsPacket::ACTION_FLAG_DEFAULT_LEVEL_PERMISSIONS;
	public $permissionLevel = 0;//AdventureSettingsPacket::PERMISSION_LEVEL_MEMBER;
	public $storedCustomPermissions = 0;
	
	public $links = [];

	public function decode($playerProtocol){}

	public function encode($playerProtocol){
		$this->reset($playerProtocol);
		$this->putUUID($this->uuid);
		$this->putString($this->username);
		$this->putEntityId($this->eid); //EntityUniqueID
		$this->putEntityId($this->eid); //EntityRuntimeID
		$this->putVector3f($this->x, $this->y, $this->z);
		$this->putVector3f($this->speedX, $this->speedY, $this->speedZ);
		$this->putLFloat($this->pitch);
		$this->putLFloat($this->headYaw ?? $this->yaw);
		$this->putLFloat($this->yaw);
		$this->putSlot($this->item, $playerProtocol);
		$this->putEntityMetadata($this->metadata, $playerProtocol);
		
		if ($playerProtocol >= Info::PROTOCOL_120) {
			$this->putUnsignedVarInt($this->flags);
			$this->putUnsignedVarInt($this->commandPermission);
			$this->putUnsignedVarInt($this->actionPermissions);
			$this->putUnsignedVarInt($this->permissionLevel);
			$this->putUnsignedVarInt($this->storedCustomPermissions);
			$this->putLLong($this->eid);
			
			$this->putUnsignedVarInt(count($this->links));
			foreach ($this->links as $link) {
				$this->putUnsignedVarInt($link['from']);
				$this->putUnsignedVarInt($link['to']);
				$this->putByte($link['type']);
				$this->putByte(0); //immediate
			}
		}
	}
}