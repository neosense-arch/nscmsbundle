<?php

namespace NS\CmsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use NS\CmsBundle\Entity\Block;
use NS\CmsBundle\Manager\BlockManager;
use NS\CmsBundle\Block\Settings\ContentBlockSettingsModel;

/**
 * Pages controller
 *
 */
class BlocksController extends Controller
{
	/**
	 * Content block
	 *
	 * @param  Block $block
	 * @return Response
	 */
	public function contentBlockAction(Block $block)
	{
		/** @var $settings ContentBlockSettingsModel */
		$settings = $this->getBlockManager()->getBlockSettings($block);

		return $this->render('NSCmsBundle:Blocks:contentBlock.html.twig', array(
			'block'    => $block,
			'settings' => $settings,
			'content'  => $settings->getContent(),
		));
	}

	/**
	 * @return BlockManager
	 */
	private function getBlockManager()
	{
		return $this->container->get('ns_cms.manager.block');
	}
}
