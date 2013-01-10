<?php

namespace NS\CmsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

use NS\CmsBundle\Entity\Page;
use NS\CmsBundle\Entity\PageRepository;

use Doctrine\ORM\NoResultException;

/**
 * Admin content controller
 *
 */
class AdminContentController extends Controller
{
	/**
	 * Index content page
	 *
	 * @return Response
	 */
	public function indexAction()
    {
		try {
			// redirects to first page settings
			$page = $this->getPageRepository()->findFirstPage();
			return $this->redirect($this->generateUrl(
				'ns_admin_bundle', array(
					'adminBundle' => 'NSCmsBundle',
					'adminController' => 'pages',
					'adminAction' => 'general',
				)
			) . '?pageId=' . $page->getId());
		}
		catch (NoResultException $e) {
			// redirects to add page
			return $this->redirect($this->generateUrl(
				'ns_admin_bundle', array(
					'adminBundle' => 'NSCmsBundle',
					'adminController' => 'pages',
					'adminAction' => 'form',
				)
			));
		}
    }

	/**
	 * Retrieves dynatree lazy-load nodes
	 *
	 * @return JsonResponse
	 */
	public function ajaxGetPagesAction()
	{
		$expanded = array();
		if (!empty($_GET['expandedKeyList'])) {
			$expanded = array_unique(explode(',', $_GET['expandedKeyList']));
		}

		$aPages = $this->getPageRepository()->findPagesForDynatree($expanded);

		return new JsonResponse($aPages);
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
}