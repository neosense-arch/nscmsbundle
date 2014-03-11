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
	 * Retrieves page blocks
	 *
	 * @param Page $page
	 * @return Block[]
	 */
	public function getPageBlocks(Page $page)
	{
        $blocks = $this->blockRepository->findPageBlocks($page);
        $this->mapBlockTypes($blocks);
		return $blocks;
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
		$block = $this->blockRepository->findBlockById($id);
		if (!$block) {
			throw new \Exception(sprintf("Block #%s wasn't found", $id));
		}
        $this->mapBlockTypes(array($block));
		return $block;
	}

	/**
	 * Retrieves block type by name
	 *
	 * @param string $name
	 * @return BlockType
	 * @throws \Exception
	 */
	public function getBlockType($name)
	{
		$blockType = $this->blockTypeRepository->findBlockTypeByName($name);
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
		return $this->blockRepository->findSharedBlocks();
	}

	/**
	 * Retrieves block settings
	 *
	 * @param  Block $block
	 * @return mixed|null
	 * @throws \Exception
	 */
	public function getBlockSettings(Block $block)
	{
		// retrieving block settings model
		$blockType = $this->getBlockType($block->getTypeName());
		$settingsModelClass = $blockType->getSettingsModelClass();

		// checking if settings exists
		if (!$settingsModelClass) {
			return null;
		}

		// checking if class exists
		if (!class_exists($settingsModelClass)) {
			throw new \Exception("Settings model class '{$settingsModelClass}' wasn't found");
		}

		// empty settings
		if (!$block->getSettings()) {
			return new $settingsModelClass;
		}

		// stored settings
		$settings = unserialize($block->getSettings());

		// checking class hierarchy
		if (!is_a($settings, $settingsModelClass)) {
			$class = get_class($settings);
			throw new \Exception("Settings object (block #{$block->getId()}) of class '{$class}' is not subclass of '{$settingsModelClass}'");
		}

		return $settings;
	}

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
     * @param Block[] $blocks
     */
    private function mapBlockTypes(array $blocks)
    {
        $blockTypes = $this->blockTypeRepository->findAll();
        foreach ($blocks as $block) {
            foreach ($blockTypes as $blockType) {
                if ($blockType->getName() === $block->getTypeName()) {
                    $block->setType($blockType);
                }
            }
        }
    }
}
