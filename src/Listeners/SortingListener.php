<?php

/**
 * Author: Radek Zíka
 * Email: radek.zika@dipcom.cz
 */

namespace SortingEntity\Listeners;

use Doctrine\ORM\Event\OnFlushEventArgs;
use Kdyby\Doctrine\Events;
use Kdyby\Events\Subscriber;
use SortingEntity\Entity\ISortingEntity;

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
			Events::onFlush
		];
	}

	/**
	 * @param OnFlushEventArgs $args
	 */
	public function onFlush(OnFlushEventArgs  $args)
	{
		$em = $args->getEntityManager();
		$uow = $em->getUnitOfWork();
		foreach ($uow->getScheduledEntityUpdates() as $keyEntity => $entity) {
			if ($entity instanceof ISortingEntity) {
				barDump($entity);
			}
		}

		exit;
	}
}
