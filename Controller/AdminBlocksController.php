<?php

namespace NS\CmsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

use NS\CmsBundle\Entity\Block;
use NS\CmsBundle\Form\BlockType;

use NS\CmsBundle\Entity\PageRepository;
use NS\CmsBundle\Entity\BlockTypeRepository;

use NS\CmsBundle\Manager\TemplateManager;
use NS\CmsBundle\Manager\BlockManager;

/**
 * Admin blocks controller
 *
 */
class AdminBlocksController extends Controller
{
	/**
	 * Adds page block
	 *
	 * @return JsonResponse
	 */
	public function ajaxAddAction()
	{
		try {
			if (empty($_GET['blockType'])) {
				return new JsonResponse(array('error' => 'Param "blockType" is required'));
			}
			if (empty($_GET['areaName'])) {
				return new JsonResponse(array('error' => 'Param "areaName" is required'));
			}
			if (empty($_GET['pageId'])) {
				return new JsonResponse(array('error' => 'Param "pageId" is required'));
			}
			if (!isset($_GET['position'])) {
				return new JsonResponse(array('error' => 'Param "position" is required'));
			}

			// checking page
			$page = $this->getPageRepository()->findPageById($_GET['pageId']);
			if (!$page) {
				return new JsonResponse(array('error' => "Page #{$_GET['pageId']} wasn't found"));
			}

			// checking block type
			$blockType = $this->getBlockTypeRepository()->findBlockTypeByName($_GET['blockType']);
			if (!$blockType) {
				return new JsonResponse(array('error' => "Block type '{$_GET['blockType']}' wasn't found"));
			}

			// template area
			$area = $this->getTemplateManager()->getAreaByPageAndName($page, $_GET['areaName']);

			// adding block
			$block = new Block();
			$block->setTitle('default');
			$block->setType($blockType);
			$block->setArea($area);
			$block->setPosition($_GET['position']);
			$block->setShared(false);

			// adding page link if area is not fixed
			if (!$area->isFixed()) {
				$block->setPage($page);
			}

			// saving block
			$this->getDoctrine()->getManager()->persist($block);
			$this->getDoctrine()->getManager()->flush();

			// new title based on block type
			$block->setTitle($blockType->getTitle() . ' #' . $block->getId());
			$this->getDoctrine()->getManager()->flush();

			// retrieving new block id
			return new JsonResponse(array(
				'id'    => $block->getId(),
				'title' => $block->getTitle(),
			));
		}
		catch (\Exception $e) {
			return new JsonResponse(array('error' => "Exception occurred: {$e->getMessage()}"));
		}
	}

	/**
	 * Reorders blocks
	 *
	 * @throws \Exception
	 * @return JsonResponse
	 */
	public function ajaxReorderAction()
	{
		try {
			if (empty($_GET['blockId'])) {
				return new JsonResponse(array('error' => 'Param "blockId" is required'));
			}
			if (empty($_GET['areaName'])) {
				return new JsonResponse(array('error' => 'Param "areaName" is required'));
			}
			if (empty($_GET['pageId'])) {
				return new JsonResponse(array('error' => 'Param "pageId" is required'));
			}
			if (!isset($_GET['position'])) {
				return new JsonResponse(array('error' => 'Param "position" is required'));
			}

			// checking block
			$block = $this->getBlockManager()->getBlock($_GET['blockId']);

			// checking page
			$page = $this->getPageRepository()->findPageById($_GET['pageId']);
			if (!$page) {
				return new JsonResponse(array('error' => "Page #{$_GET['pageId']} wasn't found"));
			}

			// template area
			$area = $this->getTemplateManager()->getAreaByPageAndName($page, $_GET['areaName']);

			// reordering
			$block->setArea($area);
			$block->setPosition($_GET['position']);

			// adding page link if area is not fixed
			if ($area->isFixed()) {
				$block->removePage();
			}
			else {
				$block->setPage($page);
			}

			// saving block
			$this->getDoctrine()->getManager()->flush();

			// retrieving new block id
			return new JsonResponse(array(
				'id'    => $block->getId(),
				'title' => $block->getTitle(),
			));
		}
		catch (\Exception $e) {
			return new JsonResponse(array('error' => "Exception occurred: {$e->getMessage()}"));
		}
	}

	/**
	 * Removes block
	 *
	 * @throws \Exception
	 * @return JsonResponse
	 */
	public function ajaxDeleteAction()
	{
		try {
			if (empty($_GET['blockId'])) {
				return new JsonResponse(array('error' => 'Param "blockId" is required'));
			}

			// checking block
			$block = $this->getBlockManager()->getBlock($_GET['blockId']);

			// saving block
			$this->getDoctrine()->getManager()->remove($block);
			$this->getDoctrine()->getManager()->flush();

			// retrieving new block id
			return new JsonResponse(array());
		}
		catch (\Exception $e) {
			return new JsonResponse(array('error' => "Exception occurred: {$e->getMessage()}"));
		}
	}

	/**
	 * @throws \Exception
	 * @return Response
	 */
	public function settingsAction()
	{
		// checking block id
		if (empty($_GET['blockId'])) {
			throw new \Exception("Required param 'blockId' wasn't found");
		}

		// retrieving block
		$block = $this->getBlockManager()->getBlock($_GET['blockId']);
		$blockType = $this->getBlockManager()->getBlockType($block->getTypeName());

		// form type
		$formTypeClass = $blockType->getSettingFormClass();
		$formType = new $formTypeClass();

		// retrieving settings model
		$settingsModelClass = $blockType->getSettingsModelClass();
		$settingsModel = new $settingsModelClass;
		if ($block->getSettings()) {
			$settingsModel = unserialize($block->getSettings());
		}

		// initializing form
		$form = $this->createForm($formType, $settingsModel);

		// validating form
		if ($this->getRequest()->getMethod() === 'POST') {
			$form->bind($this->getRequest());
			if ($form->isValid()) {
				$block->setSettings(serialize($settingsModel));
				$this->getDoctrine()->getManager()->flush();
				return $this->redirect($this->getRedirectUri());
			}
		}

		return $this->render('NSCmsBundle:AdminBlocks:settings.html.twig', array(
			'form'     => $form->createView(),
			'block'    => $block,
			'redirect' => $this->getRedirectUri(),
		));
	}

	/**
	 * @throws \Exception
	 * @return Response
	 */
	public function generalAction()
	{
		// checking block id
		if (empty($_GET['blockId'])) {
			throw new \Exception("Required param 'blockId' wasn't found");
		}

		// retrieving block
		$block = $this->getBlockManager()->getBlock($_GET['blockId']);

		// initializing form
		$form = $this->createForm(new BlockType(), $block);

		// validating form
		if ($this->getRequest()->getMethod() === 'POST') {
			$form->bind($this->getRequest());
			if ($form->isValid()) {
				$this->getDoctrine()->getManager()->flush();
				return $this->redirect($this->getRedirectUri());
			}
		}

		return $this->render('NSCmsBundle:AdminBlocks:general.html.twig', array(
			'form'     => $form->createView(),
			'block'    => $block,
			'redirect' => $this->getRedirectUri(),
		));
	}

	/**
	 * @return PageRepository
	 */
	private function getPageRepository()
	{
		return $this
			->getDoctrine()
			->getRepository('NSCmsBundle:Page');
	}

	/**
	 * @return BlockTypeRepository
	 */
	private function getBlockTypeRepository()
	{
		return $this->container->get('ns_cms.repository.blocktype');
	}

	/**
	 * @return TemplateManager
	 */
	private function getTemplateManager()
	{
		return $this->container->get('ns_cms.manager.template');
	}

	/**
	 * @return BlockManager
	 */
	private function getBlockManager()
	{
		return $this->container->get('ns_cms.manager.block');
	}

	/**
	 * Retrieves redirect URI
	 *
	 * @return string
	 * @throws \Exception
	 */
	private function getRedirectUri()
	{
		if (empty($_GET['redirect'])) {
			throw new \Exception("Required param 'redirect' wasn't found");
		}
		return $_GET['redirect'];
	}
}
