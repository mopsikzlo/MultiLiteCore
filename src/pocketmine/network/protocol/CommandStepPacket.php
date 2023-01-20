<?php

namespace pocketmine\network\protocol;

class CommandStepPacket extends PEPacket {
    
	const NETWORK_ID = Info::COMMAND_STEP_PACKET;
	const PACKET_NAME = "COMMAND_STEP_PACKET";

	public $command;
	public $overload;
	public $uvarint1;
	public $currentStep;
	public $done;
	public $clientId;
	public $inputJson;
	public $outputJson;

	public function decode($playerProtocol){
	    $this->getHeader($playerProtocol);
		$this->command = $this->getString();
		$this->overload = $this->getString();
		$this->uvarint1 = $this->getUnsignedVarInt();
		$this->currentStep = $this->getUnsignedVarInt();
		$this->done = (bool) $this->getByte();
		$this->clientId = $this->getUnsignedVarInt(); //TODO: varint64
		$this->inputJson = json_decode($this->getString());
		$this->outputJson = $this->getString();
	}

	public function encode($playerProtocol){}
}