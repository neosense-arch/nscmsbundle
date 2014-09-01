<?php

namespace NS\CmsBundle\Event;

use NS\CmsBundle\Manager\TemplateManager;
use NS\CmsBundle\Service\PageService;

/**
 * Class InstallListener
 *
 * @package NS\CmsBundle\Event
 */
class InstallListener
{
    /**
     * @var PageService
     */
    private $pageService;

    /**
     * @var TemplateManager
     */
    private $templateManager;

    /**
     * @param PageService     $pageService
     * @param TemplateManager $templateManager
     */
    public function __construct(PageService $pageService, TemplateManager $templateManager)
    {
        $this->pageService     = $pageService;
        $this->templateManager = $templateManager;
    }

    /**
     * Install event
     *
     * @return mixed
     */
    public function onInstall()
    {
        // creating main page
        $this->pageService->getMainPageOrCreate();

        // copying base template
        $this->templateManager->createUserTemplate('NSCmsBundle:Pages:page.html.twig');
    }
}