<?php

/**
 * Author: Radek Zíka
 * Email: radek.zika@dipcom.cz
 */

namespace Bajzany\SortingEntity\Repository;

use Bajzany\SortingEntity\Entity\ISortingEntity;
use Bajzany\SortingEntity\Exceptions\SortingException;
use Doctrine\Common\Collections\Criteria;

class SortingRepository extends AbstractRepository
{

	/**
	 * @param null|ISortingEntity|int $parent
	 * @param bool $compareParent
	 * @param bool $getQueryBuilder
	 * @return mixed|string
	 */
	public function getSorted($parent = NULL, bool $compareParent = FALSE, $getQueryBuilder = FALSE)
	{
		$qb = $this->createQueryBuilder('s');
		if ($compareParent) {
			$qb->where('s.parent = :parent')
				->setParameter('parent', $parent);
		}

		$qb->addOrderBy('s.sorting', 'ASC');

		if ($getQueryBuilder) {
			return $qb;
		}

		return $qb->getQuery()->getResult();
	}

	/**
	 * @param ISortingEntity $entity
	 * @param ISortingEntity $target
	 * @throws \Doctrine\DBAL\DBALException
	 */
	public function moveUp(ISortingEntity $entity, ISortingEntity $target)
	{
		$sorting = $target->getSorting();

		if ($entity->getParent() !== $target->getParent()) {
			$newParent = $target->getParent();
			$entity->setParent($newParent);
			$this->testIfCanSetParent($newParent, $entity);

			$lvl = $this->getEntityLvl($entity);
			$entity->setLvl($lvl);
		}

		$entity->setMoved(TRUE);
		$this->setEntityParentSorting($entity, $sorting - 1);
		$this->updateChildren($entity, FALSE);
		$this->entityManager->persist($entity);
		$this->entityManager->flush();
	}

	/**
	 * @param ISortingEntity $entity
	 * @param ISortingEntity $target
	 * @throws \Doctrine\DBAL\DBALException
	 */
	public function moveDown(ISortingEntity $entity, ISortingEntity $target)
	{
		$sorting = $target->getSorting();
		$parentSorting = $this->getSectionLastSorting($target, $sorting);

		if ($entity->getParent() !== $target->getParent()) {
			$newParent = $target->getParent();
			$entity->setParent($newParent);
			$this->testIfCanSetParent($newParent, $entity);

			$lvl = $this->getEntityLvl($entity);
			$entity->setLvl($lvl);
		}

		$entity->setMoved(TRUE);
		$this->setEntityParentSorting($entity, $parentSorting);
		$this->updateChildren($entity, FALSE);
		$this->entityManager->persist($entity);
		$this->entityManager->flush();
	}

	/**
	 * @param ISortingEntity $entity
	 * @param int $sorting
	 * @return int
	 */
	public function getSectionLastSorting(ISortingEntity $entity, int &$sorting)
	{
		$children = $entity->getChildren();
		if (!empty($children)) {
			$criteria = new Criteria(NULL, ['sorting' => Criteria::DESC]);
			$children = $children->matching($criteria);
			/*** @var ISortingEntity $child */
			foreach ($children as $child) {
				$sort = $child->getSorting();
				return $this->getSectionLastSorting($child, $sort);
			}
		}

		return $sorting;
	}

	/**
	 * @param ISortingEntity $entity
	 * @param int $lvl
	 * @return int
	 */
	public function getEntityLvl(ISortingEntity $entity, int $lvl = 0)
	{
		$parent = $entity->getParent();

		if ($parent) {
			$lvl = $lvl + 1;
			return $this->getEntityLvl($parent, $lvl);
		}

		return $lvl;
	}

	/**
	 * @param ISortingEntity $entity
	 * @param bool $changeSet
	 * @throws \Doctrine\DBAL\DBALException
	 */
	public function updateChildren(ISortingEntity $entity, bool $changeSet = TRUE)
	{
		$children = $entity->getChildren();
		if (!empty($children)) {
			/*** @var ISortingEntity $child */
			foreach ($children as $child) {
				$this->setEntityParentSorting($child, $entity->getSorting());

				$lvl = $this->getEntityLvl($child);
				$child->setLvl($lvl);
				$this->entityManager->persist($child);

				if ($changeSet) {
					$classMetadata = $this->entityManager->getClassMetadata(get_class($child));
					$this->entityManager->getUnitOfWork()->computeChangeSet($classMetadata, $child);
				}

				$this->updateChildren($child, $changeSet);
			}
		}
	}

	/**
	 * @param ISortingEntity $entity
	 * @param int $parentSorting
	 * @throws \Doctrine\DBAL\DBALException
	 */
	public function setEntityParentSorting(ISortingEntity $entity, int $parentSorting)
	{
		$tableName = $this->entityManager->getClassMetadata(get_class($entity))->getTableName();
		$startSorting = $parentSorting + 1;
		$stm = $this->entityManager->getConnection()->prepare(
			"SET @i := {$startSorting}; UPDATE {$tableName} SET sorting = @i:=@i+1 WHERE sorting > {$parentSorting} ORDER BY sorting ASC"
		);
		$stm->execute();
		$stm->closeCursor();

		$entity->setSorting($startSorting);
	}

	/**
	 * @param ISortingEntity|null $parent
	 * @param ISortingEntity $entity
	 * @throws \Exception
	 */
	public function testIfCanSetParent(?ISortingEntity $parent, ISortingEntity $entity)
	{
		if ($parent === NULL) {
			return;
		}

		if ($parent->getParent() === $entity->getParent()) {
			throw SortingException::parentValidationException();
		}

		foreach ($entity->getChildren() as $child) {
			$this->testIfCanSetParent($parent, $child);
		}

	}

	/**
	 * @param array $list
	 * @param null $parent
	 * @return array
	 */
	public function getTree(array $list = NULL, $parent = NULL)
	{
		if ($list === NULL) {
			$list = $this->getSorted();
		}

		return $this->treeRecursive($list, $parent);
	}

	/**
	 * @param array $list
	 * @param null $parent
	 * @return array
	 */
	private function treeRecursive(array $list, $parent = NULL)
	{
		$tree = [];
		/*** @var ISortingEntity $item */
		foreach ($list as $item){
			if($item->getParentId() === $parent){
				$childern = $this->treeRecursive($list, $item->getId());
				$tree[] = [
					"item" => $item,
					"count" => count($childern),
					"children" => $childern,
				];
			}
		}
		return $tree;
	}


	/**
	 * @param ISortingEntity $entity
	 */
	public function setFirstSorting(ISortingEntity $entity)
	{
		$qb = $this->createQueryBuilder('s');
		$entities = $qb
			->orderBy('s.sorting', 'ASC')
			->getQuery()
			->setMaxResults(1)
			->getResult();
		/*** @var ISortingEntity $en */
		foreach ($entities as $en) {
			$entity->setSorting($en->getSorting() - 1 );
		}
	}


	/**
	 * @param ISortingEntity $entity
	 */
	public function setLastSorting(ISortingEntity $entity)
	{
		$qb = $this->createQueryBuilder('s');
		$entities = $qb
			->orderBy('s.sorting', 'DESC')
			->getQuery()
			->setMaxResults(1)
			->getResult();
		/*** @var ISortingEntity $en */
		foreach ($entities as $en) {
			$entity->setSorting($en->getSorting() + 1);
		}
	}

	/**
	 * @param ISortingEntity $entity
	 * @param int $lastSort
	 * @param bool $changeSet
	 * @throws \Exception
	 */
	public function setLastSortingChildren(ISortingEntity $entity, int &$lastSort, bool $changeSet = TRUE)
	{
		$children = $entity->getChildren();
		if (!empty($children)) {
			$criteria = new Criteria(NULL, ['sorting' => Criteria::ASC]);
			$children = $children->matching($criteria);
			/*** @var ISortingEntity $child */
			foreach ($children as $child) {
				$lvl = $this->getEntityLvl($child);
				$child->setLvl($lvl);

				$child->setSorting($lastSort);
				$lastSort++;
				$this->entityManager->persist($child);
				if ($changeSet) {
					$classMetadata = $this->entityManager->getClassMetadata(get_class($child));
					$this->entityManager->getUnitOfWork()->computeChangeSet($classMetadata, $child);
				}

				$this->setLastSortingChildren($child, $lastSort, $changeSet);
			}
		}
	}

	/**
	 * @param ISortingEntity $entity
	 * @throws \Doctrine\DBAL\DBALException
	 */
	public function setEntitySorting(ISortingEntity $entity)
	{
		$parent = $entity->getParent();
		if ($parent) {
			$this->setEntityParentSorting($entity, $parent->getSorting());
		} else {
			$this->setLastSorting($entity);
		}
	}

}
