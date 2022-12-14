<?php

namespace NS\CmsBundle\Controller;

use NS\CmsBundle\Service\TemplateService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\Form\AbstractType;

use NS\CmsBundle\Entity\Page;
use NS\CmsBundle\Entity\PageRepository;
use NS\CmsBundle\Form\PageType;

use NS\CmsBundle\Form\PageGeneralSettingsType;
use NS\CmsBundle\Form\PageAdditionalSettingsType;

use NS\CmsBundle\Entity\Template;
use NS\CmsBundle\Entity\TemplateRepository;

use NS\CmsBundle\Entity\BlockTypeRepository;

use NS\CmsBundle\Entity\BlockRepository;

use NS\CmsBundle\Manager\BlockManager;

/**
 * Admin pages controller
 *
 */
class AdminPagesController extends Controller
{
	/**
	 * Pages tree block
	 *
	 * @return Response
	 */
	public function blockPagesTreeAction()
	{
		// pages
		$pages = $this->getPageRepository()->findPagesForDynatree();

		// active page
		$pageId = null;
		if (!empty($_GET['pageId'])) {
			$pageId = $_GET['pageId'];
		}

		return $this->render('NSCmsBundle:AdminPages:block-pages-tree.html.twig', array(
			'pagesJson' => json_encode($pages),
			'pageId'    => $pageId,
		));
	}

	/**
	 * Adds new page
	 *
	 * @return RedirectResponse
	 */
	public function addAction()
	{
		$page = $this->getPageRepository()->createDefaultPage();
		return $this->redirect($this->generateUrl('ns_admin_bundle', array(
			'adminBundle'     => 'NSCmsBundle',
			'adminController' => 'pages',
			'adminAction'     => 'general'
		)) . '?pageId=' . $page->getId());
	}

	/**
	 * @return Response
	 */
	public function formAction()
	{
		// edit mode
		if (!empty($_GET['id'])) {
			// retrieving page
			$page = $this
				->getPageRepository()
				->findOneBy(array('id' => $_GET['id']));

			// checking if page exists
			if (!$page) {
				return $this->back();
			}
		}

		// creation mode
		else {
			$page = new Page();
		}

		// initializing form
		$form = $this->createForm(new PageType(), $page);

		// validating form
		if ($this->getRequest()->getMethod() === 'POST') {
			$form->submit($this->getRequest());
			if ($form->isValid()) {
				$this->getPageRepository()->savePage($page);
				return $this->back();
			}
		}

		return $this->render('NSAdminBundle:Generic:form.html.twig', array(
			'form' => $form->createView(),
			'form_label' => $page->getId()
				? '???????????????????????????? ????????????????' : '???????????????? ????????????????'
		));
	}

	/**
	 * @return Response
	 */
	public function generalAction()
	{
		return $this->pageFormAction(new PageGeneralSettingsType(), 'general');
	}

	/**
	 * @return Response
	 */
	public function additionalAction()
	{
		return $this->pageFormAction(new PageAdditionalSettingsType(), 'additional');
	}

	/**
	 * @param  AbstractType $form
	 * @param  string       $tab
	 * @return Response
	 */
	private function pageFormAction(AbstractType $form, $tab)
	{
		// checking page id
		if (empty($_GET['pageId'])) {
			return $this->back();
		}

		// retrieving page
		$page = $this->getPageRepository()->findPageById($_GET['pageId']);
		if (!$page) {
			return $this->back();
		}

		// initializing form
		$form = $this->createForm($form, $page);

		// validating form
		if ($this->getRequest()->getMethod() === 'POST') {
			$form->submit($this->getRequest());
			if ($form->isValid()) {
				$this->getPageRepository()->savePage($page);
			}
		}

		return $this->render('NSCmsBundle:AdminPages:settings.html.twig', array(
			'form'   => $form->createView(),
			'pageId' => $page->getId(),
			'tab'    => $tab,
			'page'   => $page,
		));
	}

	/**
	 *
	 * @return Response
	 */
	public function blocksAction()
	{
		if (empty($_GET['pageId'])) {
			return $this->back();
		}

		$page = $this->getPageRepository()->findPageById($_GET['pageId']);
		if (!$page) {
			return $this->back();
		}

		/** @var TemplateService $templateService */
		$templateService = $this->get('ns_cms.service.template');
		$template = $templateService->getPageTemplate($page);

		// available block types
		$blockTypes = $this->getBlockTypeRepository()->findAll();

		// page blocks
		$blocks = $this->getBlockManager()->getPageBlocks($page);

		// shared blocks
		$shared = $this->getBlockManager()->getSharedBlocks();

        // buffered blocks
        $buffered = $this->getBlockManager()->getBufferBlocks();

        // area rows
        $areaRows = array();
        foreach ($template->getAreas() as $area) {
            $rowName = $area->getRow() ?: uniqid();
            if (!isset($areaRows[$rowName])) {
                $areaRows[$rowName] = array();
            }
            $areaRows[$rowName][] = $area;
        }

		return $this->render('NSCmsBundle:AdminPages:blocks.html.twig', array(
			'page'       => $page,
			'template'   => $template,
			'blockTypes' => $blockTypes,
			'blocks'     => $blocks,
			'shared'     => $shared,
            'buffered'   => $buffered,
            'areaRows'   => $areaRows,
		));
	}

	/**
	 * Reorders pages
	 *
	 * @return JsonResponse
	 */
	public function ajaxReorderAction()
	{
		try {
			// checking page id
			if (empty($_GET['pageId'])) {
				return new JsonResponse(array('error' => 'Param "pageId" is required'));
			}

			// retrieving page
			$page = $this->getPageRepository()->findPageById($_GET['pageId']);
			if (!$page) {
				return new JsonResponse(array('error' => "Page #{$_GET['pageId']} wasn't found"));
			}

			// checking position
			if (!isset($_GET['position'])) {
				return new JsonResponse(array('error' => 'Param "position" is required'));
			}

			// reordering
			$page->setPosition($_GET['position']);
			$this->getPageRepository()->savePage($page);

			// retrieving new position
			return new JsonResponse(array('position' => $page->getPosition()));
		}
		catch (\Exception $e) {
			return new JsonResponse(array('error' => "Exception occurred: {$e->getMessage()}"));
		}
	}

	/**
	 * Removes page
	 *
	 * @return Response
	 */
	public function deleteAction()
	{
		// edit mode
		if (!empty($_GET['id'])) {
			// retrieving page
			$page = $this
				->getPageRepository()
				->findOneBy(array('id' => $_GET['id']));

			// checking if page exists
			if (!$page) {
				return $this->back();
			}

			// removing page
			$this->getEntityManager()->remove($page);
			$this->getEntityManager()->flush();
		}

		return $this->back();
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
	 * @return BlockTypeRepository
	 */
	private function getBlockTypeRepository()
	{
		return $this->container->get('ns_cms.repository.blocktype');
	}

	/**
	 * Retrieves entity manager
	 *
	 * @return \Doctrine\Common\Persistence\ObjectManager
	 */
	private function getEntityManager()
	{
		return $this->getDoctrine()->getManager();
	}

	/**
	 * Redirects back
	 *
	 * @return RedirectResponse
	 */
	private function back()
	{
		return $this->redirect($this->generateUrl(
			'ns_admin_bundle', array(
				'adminBundle'     => 'NSCmsBundle',
				'adminController' => 'content',
				'adminAction'     => 'index',
			)
		));
	}
}
