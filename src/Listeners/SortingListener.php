<?php

/**
 * Author: Radek Zíka
 * Email: radek.zika@dipcom.cz
 */

namespace Bajzany\SortingEntity\Listeners;

use Bajzany\SortingEntity\Entity\ISortingEntity;
use Bajzany\SortingEntity\Exceptions\SortingException;
use Bajzany\SortingEntity\Repository\SortingRepository;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Kdyby\Doctrine\Events;
use Kdyby\Events\Subscriber;

class SortingListener implements Subscriber
{

	/**
	 * Returns an array of events this subscriber wants to listen to.
	 *
	 * @return array
	 */
	public function getSubscribedEvents()
	{
		return [
			Events::prePersist,
			Events::onFlush
		];
	}

	/**
	 * @param OnFlushEventArgs $args
	 * @throws \Doctrine\DBAL\DBALException
	 * @throws SortingException
	 * @throws \Exception
	 */
	public function onFlush(OnFlushEventArgs $args)
	{
		$em = $args->getEntityManager();


		$uow = $em->getUnitOfWork();
		foreach ($uow->getScheduledEntityUpdates() as $keyEntity => $entity) {
			if ($entity instanceof ISortingEntity) {
				if ($entity->isMoved()) {
					return;
				}

				$repository = $em->getRepository(get_class($entity));
				if (!$repository instanceof SortingRepository) {
					throw SortingException::repositoryIsNotSortingRepository(get_class($repository));
				}

				foreach ($uow->getEntityChangeSet($entity) as $keyField => $field) {
					if ($keyField == 'parent') {

						/*** @var ISortingEntity $newParent */
						$newParent = $field[1];
						$repository->testIfCanSetParent($newParent, $entity);
						$lvl = $repository->getEntityLvl($entity);
						$entity->setLvl($lvl);

						if ($newParent instanceof ISortingEntity) {
							$repository->setEntityParentSorting($entity, $newParent->getSorting());
							$repository->updateChildren($entity);
						} else {
							$repository->setLastSorting($entity);
							$sort = $entity->getSorting() + 1;
							$repository->setLastSortingChildren($entity, $sort);
						}

					}
				}
			}
		}
	}

	/**
	 * @param LifecycleEventArgs $args
	 * @throws SortingException
	 * @throws \Doctrine\DBAL\DBALException
	 */
	public function prePersist(LifecycleEventArgs $args)
	{
		$entity = $args->getEntity();
		$em = $args->getEntityManager();
		if ($entity instanceof ISortingEntity) {
			$repository = $em->getRepository(get_class($entity));
			if (!$repository instanceof SortingRepository) {
				throw SortingException::repositoryIsNotSortingRepository(get_class($repository));
			}
			$lvl = $repository->getEntityLvl($entity);
			$repository->setEntitySorting($entity);
			$entity->setLvl($lvl);
		}
	}

}
