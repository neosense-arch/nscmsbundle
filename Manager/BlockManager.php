<?php

namespace NS\CmsBundle\Manager;

use NS\CmsBundle\Entity\Block;
use NS\CmsBundle\Entity\BlockRepository;
use NS\CmsBundle\Entity\Page;
use NS\CmsBundle\Entity\BlockType;
use NS\CmsBundle\Entity\BlockTypeRepository;

/**
 * CMS blocks manager
 *
 */
class BlockManager
{
	/**
	 * @var BlockRepository
	 */
	private $blockRepository;

	/**
	 * @var BlockTypeRepository
	 */
	private $blockTypeRepository;

	/**
	 * @param BlockRepository $blockRepository
	 */
	public function setBlockRepository(BlockRepository $blockRepository)
	{
		$this->blockRepository = $blockRepository;
	}

	/**
	 * @param BlockTypeRepository $blockTypeRepository
	 */
	public function setBlockTypeRepository(BlockTypeRepository $blockTypeRepository)
	{
		$this->blockTypeRepository = $blockTypeRepository;
	}

	/**
	 * Retrieves page blocks
	 *
	 * @param Page $page
	 * @return Block[]
	 */
	public function getPageBlocks(Page $page)
	{
		return $this->getBlockRepository()->findPageBlocks($page);
	}

	/**
	 * Retrieves block by id
	 *
	 * @param int $id
	 * @return Block
	 * @throws \Exception
	 */
	public function getBlock($id)
	{
		$block = $this->getBlockRepository()->findBlockById($id);
		if (!$block) {
			throw new \Exception(sprintf("Block #%s wasn't found", $id));
		}
		return $block;
	}

	/**
	 * Retrieves block type by name
	 *
	 * @param string $name
	 * @throws \Exception
	 * @return BlockType|null
	 */
	public function getBlockType($name)
	{
		$blockType = $this->getBlockTypeRepository()->findBlockTypeByName($name);
		if (!$blockType) {
			throw new \Exception(sprintf("Block type '%s' wasn't found", $name));
		}
		return $blockType;
	}

	/**
	 * Retrieves shared blocks
	 *
	 * @return Block[]
	 */
	public function getSharedBlocks()
	{
		return $this->getBlockRepository()->findSharedBlocks();
	}

	/**
	 * @return BlockRepository
	 */
	private function getBlockRepository()
	{
		return $this->blockRepository;
	}

	/**
	 * @return BlockTypeRepository
	 */
	private function getBlockTypeRepository()
	{
		return $this->blockTypeRepository;
	}
}
