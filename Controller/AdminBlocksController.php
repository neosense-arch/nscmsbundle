<?php

namespace NS\CmsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
            /** @var TemplateManager $templateManager */
            $templateManager = $this->get('ns_cms.manager.template');
			$area = $templateManager->getAreaByPageAndName($page, $_GET['areaName']);

			// adding block
			$block = new Block();
			$block->setTitle($blockType->getTitle());
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

            // overriding block template
            $templateManager->createUserTemplate($block->getType()->getTemplate());

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
			if (empty($_GET['pageId'])) {
				return new JsonResponse(array('error' => 'Param "pageId" is required'));
			}
			if (!isset($_GET['position'])) {
				return new JsonResponse(array('error' => 'Param "position" is required'));
			}

			// checking block
            /** @var BlockManager $blockManager */
            $blockManager = $this->get('ns_cms.manager.block');
			$block = $blockManager->getBlock($_GET['blockId']);

			// checking page
			$page = $this->getPageRepository()->findPageById($_GET['pageId']);
			if (!$page) {
				return new JsonResponse(array('error' => "Page #{$_GET['pageId']} wasn't found"));
			}

            // setting area
            if (!empty($_GET['areaName'])) {
                // template area
                /** @var TemplateManager $templateManager */
                $templateManager = $this->get('ns_cms.manager.template');
                $area = $templateManager->getAreaByPageAndName($page, $_GET['areaName']);
                $block->setArea($area);

                // adding page link if area is not fixed
                if ($area->isFixed()) {
                    $block->removePage();
                }
                else {
                    $block->setPage($page);
                }
            }
            else {
                $block->removeArea();
                $block->removePage();
            }

            // reordering
            $block->setPosition($_GET['position']);

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
            /** @var BlockManager $blockManager */
            $blockManager = $this->get('ns_cms.manager.block');
			$block = $blockManager->getBlock($_GET['blockId']);

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
     * Clones blocks
     *
     * @throws \Exception
     * @return JsonResponse
     */
    public function ajaxCloneAction()
    {
        try {
            if (empty($_GET['blockId'])) {
                return new JsonResponse(array('error' => 'Param "blockId" is required'));
            }

            // checking block
            /** @var BlockManager $blockManager */
            $blockManager = $this->get('ns_cms.manager.block');
            $block = $blockManager->getBlock($_GET['blockId']);

            // cloning block
            $clone = $blockManager->cloneBlock($block);

            // retrieving new block id
            return new JsonResponse(array(
                'id'    => $clone->getId(),
                'title' => $clone->getTitle(),
            ));
        }
        catch (\Exception $e) {
            return new JsonResponse(array('error' => "Exception occurred: {$e->getMessage()}"));
        }
    }

    /**
     * @param Request $request
     * @throws \Exception
     * @return Response
     */
	public function settingsAction(Request $request)
	{
        // checking block id
        $blockId = $request->query->get('blockId');
		if (!$blockId) {
			throw new \Exception("Required param 'blockId' wasn't found");
		}

		// retrieving block
        /** @var BlockManager $blockManager */
        $blockManager = $this->get('ns_cms.manager.block');
		$block = $blockManager->getBlock($blockId);
		$blockType = $blockManager->getBlockType($block->getTypeName());

		// form type
		if (!$blockType->getSettingFormClass()) {
			return $this->redirect($this->generateUrl('ns_admin_bundle', array(
				'adminBundle'     => 'NSCmsBundle',
				'adminController' => 'blocks',
				'adminAction'     => 'general',
			)) . '?blockId=' . $block->getId() . '&redirect=' . $this->getRedirectUri());
		}
		$formType = $blockType->createSettingsFormType();

		// retrieving settings model
		$settingsModel = $blockType->createSettingsModel();
		if ($block->getSettings()) {
			$settingsModel = unserialize($block->getSettings());
		}

		// initializing form
		$form = $this->createForm($formType, $settingsModel);

		// validating form
        $form->handleRequest($request);
        if ($form->isValid()) {
            $block->setSettings(serialize($settingsModel));
            $this->getDoctrine()->getManager()->flush();
            return $this->redirect($this->getRedirectUri());
        }

		return $this->render('NSCmsBundle:AdminBlocks:settings.html.twig', array(
			'form'     => $form->createView(),
			'block'    => $block,
			'redirect' => $this->getRedirectUri(),
		));
	}

    /**
     * @param Request $request
     * @throws \Exception
     * @return Response
     */
	public function generalAction(Request $request)
	{
        // checking block id
        $blockId = $request->query->get('blockId');
        if (!$blockId) {
            throw new \Exception("Required param 'blockId' wasn't found");
        }

        /** @var TemplateManager $templateManager */
        $templateManager = $this->get('ns_cms.manager.template');
        $templates = $templateManager->getAllUserTemplates();

		// retrieving block
        /** @var BlockManager $blockManager */
        $blockManager = $this->get('ns_cms.manager.block');
        $block = $blockManager->getBlock($blockId);

		// initializing form
		$form = $this->createForm(new BlockType(), $block);

		// validating form
		$form->handleRequest($request);
        if ($form->isValid()) {
            $templateManager->createUserTemplate($block->getType()->getTemplate(), $block->getTemplate());

            $this->getDoctrine()->getManager()->flush();
            return $this->redirect($this->getRedirectUri());
        }

		return $this->render('NSCmsBundle:AdminBlocks:general.html.twig', array(
            'form'      => $form->createView(),
            'block'     => $block,
            'redirect'  => $this->getRedirectUri(),
            'templates' => $templates,
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
