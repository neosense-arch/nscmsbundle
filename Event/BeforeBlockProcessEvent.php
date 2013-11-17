<?php

namespace NS\CmsBundle\Event;

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
	 * @param Request $request
	 */
	public function __construct(Request $request)
	{
		$this->request  = $request;
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
}