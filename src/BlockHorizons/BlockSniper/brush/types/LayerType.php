<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use pocketmine\block\Block;
use pocketmine\level\ChunkManager;
use pocketmine\math\Vector3;
use pocketmine\Player;

class LayerType extends BaseType {

	/** @var int */
	protected $id = self::TYPE_LAYER;

	/*
	 * Lays a thin layer of blocks within the brush radius.
	 */
	public function __construct(Player $player, ChunkManager $level, array $blocks) {
		parent::__construct($player, $level, $blocks);
		$this->center = $player->getTargetBlock(100)->asVector3();
	}

	/**
	 * @return Block[]|null
	 */
	public function fillShape(): ?array {
		if($this->isAsynchronous()) {
			$this->fillAsynchronously();
			return null;
		}
		$undoBlocks = [];
		foreach($this->blocks as $block) {
			$randomBlock = $this->brushBlocks[array_rand($this->brushBlocks)];
			if($block->getId() !== $randomBlock->getId()) {
				$undoBlocks[] = $block;
			}
			$this->getLevel()->setBlock(new Vector3($block->x, $this->center->y + 1, $block->z), $randomBlock, false, false);
		}
		return $undoBlocks;
	}

	public function fillAsynchronously(): void {
		foreach($this->blocks as $block) {
			$randomBlock = $this->brushBlocks[array_rand($this->brushBlocks)];
			$this->getChunkManager()->setBlockIdAt($block->x, $this->center->y + 1, $block->z, $randomBlock->getId());
			$this->getChunkManager()->setBlockDataAt($block->x, $this->center->y + 1, $block->z, $randomBlock->getDamage());
		}
	}

	public function getName(): string {
		return "Layer";
	}

	/**
	 * Returns the center of this type.
	 *
	 * @return Vector3
	 */
	public function getCenter(): Vector3 {
		return $this->center;
	}
}

