<?php

namespace NS\CmsBundle\Entity;

use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

/**
 * Pages repository
 *
 */
class PageRepository extends NestedTreeRepository
{
	/**
	 * @return array
	 */
	public function findPagesForDynatree()
	{
		$pages = $this->findBy(array(), array('root' => 'ASC', 'left' => 'ASC'));
		return $this->mapPages($pages);
	}

	/**
	 * @param  Page[] $pages
	 * @param  Page   $parent
	 * @return array
	 */
	private function mapPages(array $pages, Page $parent = null)
	{
		$res = array();

		if (is_null($parent)) {
			$parent = $this->findRootPageOrCreate();
		}

		foreach ($pages as $page) {
			if ($page->getParent() === $parent) {
				$res[] = array(
					'title'    => $page->getTitle(),
					'id'       => $page->getId(),
					'key'      => $page->getId(),
					'children' => $this->mapPages($pages, $page),
				);
			}
		}

		return $res;
	}

	/**
	 * Retrieves first page
	 *
	 * @return Page|null
	 */
	public function findFirstPage()
	{
		$query = $this->_em->createQuery('
			SELECT p FROM NSCmsBundle:Page p WHERE p.parent IS NOT NULL ORDER BY p.root ASC, p.left ASC
		');

		$query->setMaxResults(1);

		return $query->getSingleResult();
	}

	/**
	 * Retrieves pages by ids
	 *
	 * @param  int[] $ids
	 * @return Page[]
	 */
	public function findPagesByIds(array $ids)
	{
		$query = $this->_em->createQuery('
			SELECT p FROM NSCmsBundle:Page p WHERE p.id IN (:ids)
		');

		$pages = $query->execute(array('ids' => $ids));

		return $pages;
	}

	/**
	 * Retrieves pages by parent
	 *
	 * @param  Page $parent
	 * @return Page[]
	 */
	public function findPagesByParent(Page $parent = null)
	{
		if (is_null($parent)) {
			$parent = $this->findRootPageOrCreate();
		}

		$criteria = array(
			'parent' => $parent,
		);

		$order = array(
			'root' => 'ASC',
			'left' => 'ASC',
		);

		return $this->findBy($criteria, $order);
	}

	/**
	 * Retrieves root pages
	 *
	 * @return Page[]
	 */
	public function findRootPages()
	{
		return $this->findPagesByParent();
	}

	/**
	 * Retrieves page by id
	 *
	 * @param  int $id
	 * @return Page
	 */
	public function findPageById($id)
	{
		return $this->findOneBy(array('id' => $id));
	}

	/**
	 * Saves page
	 *
	 * @param  Page $page
	 * @return Page
	 */
	public function savePage(Page $page)
	{
		if (!$page->getParent()) {
			$page->setParent($this->findRootPageOrCreate());
		}

		$this->_em->persist($page);
		$this->_em->flush();

		$this->reorder($page->getParent(), 'position');

		return $page;
	}

	/**
	 * Retrieves root page
	 *
	 * @return Page|null
	 */
	private function findRootPage()
	{
		return $this->findOneBy(array('parent' => null));
	}

	/**
	 * Adds root page
	 *
	 * @return Page
	 */
	private function addRootPage()
	{
		$root = new Page();
		$root->setTitle('ns_cms_pages_root_page');
		$root->setName('ns_cms_pages_root_page');

		$this->_em->persist($root);
		$this->_em->flush();

		return $root;
	}

	/**
	 * Retrieves root page
	 *
	 * @return Page
	 */
	public function findRootPageOrCreate()
	{
		$root = $this->findRootPage();
		if ($root) {
			return $root;
		}

		return $this->addRootPage();
	}
}
