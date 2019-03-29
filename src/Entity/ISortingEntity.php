<?php

/**
 * Author: Radek Zíka
 * Email: radek.zika@dipcom.cz
 */

namespace Bajzany\SortingEntity\Entity;

interface ISortingEntity
{

	/**
	 * @return int
	 */
	public function getId();

	/**
	 * @return int
	 */
	public function getSorting(): int;

	/**
	 * @return int
	 */
	public function getLvl(): int;

	/**
	 * @return ISortingEntity|null
	 */
	public function getParent(): ?ISortingEntity;

}
