<?php

namespace pocketmine\network\protocol;

class MovePlayerPacket extends PEPacket{
	const NETWORK_ID = Info::MOVE_PLAYER_PACKET;
	const PACKET_NAME = "MOVE_PLAYER_PACKET";

	const MODE_NORMAL = 0;
	const MODE_RESET = 1;
    const MODE_TELEPORT = 2;
    const MODE_PITCH = 3; //facepalm Mojang

	public $eid;
	public $x;
	public $y;
	public $z;
	public $yaw;
	public $bodyYaw;
	public $pitch;
	public $mode = self::MODE_NORMAL;
	public $onGround = false; //TODO
	public $eid2 = 0;
    public $teleportCause = 0;
    public $teleportItem = 0;

	public function decode($playerProtocol){
	    $this->getHeader($playerProtocol);
		$this->eid = $this->getEntityId(); //EntityRuntimeID
		$this->getVector3f($this->x, $this->y, $this->z);
		$this->pitch = $this->getLFloat();
		$this->yaw = $this->getLFloat();
		$this->bodyYaw = $this->getLFloat();
		$this->mode = $this->getByte();
		$this->onGround = $this->getBool();
		$this->eid2 = $this->getEntityId();
        if($this->mode === MovePlayerPacket::MODE_TELEPORT){
            $this->teleportCause = $this->getLInt();
            $this->teleportItem = $this->getLInt();
        }
	}

	public function encode($playerProtocol){
		$this->reset($playerProtocol);
		$this->putEntityId($this->eid); //EntityRuntimeID
		$this->putVector3f($this->x, $this->y, $this->z);
		$this->putLFloat($this->pitch);
		$this->putLFloat($this->yaw);
		$this->putLFloat($this->bodyYaw); //TODO
		$this->putByte($this->mode);
		$this->putBool($this->onGround);
		$this->putEntityId($this->eid2); //EntityRuntimeID
        if($this->mode === MovePlayerPacket::MODE_TELEPORT){
            $this->putLInt($this->teleportCause);
            $this->putLInt($this->teleportItem);
        }
	}

}
