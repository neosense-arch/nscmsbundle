<?php

namespace NS\CmsBundle\Entity;

use NS\AdminBundle\Service\AdminService;
use Symfony\Component\Yaml\Yaml;

/**
 * Blocks repository
 *
 */
class BlockTypeRepository
{
	/**
	 * Templates filename
	 * @var string
	 */
	const TEMPLATES_CONFIG_FILENAME = 'Resources/config/ns_cms.blocks.yml';

	/**
	 * @var AdminService
	 */
	private $adminService;

	/**
	 * @param AdminService $adminService
	 */
	public function __construct(AdminService $adminService)
	{
		$this->adminService = $adminService;
	}

	/**
	 * Retrieves all blocks
	 *
	 * @throws \Exception
	 * @return BlockType[]
	 */
	public function findAll()
	{
		$blocks = array();

		foreach ($this->adminService->getActiveBundles() as $bundle) {
			$fileName = $bundle->getPath() . '/' . self::TEMPLATES_CONFIG_FILENAME;
			if (file_exists($fileName)) {
				$blocks = array_merge($blocks, $this->createBlockTypesFromYml($fileName));
			}
		}

		return array_values($blocks);
	}

	/**
	 * Retrieves block by name
	 *
	 * @param  string $name
	 * @return BlockType|null
	 */
	public function findBlockTypeByName($name)
	{
		foreach ($this->findAll() as $blockType) {
			if ($blockType->getName() === $name) {
				return $blockType;
			}
		}
		return null;
	}

	/**
	 * Creates blocks from YAML file
	 *
	 * @param  string $fileName
	 * @return BlockType[]
	 * @throws \Exception
	 */
	private function createBlockTypesFromYml($fileName)
	{
		$blocks = array();

		$yml = file_get_contents($fileName);
		foreach (Yaml::parse($yml) as $data) {
			if (!is_array($data)) {
				throw new \Exception("Config section 'blocks' must be array in '{$fileName}'");
			}

			// adding blocks
			foreach ($data as $row) {
				$block = BlockType::createFromArray($row);
				$blocks[$block->getName()] = $block;
			}
		}

		return $blocks;
	}
}
