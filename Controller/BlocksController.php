<?php

namespace NS\CmsBundle\Controller;

use Knp\Menu\Matcher\Matcher;
use Knp\Menu\MenuFactory;
use NS\CmsBundle\Block\Settings\MenuBlockSettingsModel;
use NS\CmsBundle\Entity\Page;
use NS\CmsBundle\Menu\Matcher\Voter\PageVoter;
use NS\CmsBundle\Menu\PageNode;
use NS\CmsBundle\Service\PageService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
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
	 * Menu block
	 *
	 * @param  Block $block
	 * @throws \Exception
	 * @return Response
	 */
	public function menuBlockAction(Block $block)
	{
		/** @var MenuBlockSettingsModel $settings */
		$settings = $this->getBlockManager()->getBlockSettings($block);

		// root page
		$rootPage = $this->getMenuRootPage($block, $settings);

		// creating from root node
		$factory = new MenuFactory();
		$rootNode = new PageNode($rootPage, $this->getRouter());
		$menu = $factory->createFromNode($rootNode);

		// pages matcher
		$matcher = new Matcher();
		/** @var $page Page */
		$page = $this->getRequest()->attributes->get('page');
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
	 * @return PageService
	 */
	private function getPageService()
	{
		return $this->get('ns_cms.service.page');
	}

	/**
	 * @return RouterInterface
	 */
	private function getRouter()
	{
		return $this->get('router');
	}

	/**
	 * @param Block                  $block
	 * @param MenuBlockSettingsModel $settings
	 * @return Page
	 * @throws \Exception
	 */
	private function getMenuRootPage(Block $block, MenuBlockSettingsModel $settings)
	{
		$pageService = $this->getPageService();

		// root page
		$rootPageId = $settings->getRootPageId();
		if ($rootPageId) {
			$rootPage = $pageService->getPageById($rootPageId);
			if (!$rootPage) {
				throw new \Exception("Incorrect block #{$block->getId()} settings: page #{$rootPageId} wasn't found");
			}
			return $rootPage;
		}

		return $pageService->getRootPageOrCreate();
	}
}
