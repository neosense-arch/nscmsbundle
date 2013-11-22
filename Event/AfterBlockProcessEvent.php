<?php

namespace NS\CmsBundle\Event;

use NS\CmsBundle\Entity\Block;
use NS\CmsBundle\Entity\Page;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AfterBlockProcessEvent
 *
 * @package NS\CmsBundle\Event
 */
class AfterBlockProcessEvent extends Event
{
	/**
	 * @var Block
	 */
	private $block;

	/**
	 * @var Page
	 */
	private $page;

	/**
	 * @var Response
	 */
	private $response;

	/**
	 * @var bool
	 */
	private $fromCache;

	/**
	 * @param Block    $block
	 * @param Page     $page
	 * @param Response $response
	 * @param bool     $fromCache
	 */
	public function __construct(Block $block, Page $page, Response $response, $fromCache = false)
	{
		$this->block     = $block;
		$this->page      = $page;
		$this->response  = $response;
		$this->fromCache = $fromCache;
	}

	/**
	 * @return Block
	 */
	public function getBlock()
	{
		return $this->block;
	}

	/**
	 * @return Page
	 */
	public function getPage()
	{
		return $this->page;
	}

	/**
	 * @return boolean
	 */
	public function isFromCache()
	{
		return $this->fromCache;
	}

	/**
	 * @return Response
	 */
	public function getResponse()
	{
		return $this->response;
	}
}