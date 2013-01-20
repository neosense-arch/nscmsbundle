<?php

namespace NS\CmsBundle\Menu\Matcher\Voter;

use Knp\Menu\Matcher\Voter\VoterInterface;
use Knp\Menu\ItemInterface;
use NS\CmsBundle\Entity\Page;

/**
 * Page voter
 *
 */
class PageVoter implements VoterInterface
{
	/**
	 * @var Page
	 */
	private $page;

	/**
	 * @param Page $page
	 */
	public function __construct(Page $page)
	{
		$this->page = $page;
	}

	/**
	 * Checks whether an item is current.
	 *
	 * If the voter is not able to determine a result,
	 * it should return null to let other voters do the job.
	 *
	 * @param ItemInterface $item
	 *
	 * @return boolean|null
	 */
	public function matchItem(ItemInterface $item)
	{
		return $this->page->getId() == $item->getName();
	}

}
