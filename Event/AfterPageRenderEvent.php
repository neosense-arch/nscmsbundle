<?php

namespace NS\CmsBundle\Event;

use NS\CmsBundle\Entity\Page;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class AfterPageRenderEvent
 *
 * @package NS\CmsBundle\Event
 */
class AfterPageRenderEvent extends Event
{
	/**
	 * @var Page
	 */
	private $page;

	/**
	 * @var string
	 */
	private $html;

	/**
	 * @param Page   $page
	 * @param string $html
	 */
	public function __construct(Page $page, $html)
	{
		$this->page = $page;
	}

	/**
	 * @return string
	 */
	public function getHtml()
	{
		return $this->html;
	}

	/**
	 * @return Page
	 */
	public function getPage()
	{
		return $this->page;
	}
}