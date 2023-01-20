<?php

namespace pocketmine\network\protocol\v120;

use pocketmine\inventory\transactions\SimpleTransactionData;
use pocketmine\network\protocol\Info120;
use pocketmine\network\protocol\PEPacket;
use pocketmine\network\protocol\Info;
use pocketmine\math\Vector3;

class InventoryTransactionPacket extends PEPacket {

	const NETWORK_ID = Info120::INVENTORY_TRANSACTION_PACKET;
	const PACKET_NAME = "INVENTORY_TRANSACTION_PACKET";
	
	const TRANSACTION_TYPE_NORMAL = 0;
	const TRANSACTION_TYPE_INVENTORY_MISMATCH = 1;
	const TRANSACTION_TYPE_ITEM_USE = 2;
	const TRANSACTION_TYPE_ITEM_USE_ON_ENTITY = 3;
	const TRANSACTION_TYPE_ITEM_RELEASE = 4;	
	
	const INV_SOURCE_TYPE_CONTAINER = 0;
	const INV_SOURCE_TYPE_GLOBAL = 1;
	const INV_SOURCE_TYPE_WORLD_INTERACTION = 2;
	const INV_SOURCE_TYPE_CREATIVE = 3;
	const INV_SOURCE_TYPE_CRAFT_SLOT = 100;
	const INV_SOURCE_TYPE_CRAFT = 99999;	
	
	const ITEM_RELEASE_ACTION_RELEASE = 0;
	const ITEM_RELEASE_ACTION_USE = 1;
	
	const ITEM_USE_ACTION_PLACE = 0;
	const ITEM_USE_ACTION_USE = 1;
	const ITEM_USE_ACTION_DESTROY = 2;
	
	const ITEM_USE_ON_ENTITY_ACTION_INTERACT = 0;
	const ITEM_USE_ON_ENTITY_ACTION_ATTACK = 1;
	const ITEM_USE_ON_ENTITY_ACTION_ITEM_INTERACT = 2;

	public $transactionType;
	/** @var SimpleTransactionData */
	public $transactions;
	public $actionType;
	public $position;
	public $face;
	public $slot;
	public $item;
	public $fromPosition;
	public $clickPosition;
	public $entityId;

	public function decode($playerProtocol) {	
		$this->getHeader($playerProtocol);
		$this->transactionType = $this->getUnsignedVarInt();
		$this->transactions = $this->getTransactions($playerProtocol);
		$this->getComplexTransactions($playerProtocol);
	}

	public function encode($playerProtocol) {}
	
	private function getTransactions($playerProtocol) {
		$transactions = [];
		$actionsCount = $this->getUnsignedVarInt();
		for ($i = 0; $i < $actionsCount; $i++) {
			$tr = new SimpleTransactionData();
			$tr->sourceType = $this->getUnsignedVarInt();
			switch ($tr->sourceType) {
				case self::INV_SOURCE_TYPE_CONTAINER;
					$tr->inventoryId = $this->getVarInt();
					break;
				case self::INV_SOURCE_TYPE_GLOBAL: // ???
					break;
				case self::INV_SOURCE_TYPE_WORLD_INTERACTION:
					$tr->flags = $this->getUnsignedVarInt(); // flags NoFlag = 0 WorldInteraction_Random = 1
					break;
				case self::INV_SOURCE_TYPE_CREATIVE:
					$tr->inventoryId = Protocol120::CONTAINER_ID_CREATIVE;
					break;
				case self::INV_SOURCE_TYPE_CRAFT:
				case self::INV_SOURCE_TYPE_CRAFT_SLOT:
					$tr->action = $this->getUnsignedVarInt();
					break;
			}
			$tr->slot = $this->getUnsignedVarInt();
			$tr->oldItem = $this->getSlot($playerProtocol);
			$tr->newItem = $this->getSlot($playerProtocol);	
			$transactions[] = $tr;
		}
		return $transactions;
	}



	private function getComplexTransactions($playerProtocol) {
		switch ($this->transactionType) {
			case self::TRANSACTION_TYPE_NORMAL:
			case self::TRANSACTION_TYPE_INVENTORY_MISMATCH:
				return;
			case self::TRANSACTION_TYPE_ITEM_USE:
				$this->actionType = $this->getUnsignedVarInt();
				$this->position = new Vector3($this->getVarInt(), $this->getUnsignedVarInt(), $this->getVarInt());
				$this->face = $this->getVarInt();
				$this->slot = $this->getVarInt();
				$this->item = $this->getSlot($playerProtocol);
				$this->fromPosition = new Vector3($this->getLFloat(), $this->getLFloat(), $this->getLFloat());
				$this->clickPosition = new Vector3($this->getLFloat(), $this->getLFloat(), $this->getLFloat());
				return;
			case self::TRANSACTION_TYPE_ITEM_USE_ON_ENTITY:
				$this->entityId = $this->getUnsignedVarInt();
				$this->actionType = $this->getUnsignedVarInt();
				$this->slot = $this->getVarInt();
				$this->item = $this->getSlot($playerProtocol);
				$this->fromPosition = new Vector3($this->getLFloat(), $this->getLFloat(), $this->getLFloat());
				return;
			case self::TRANSACTION_TYPE_ITEM_RELEASE:
				$this->actionType = $this->getUnsignedVarInt();
				$this->slot = $this->getVarInt();
				$this->item = $this->getSlot($playerProtocol);
				$this->fromPosition = new Vector3($this->getLFloat(), $this->getLFloat(), $this->getLFloat());
				return;
		}
	}
}