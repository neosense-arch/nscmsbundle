<?php

namespace NS\CmsBundle\Search;

use NS\CmsBundle\Entity\Block;
use NS\CmsBundle\Block\Settings\ContentBlockSettingsModel;
use NS\CmsBundle\Manager\BlockManager;
use NS\SearchBundle\Agent\MapperInterface;
use NS\SearchBundle\Models\Document;
use NS\SearchBundle\Models\DocumentView;

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
	 * @param  Block $block
	 * @return Document
	 */
	public function getDocumentByModel($block)
	{
		/** @var $settings ContentBlockSettingsModel */
		$settings = $this->getBlockManager()->getBlockSettings($block);

		return new Document(
			$block->getId(),
			'ns_cms:content',
			$block->getTitle(),
			$settings->getContent()
		);
	}

	/**
	 * Retrieves document view by model
	 *
	 * @param  Block $block
	 * @return DocumentView
	 */
	public function getDocumentViewByModel($block)
	{
		/** @var $settings ContentBlockSettingsModel */
		$settings = $this->getBlockManager()->getBlockSettings($block);

		$description = $settings->getContent();
		$description = str_replace(array("\n", "\r"), array(' ', ''), $description);
		$description = strip_tags($description);
		$description = preg_replace('/\s+/u', ' ',$description);
		$description = html_entity_decode($description);
		$description = mb_substr($description, 0, 300, 'utf-8');

		return new DocumentView(
			$block->getPage()->getTitle(),
			$description,
			$block
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
