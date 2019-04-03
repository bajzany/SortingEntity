<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 */

namespace Bajzany\SortingEntity\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;

class AbstractRepository extends EntityRepository
{

	/**
	 * @var EntityManagerInterface
	 */
	protected $entityManager;

	/**
	 * {@inheritdoc}
	 */
	public function __construct(EntityManagerInterface $em, ClassMetadata $class)
	{
		parent::__construct($em, $class);
		$this->entityManager = $em;
	}

}
