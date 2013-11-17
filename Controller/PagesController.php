<?php

namespace NS\CmsBundle\Controller;

use NS\CmsBundle\Entity\Block;
use NS\CmsBundle\Event\AfterBlockProcessEvent;
use NS\CmsBundle\Event\AfterPageRenderEvent;
use NS\CmsBundle\Event\BeforeBlockProcessEvent;
use NS\CmsBundle\Event\BlockEvents;
use NS\CmsBundle\Event\PageEvents;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Kernel;

use NS\CmsBundle\Entity\Page;
use NS\CmsBundle\Entity\PageRepository;
use NS\CmsBundle\Manager\BlockManager;

/**
 * Pages controller
 *
 */
class PagesController extends Controller
{
	/**
	 * Main page action
	 *
	 */
	public function mainAction()
	{
		$page = $this->getPageRepository()->findMainPage();
		if (!$page) {
			throw new \Exception("Main page wasn't found");
		}

		return $this->getPageResponse($page);
	}

	/**
	 * Page view action
	 *
	 * @param int $id
	 * @return Response
	 */
	public function pageAction($id)
	{
		$page = $this->getPageRepository()->findPageById($id);

		return $page
			? $this->getPageResponse($page)
			: $this->get404Response($id);
	}

	/**
	 * Page view by name action
	 *
	 * @param  string $name
	 * @return Response
	 */
	public function pageNameAction($name)
	{
		$page = $this
			->getPageRepository()
			->findOneByName($name);

		return $page
			? $this->getPageResponse($page)
			: $this->get404Response($name);
	}

	/**
	 * @param string|int $pageName
	 * @return RedirectResponse
	 * @throws \Exception
	 */
	private function get404Response($pageName)
	{
		$page404 = $this->getPageRepository()->findOneByName('error404');
		if ($page404) {
			return $this->getPageResponse($page404, 404);
		}

		if ($this->container->get('kernel')->getEnvironment() === 'dev') {
			throw new \Exception("Page '{$pageName}' wasn't found. Error page 'error404' wasn't found too.");
		}

		return $this->redirect($this->generateUrl('ns_cms_main'));
	}

	/**
	 * @param Page $page
	 * @param int  $statusCode
	 * @return Response
	 */
	private function getPageResponse(Page $page, $statusCode = null)
	{
		/** @var $kernel Kernel */
		$kernel = $this->container->get('kernel');

		// rendering blocks
		$headers = array();
		$cookies = array();
		foreach ($this->getBlockManager()->getPageBlocks($page) as $block) {
			// new request
			$request = $this->createBlockRequest($block, $page);

			// before block process event
			$beforeBlockProcessEvent = new BeforeBlockProcessEvent($request);
			$this->getEventDispatcher()->dispatch(BlockEvents::BEFORE_PROCESS, $beforeBlockProcessEvent);

			// retrieving response
			if ($beforeBlockProcessEvent->hasResponse()) {
				$response = $beforeBlockProcessEvent->getResponse();
			}
			else {
				$response = $kernel->handle($request);
			}

			// after block process event
			$afterBlockProcessEvent = new AfterBlockProcessEvent($response, $beforeBlockProcessEvent->hasResponse());
			$this->getEventDispatcher()->dispatch(BlockEvents::AFTER_PROCESS, $afterBlockProcessEvent);

			// block can throw 404
			if ($response->getStatusCode() === 404) {
				return $this->get404Response($page->getName());
			}

			// setting block content
			$block->setHtml($response->getContent());

			// merging headers
			$headers = array_merge($headers, $response->headers->all());

			// cookies
			$cookies = array_merge($cookies, $response->headers->getCookies());
		}

		// rendering
		$response = $this->render('NSCmsBundle:Pages:page.html.twig', array(
			'page'   => $page,
			'blocks' => $this->getBlockManager()->getPageBlocks($page),
		));

		// headers
		$headers = array_merge($response->headers->all(), $headers);
		$response->headers->replace($headers);

		// cookies
		$cookies = array_merge($response->headers->getCookies(), $cookies);
		foreach ($cookies as $cookie) {
			$response->headers->setCookie($cookie);
		}

		// response code
		if ($statusCode) {
			$response->setStatusCode($statusCode);
		}

		// after page render event
		$afterPageRenderEvent = new AfterPageRenderEvent($page, $this->getRequest(), $response);
		$this->getEventDispatcher()->dispatch(PageEvents::AFTER_RENDER, $afterPageRenderEvent);

		return $response;
	}

	/**
	 * @param Block $block
	 * @param Page  $page
	 * @return Request
	 */
	private function createBlockRequest(Block $block, Page $page)
	{
		$request = clone $this->getRequest();
		$request->attributes->set('block', $block);
		$request->attributes->set('page', $page);
		$request->attributes->set('_controller', $block->getTypeName());

		return $request;
	}

	/**
	 * Retrieves pages repository
	 *
	 * @return PageRepository
	 */
	private function getPageRepository()
	{
		return $this
			->getDoctrine()
			->getRepository('NSCmsBundle:Page');
	}

	/**
	 * @return BlockManager
	 */
	private function getBlockManager()
	{
		return $this->container->get('ns_cms.manager.block');
	}

	/**
	 * @return EventDispatcherInterface
	 */
	private function getEventDispatcher()
	{
		return $this->container->get('event_dispatcher');
	}

	/**
	 * @param Page     $page
	 * @param Response $response
	 */
	private function throwAfterPageRenderEvent(Page $page, Response $response)
	{
		$afterPageRenderEvent = new AfterPageRenderEvent($page, $this->getRequest(), $response);
		$this->getEventDispatcher()->dispatch(PageEvents::AFTER_RENDER, $afterPageRenderEvent);
	}
}
