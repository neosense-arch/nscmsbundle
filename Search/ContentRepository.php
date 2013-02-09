<?php

namespace NS\CmsBundle\Search;

use NS\CmsBundle\Entity\BlockRepository;
use NS\SearchBundle\Agent\RepositoryInterface;
use NS\SearchBundle\Models\ModelCollection;

class ContentRepository implements RepositoryInterface
{
	const CONTENT_BLOCK_TYPE_NAME = 'NSCmsBundle:Blocks:contentBlock';

	/**
	 * @var
	 */
	private $blockRepository;

	/**
	 * @param BlockRepository $blockRepository
	 */
	public function __construct(BlockRepository $blockRepository)
	{
		$this->setBlockRepository($blockRepository);
	}

	/**
	 * Retrieves all models
	 *
	 * @return ModelCollection
	 */
	public function findAllModels()
	{
		$blocks = $this->getBlockRepository()
			->findBlocksByTypeName(self::CONTENT_BLOCK_TYPE_NAME);

		return new ModelCollection($blocks);
	}

	/**
	 * Retrieves models by ID array
	 *
	 * @param  int[] $ids
	 * @return ModelCollection
	 */
	public function findModelsByIds(array $ids)
	{
		$blocks = $this->getBlockRepository()
			->findBlocksByTypeNameAndIds(self::CONTENT_BLOCK_TYPE_NAME, $ids);

		return new ModelCollection($blocks);
	}

	/**
	 * @param BlockRepository $blockRepository
	 */
	private function setBlockRepository(BlockRepository $blockRepository)
	{
		$this->blockRepository = $blockRepository;
	}

	/**
	 * @return BlockRepository
	 */
	private function getBlockRepository()
	{
		return $this->blockRepository;
	}
}
