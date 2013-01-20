<?php

namespace NS\CmsBundle\Controller;

use Knp\Menu\Matcher\Matcher;
use Knp\Menu\MenuFactory;
use NS\CmsBundle\Entity\Page;
use NS\CmsBundle\Menu\Matcher\Voter\PageVoter;
use NS\CmsBundle\Menu\PageNode;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

use NS\CmsBundle\Entity\Block;
use NS\CmsBundle\Manager\BlockManager;
use NS\CmsBundle\Block\Settings\ContentBlockSettingsModel;
use NS\CmsBundle\Entity\PageRepository;

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
	 * Menu block
	 *
	 * @param  Block $block
	 * @return Response
	 */
	public function menuBlockAction(Block $block)
	{
		$settings = $this->getBlockManager()->getBlockSettings($block);

		/** @var $page Page */
		$page = $this->getRequest()->attributes->get('page');

		/** @var $router RouterInterface */
		$router = $this->get('router');

		// creating from root node
		$factory = new MenuFactory();
		$rootNode = new PageNode($this->getPageRepository()->findRootPageOrCreate(), $router);
		$menu = $factory->createFromNode($rootNode);

		// pages matcher
		$matcher = new Matcher();
		$matcher->addVoter(new PageVoter($page));

		// rendering
		return $this->render('NSCmsBundle:Blocks:menuBlock.html.twig', array(
			'block'    => $block,
			'settings' => $settings,
			'menu'     => $menu,
			'matcher'  => $matcher,
		));
	}

	/**
	 * @return BlockManager
	 */
	private function getBlockManager()
	{
		return $this->container->get('ns_cms.manager.block');
	}

	/**
	 * @return PageRepository
	 */
	private function getPageRepository()
	{
		return $this->getDoctrine()->getManager()->getRepository('NSCmsBundle:Page');
	}
}
