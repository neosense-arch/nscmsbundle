<?php

namespace NS\CmsBundle\Controller;

use Knp\Menu\Matcher\Matcher;
use Knp\Menu\MenuFactory;
use Knp\Menu\MenuItem;
use NS\CmsBundle\Block\Settings\MapBlockSettingsModel;
use NS\CmsBundle\Block\Settings\MenuBlockSettingsModel;
use NS\CmsBundle\Entity\Page;
use NS\CmsBundle\Menu\Matcher\Voter\PageVoter;
use NS\CmsBundle\Menu\PageNode;
use NS\CmsBundle\Service\PageService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use NS\CmsBundle\Entity\Block;
use NS\CmsBundle\Manager\BlockManager;
use NS\CmsBundle\Block\Settings\ContentBlockSettingsModel;

/**
 * Blocks controller
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
		$settings = $this->get('ns_cms.manager.block')->getBlockSettings($block);

		return $this->render($block->getTemplate(), array(
			'block'    => $block,
			'settings' => $settings,
			'content'  => $settings->getContent(),
		));
	}

    /**
     * Menu block
     *
     * @param Request                $request
     * @param  Block                 $block
     * @param MenuBlockSettingsModel $settings
     * @return Response
     */
	public function menuBlockAction(Request $request, Block $block, MenuBlockSettingsModel $settings)
	{
		$rootPage = $this->getMenuRootPage($block, $settings);

		// creating from root node
		$factory = new MenuFactory();
		$rootNode = new PageNode($rootPage, $this->get('router'));
		$menu = $factory->createFromNode($rootNode);

        // pages matcher
        $matcher = new Matcher();
        /** @var $page Page */
        $page = $request->attributes->get('page');
        $matcher->addVoter(new PageVoter($page));

        // active root
        $currentItem = null;
        foreach ($menu->getChildren() as $item) {
            if ($matcher->isCurrent($item) || $matcher->isAncestor($item)) {
                $currentItem = $item;
                break;
            }
        }

        // submenu
        if ($settings->getIsSubmenu() && $currentItem) {
            $breadcrumbs = array_slice($currentItem->getBreadcrumbsArray(), $settings->getDepth());
            $menu = $breadcrumbs[0]['item'];
        }

        // skipping items (only root nodes)
        foreach ($settings->getSkipArray() as $skip) {
            /** @var MenuItem $node */
            foreach ($menu->getChildren() as $node) {
                /** @var Page $page */
                $page = $node->getExtra('page');
                if ($page->getName() === $skip) {
                    $node->getParent()->removeChild($node);
                }
            }
        }

		// rendering
		return $this->render($block->getTemplate(), array(
            'page'        => $page,
            'block'       => $block,
            'settings'    => $settings,
            'menu'        => $menu,
            'matcher'     => $matcher,
            'currentItem' => $currentItem,
		));
	}

    /**
     * Map block
     *
     * @param  Block $block
     * @throws \Exception
     * @return Response
     */
    public function mapBlockAction(Block $block)
    {
        /** @var MapBlockSettingsModel $settings */
        $settings = $this->get('ns_cms.manager.block')->getBlockSettings($block);

        // rendering
        return $this->render($block->getTemplate(), array(
            'block'    => $block,
            'settings' => $settings,
            'uid'      => md5(uniqid('', true)),
        ));
    }

	/**
	 * @param Block                  $block
	 * @param MenuBlockSettingsModel $settings
	 * @return Page
	 * @throws \Exception
	 */
	private function getMenuRootPage(Block $block, MenuBlockSettingsModel $settings)
	{
        /** @var PageService $pageService */
		$pageService = $this->get('ns_cms.service.page');

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
