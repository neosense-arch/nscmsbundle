<?php

namespace NS\CmsBundle\Form\ChoiceList;

use NS\CmsBundle\Entity\Page;
use NS\CmsBundle\Entity\PageRecursiveIterator;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceList;

/**
 * Class PageChoiceList
 *
 * @package NS\CmsBundle\Form\ChoiceList
 */
class PageChoiceList extends ChoiceList
{
	/**
	 * @param Page $root
	 */
	public function __construct(Page $root)
	{
		$choices = array();

		$iterator = new PageRecursiveIterator(array($root));
		$recursiveIterator = new \RecursiveIteratorIterator($iterator, \RecursiveIteratorIterator::SELF_FIRST);

		/** @var Page $page */
		foreach ($recursiveIterator as $page) {
			$choices[$page->getId()] = $this->getPageLabel($page);
		}

		parent::__construct(array_keys($choices), array_values($choices));
	}

	/**
	 * @param Page $page
	 * @return string
	 */
	private function getPageLabel(Page $page)
	{
		if (!$page->getLevel()) {
			return '[ Корневой уровень ]';
		}

		return str_repeat('-', $page->getLevel()) . ' ' . $page->getTitle();
	}
}
