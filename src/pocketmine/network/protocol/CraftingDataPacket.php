<?php

namespace pocketmine\network\protocol;

use pocketmine\inventory\FurnaceRecipe;
use pocketmine\inventory\ShapedRecipe;
use pocketmine\inventory\ShapelessRecipe;
use pocketmine\item\Item;
use pocketmine\utils\BinaryStream;

class CraftingDataPacket extends PEPacket{
	const NETWORK_ID = Info::CRAFTING_DATA_PACKET;
	const PACKET_NAME = "CRAFTING_DATA_PACKET";

	const ENTRY_SHAPELESS = 0;
	const ENTRY_SHAPED = 1;
	const ENTRY_FURNACE = 2;
	const ENTRY_FURNACE_DATA = 3;
	const ENTRY_MULTI = 4;

	/** @var object[] */
	public $entries = [];
	public $cleanRecipes = false;

	/**
	 * @return $this
	 */
	public function clean(){
		$this->entries = [];

		return parent::clean();
	}

	public function decode($playerProtocol){}

	/**
	 * @param              $entry
	 * @param BinaryStream $stream
	 *
	 * @return int
	 */
	private static function writeEntry($entry, BinaryStream $stream, int $playerProtocol){
		if($entry instanceof ShapelessRecipe){
			return self::writeShapelessRecipe($entry, $stream, $playerProtocol);
		}elseif($entry instanceof ShapedRecipe){
			return self::writeShapedRecipe($entry, $stream, $playerProtocol);
		}elseif($entry instanceof FurnaceRecipe){
			return self::writeFurnaceRecipe($entry, $stream, $playerProtocol);
		}

		//TODO: add MultiRecipe

		return -1;
	}

	/**
	 * @param ShapelessRecipe $recipe
	 * @param BinaryStream    $stream
	 *
	 * @return int
	 */
	private static function writeShapelessRecipe(ShapelessRecipe $recipe, BinaryStream $stream, int $playerProtocol){
		$stream->putUnsignedVarInt($recipe->getIngredientCount());
		foreach($recipe->getIngredientList() as $item){
			$stream->putSlot($item, $playerProtocol);
		}

		$stream->putUnsignedVarInt(1);
		$stream->putSlot($recipe->getResult(), $playerProtocol);

		$stream->putUUID($recipe->getId());

		return CraftingDataPacket::ENTRY_SHAPELESS;
	}

	/**
	 * @param ShapedRecipe $recipe
	 * @param BinaryStream $stream
	 *
	 * @return int
	 */
	private static function writeShapedRecipe(ShapedRecipe $recipe, BinaryStream $stream, int $playerProtocol){
		$stream->putVarInt($recipe->getWidth());
		$stream->putVarInt($recipe->getHeight());

		for($z = 0; $z < $recipe->getHeight(); ++$z){
			for($x = 0; $x < $recipe->getWidth(); ++$x){
				$stream->putSlot($recipe->getIngredient($x, $z), $playerProtocol);
			}
		}

		$stream->putUnsignedVarInt(1);
		$stream->putSlot($recipe->getResult(), $playerProtocol);

		$stream->putUUID($recipe->getId());

		return CraftingDataPacket::ENTRY_SHAPED;
	}

	/**
	 * @param FurnaceRecipe $recipe
	 * @param BinaryStream  $stream
	 *
	 * @return int
	 */
	private static function writeFurnaceRecipe(FurnaceRecipe $recipe, BinaryStream $stream, int $playerProtocol){
		if(!$recipe->getInput()->hasAnyDamageValue()){ //Data recipe
			$stream->putVarInt($recipe->getInput()->getId());
			$stream->putVarInt($recipe->getInput()->getDamage());
			$stream->putSlot($recipe->getResult(), $playerProtocol);

			return CraftingDataPacket::ENTRY_FURNACE_DATA;
		}else{
			$stream->putVarInt($recipe->getInput()->getId());
			$stream->putSlot($recipe->getResult(), $playerProtocol);

			return CraftingDataPacket::ENTRY_FURNACE;
		}
	}

	/**
	 * @param ShapelessRecipe $recipe
	 */
	public function addShapelessRecipe(ShapelessRecipe $recipe){
		$this->entries[] = $recipe;
	}

	/**
	 * @param ShapedRecipe $recipe
	 */
	public function addShapedRecipe(ShapedRecipe $recipe){
		$this->entries[] = $recipe;
	}

	/**
	 * @param FurnaceRecipe $recipe
	 */
	public function addFurnaceRecipe(FurnaceRecipe $recipe){
		$this->entries[] = $recipe;
	}

	public function encode($playerProtocol){
		$this->reset($playerProtocol);
		$this->putUnsignedVarInt(count($this->entries));

		$writer = new BinaryStream();
		foreach($this->entries as $d){
			$entryType = self::writeEntry($d, $writer, $playerProtocol);
			if($entryType >= 0){
				$this->putVarInt($entryType);
				$this->put($writer->getBuffer());
			}else{
				$this->putVarInt(-1);
			}

			$writer->reset();
		}

		$this->putBool($this->cleanRecipes);
	}
}