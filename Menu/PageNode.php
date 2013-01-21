<?php

namespace NS\CmsBundle\Menu;

use Knp\Menu\NodeInterface;
use NS\CmsBundle\Entity\Page;

use Symfony\Component\Routing\RouterInterface;

/**
 * Page node entity
 *
 */
class PageNode implements NodeInterface
{
	const PAGE_ID_ROUTE   = 'ns_cms_page';
	const PAGE_NAME_ROUTE = 'ns_cms_page_name';

	/**
	 * @var Page
	 */
	private $page;

	/**
	 * @var RouterInterface
	 */
	private $router;

	/**
	 * @param Page $page
	 * @param RouterInterface $router
	 */
	public function __construct(Page $page, RouterInterface $router)
	{
		$this->page = $page;
		$this->router = $router;
	}

	/**
	 * Get the name of the node
	 *
	 * Each child of a node must have a unique name
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->page->getId();
	}

	/**
	 * Get the options for the factory to create the item for this node
	 *
	 * @return array
	 */
	public function getOptions()
	{
		return array(
			'label'   => $this->page->getTitle(),
			'display' => $this->page->isVisible(),
			'uri'     => $this->getUrl(),
			'extras'  => array(
				'page' => $this->page,
			),
		);
	}

	/**
	 * Get the child nodes implementing NodeInterface
	 *
	 * @return \Traversable
	 */
	public function getChildren()
	{
		$children = array();
		foreach ($this->page->getChildren() as $page) {
			$children[] = new PageNode($page, $this->router);
		}
		return $children;
	}

	/**
	 * Generates page URL
	 *
	 * @return string
	 */
	private function getUrl()
	{
		// named route
		if ($this->page->getName()) {
			return $this->router->generate(self::PAGE_NAME_ROUTE, array(
				'name' => $this->page->getName(),
			));
		}

		// numeric route
		return $this->router->generate(self::PAGE_ID_ROUTE, array(
			'id' => $this->page->getId(),
		));
	}
}
