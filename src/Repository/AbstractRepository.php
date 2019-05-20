<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 */

namespace Bajzany\SortingEntity\Repository;

use Doctrine\ORM\Mapping\ClassMetadata;
use Kdyby\Doctrine\EntityManager;
use Kdyby\Doctrine\EntityRepository;

abstract class AbstractRepository extends EntityRepository
{

	/**
	 * @var EntityManager
	 */
	protected $entityManager;

	/**
	 * @param EntityManager $entityManager
	 * @param ClassMetadata $class
	 */
	public function __construct(EntityManager $entityManager, ClassMetadata $class)
	{
		$this->entityManager = $entityManager;
		parent::__construct($entityManager, $class);
	}

	/**
	 * @return EntityManager
	 */
	public function getEntityManager(): EntityManager
	{
		return $this->entityManager;
	}

}
