<?php

namespace NS\CmsBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AfterBlockProcessEvent
 *
 * @package NS\CmsBundle\Event
 */
class AfterBlockProcessEvent extends Event
{
	/**
	 * @var Response
	 */
	private $response;

	/**
	 * @var bool
	 */
	private $fromCache;

	/**
	 * @param Response $response
	 * @param bool     $fromCache
	 */
	public function __construct(Response $response, $fromCache = false)
	{
		$this->response = $response;
		$this->fromCache = $fromCache;
	}
	/**
	 * @return boolean
	 */
	public function getFromCache()
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