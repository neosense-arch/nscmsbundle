<?php

namespace NS\CmsBundle\Event;

use NS\CmsBundle\Entity\Block;
use NS\CmsBundle\Entity\Page;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class BeforeBlockProcessEvent
 *
 * @package NS\CmsBundle\Event
 */
class BeforeBlockProcessEvent extends Event
{
	/**
	 * @var Request
	 */
	private $request;

	/**
	 * @var Response
	 */
	private $response;

	/**
	 * @var Block
	 */
	private $block;

	/**
	 * @var Page
	 */
	private $page;

	/**
	 * @param Request $request
	 * @param Block   $block
	 * @param Page    $page
	 */
	public function __construct(Request $request, Block $block, Page $page)
	{
		$this->request = $request;
		$this->block   = $block;
		$this->page    = $page;
	}

	/**
	 * @return bool
	 */
	public function hasResponse()
	{
		return (bool)$this->response;
	}

	/**
	 * @return Request
	 */
	public function getRequest()
	{
		return $this->request;
	}

	/**
	 * @return Response
	 */
	public function getResponse()
	{
		return $this->response;
	}

	/**
	 * @param Response $response
	 */
	public function setResponse(Response $response)
	{
		$this->response = $response;
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
}