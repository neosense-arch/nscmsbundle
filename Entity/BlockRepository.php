<?php

namespace NS\CmsBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Blocks repository
 *
 */
class BlockRepository extends EntityRepository
{
	/**
	 * Retrieves page blocks
	 *
	 * @param Page $page
	 * @return mixed
	 */
	public function findPageBlocks(Page $page)
	{
		$query = $this->_em->createQuery("
			SELECT b
			FROM NSCmsBundle:Block b
			WHERE b.page = :page OR b.page IS NULL
			ORDER BY b.areaName, b.position
		");

		return $query->execute(array('page' => $page));
	}

	/**
	 * Retrieves block by id
	 *
	 * @param int $id
	 * @return Block|null
	 */
	public function findBlockById($id)
	{
		return $this->findOneBy(array('id' => $id));
	}

	/**
	 * @param  string $typeName
	 * @return Block[]
	 */
	public function findBlocksByTypeName($typeName)
	{
		return $this->findBy(array('typeName' => $typeName));
	}

	/**
	 * Retrieves shared blocks
	 *
	 * @return Block[]
	 */
	public function findSharedBlocks()
	{
		return $this->findBy(array('shared' => true), array('title' => 'ASC'));
	}
}
