<?php

namespace NS\CmsBundle\Search;

use NS\CmsBundle\Block\Settings\ContentBlockSettingsModel;
use NS\CmsBundle\Manager\BlockManager;
use NS\SearchBundle\Agent\MapperInterface;
use NS\SearchBundle\Models\Document;
use NS\CmsBundle\Entity\Block;

class ContentMapper implements MapperInterface
{
	/**
	 * @var BlockManager
	 */
	private $blockManager;

	/**
	 * @param BlockManager $blockManager
	 */
	public function __construct(BlockManager $blockManager)
	{
		$this->setBlockManager($blockManager);
	}

	/**
	 * Retrieves document by model
	 *
	 * @param  Block $model
	 * @return Document
	 */
	public function getDocumentByModel($model)
	{
		/** @var $settings ContentBlockSettingsModel */
		$settings = $this->getBlockManager()->getBlockSettings($model);
		$settings->getContent();

		return new Document(
			$model->getId(),
			'NSCmsBundle:Block',
			$model->getTitle(),
			$settings->getContent()
		);
	}

	/**
	 * @param BlockManager $blockManager
	 */
	private function setBlockManager(BlockManager $blockManager)
	{
		$this->blockManager = $blockManager;
	}

	/**
	 * @return BlockManager
	 */
	private function getBlockManager()
	{
		return $this->blockManager;
	}
}
