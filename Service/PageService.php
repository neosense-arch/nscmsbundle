<?php

namespace NS\CmsBundle\Service;

use Doctrine\Common\Persistence\ObjectManager;
use NS\CmsBundle\Entity\Page;
use NS\CmsBundle\Entity\PageRepository;

/**
 * Class PageService
 *
 * @package NS\CmsBundle\Service
 */
class PageService
{
	/**
	 * @var PageRepository
	 */
	private $pageRepository;

	/**
	 * @var ObjectManager
	 */
	private $entityManager;

	/**
	 * @return Page|null
	 */
	public function getMainPage()
	{
		return $this->pageRepository->findMainPage();
	}

	/**
	 * @return Page
	 */
	public function getMainPageOrCreate()
	{
		$mainPage = $this->getMainPage();
		if (!$mainPage) {
			$mainPage = $this->createMainPage();
			$this->entityManager->persist($mainPage);
			$this->entityManager->flush();
		}

		return $mainPage;
	}

	/**
	 * @return Page|null
	 */
	public function getRootPage()
	{
		return $this->pageRepository->findRootPage();
	}

	/**
	 * @return Page
	 */
	public function getRootPageOrCreate()
	{
		$rootPage = $this->getRootPage();
		if (!$rootPage) {
			$rootPage = $this->createRootPage();
			$this->entityManager->persist($rootPage);
			$this->entityManager->flush();
		}
		return $rootPage;
	}

	/**
	 * Retrieves page by id
	 *
	 * @param  int $id
	 * @return Page|null
	 */
	public function getPageById($id)
	{
		return $this->pageRepository->findPageById($id);
	}

    /**
     * Retrieves page by name
     *
     * @param string $name
     * @return Page|null
     */
    public function getPageByName($name)
    {
        return $this->pageRepository->findOneBy(array(
            'name' => $name,
        ));
    }

    /**
     * Retrieve page by id or name
     *
     * @param string|int|Page $identifier
     * @return Page|null
     */
    public function getPage($identifier)
    {
        if (is_numeric($identifier)) {
            return $this->getPageById($identifier);
        }
        if (is_string($identifier)) {
            return $this->getPageByName($identifier);
        }
        return null;
    }

    /**
     * Creates new page instance without saving to DB
     *
     * @param string|null          $title
     * @param string|null          $name
     * @param string|int|Page|null $parent
     * @return Page
     */
    public function createPage($title = null, $name = null, $parent = null)
    {
        if ($parent && !$parent instanceof Page) {
            $parent = $this->getPage($parent);
        }

        $page = new Page();
        $page->setTitle($title);
        $page->setName($name);
        $page->setParent($parent);

        return $page;
    }

    /**
     * Updates page
     *
     * @param Page $page
     * @return $this
     */
    public function updatePage(Page $page)
    {
        $this->entityManager->persist($page);
        $this->entityManager->flush();
        return $this;
    }

	/**
	 * @param ObjectManager $entityManager
	 */
	public function setEntityManager($entityManager)
	{
		$this->entityManager = $entityManager;
	}
	/**
	 * @return ObjectManager
	 */
	public function getEntityManager()
	{
		return $this->entityManager;
	}
	/**
	 * @param PageRepository $pageRepository
	 */
	public function setPageRepository($pageRepository)
	{
		$this->pageRepository = $pageRepository;
	}
	/**
	 * @return PageRepository
	 */
	public function getPageRepository()
	{
		return $this->pageRepository;
	}

	/**
	 * @return Page
	 */
	private function createRootPage()
	{
		$page = new Page();

		$page->setTitle(Page::ROOT_PAGE_NAME);
		$page->setName(Page::ROOT_PAGE_TITLE);

		return $page;
	}

	/**
	 * @return Page
	 */
	private function createMainPage()
	{
		$page = new Page();

		$page->setTitle(Page::MAIN_PAGE_TITLE);
		$page->setName(Page::MAIN_PAGE_NAME);
		$page->setParent($this->getRootPageOrCreate());

		return $page;
	}
}