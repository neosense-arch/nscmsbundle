<?php

namespace NS\CmsBundle\Entity;

use Doctrine\ORM\EntityRepository;
use NS\CmsBundle\Service\TemplateService;

/**
 * Blocks repository
 *
 */
class BlockRepository extends EntityRepository
{
    /**
     * @var TemplateService
     */
    private $templateService;

    /**
     * @param TemplateService $templateService
     */
    public function setTemplateService(TemplateService $templateService)
    {
        $this->templateService = $templateService;
    }

	/**
	 * Retrieves page blocks
	 *
	 * @param Page $page
	 * @return mixed
	 */
	public function findPageBlocks(Page $page)
	{
        // retrieving page areas
        $template = $this->templateService->getPageTemplate($page);
        $areas = array();
        foreach ($template->getAreas() as $area) {
            $areas[] = $area->getName();
        }

        $queryBuilder = $this->createQueryBuilder('b')
            ->andWhere('b.page = :page')
            ->setParameter('page', $page)

            ->orWhere('b.page IS NULL')

            ->andWhere('b.areaName IN (:areas)')
            ->setParameter('areas', $areas)

            ->orderBy('b.areaName')
            ->addOrderBy('b.position');

        return $queryBuilder->getQuery()->getResult();
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
		$query = $this->_em->createQuery("
			SELECT b, p
			FROM NSCmsBundle:Block b
			JOIN b.page p
			WHERE b.typeName = :typeName
			ORDER BY b.areaName, b.position
		");

		$params = array(
			'typeName' => $typeName,
		);

		return $query->execute($params);
	}

	/**
	 * Retrieves blocks by type name and ID array
	 *
	 * @param  string $typeName
	 * @param  int[]  $ids
	 * @return Block[]
	 */
	public function findBlocksByTypeNameAndIds($typeName, array $ids)
	{
		$query = $this->_em->createQuery("
			SELECT b, p
			FROM NSCmsBundle:Block b
			JOIN b.page p
			WHERE b.typeName = :typeName AND b.id IN (:ids)
			ORDER BY b.areaName, b.position
		");

		$params = array(
			'typeName' => $typeName,
			'ids'      => $ids,
		);

		return $query->execute($params);
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

    /**
     * Retrieves unlinked buffer blocks
     *
     * @return Block[]
     */
    public function findBufferBlocks()
    {
        $queryBuilder = $this->createQueryBuilder('b')
            ->andWhere('b.page IS NULL')
            ->andWhere('b.areaName IS NULL')
            ->addOrderBy('b.position');

        return $queryBuilder->getQuery()->getResult();
    }
}
