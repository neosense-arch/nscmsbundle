<?php

namespace NS\CmsBundle\Event;

use NS\CmsBundle\Entity\Page;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
	 * @var Request
	 */
	private $request;

	/**
	 * @var Response
	 */
	private $response;

	/**
	 * @param Page     $page
	 * @param Request  $request
	 * @param Response $response
	 */
	public function __construct(Page $page, Request $request, Response $response)
	{
		$this->page     = $page;
		$this->request  = $request;
		$this->response = $response;
	}

	/**
	 * @return Page
	 */
	public function getPage()
	{
		return $this->page;
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
}