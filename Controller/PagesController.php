<?php

namespace NS\CmsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
		if (!$page) {
			// @todo 404
			return $this->redirect($this->generateUrl('ns_cms_main'));
		}

		return $this->getPageResponse($page);
	}

	/**
	 * Page view by name action
	 *
	 * @param  string $name
	 * @return Response
	 */
	public function pageNameAction($name)
	{
		$page = $this->getPageRepository()->findPageByName($name);
		if (!$page) {
			// @todo 404
			return $this->redirect($this->generateUrl('ns_cms_main'));
		}

		return $this->getPageResponse($page);
	}

	/**
	 * @param  Page $page
	 * @return Response
	 */
	private function getPageResponse(Page $page)
	{
		/** @var $kernel Kernel */
		$kernel = $this->container->get('kernel');

		// rendering blocks
		foreach ($this->getBlockManager()->getPageBlocks($page) as $block) {
			// new request
			$request = clone $this->getRequest();
			$request->attributes->set('block', $block);
			$request->attributes->set('page', $page);
			$request->attributes->set('_controller', $block->getTypeName());

			// rendering
			$response = $kernel->handle($request);
			$block->setHtml($response->getContent());
		}

		return $this->render('NSCmsBundle:Pages:page.html.twig', array(
			'page'   => $page,
			'blocks' => $this->getBlockManager()->getPageBlocks($page),
		));
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
}
