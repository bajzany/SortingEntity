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
	 * @return string
	 */
	public function getName(): string;

	/**
	 * @param int $sorting
	 * @return int
	 */
	public function setSorting(int $sorting);

	/**
	 * @return int
	 */
	public function getLvl(): int;

	/**
	 * @param int $lvl
	 */
	public function setLvl(int $lvl);

	/**
	 * @return ISortingEntity|null
	 */
	public function getParent(): ?ISortingEntity;

	/**
	 * @param ISortingEntity|null $parent
	 * @return ISortingEntity|null
	 */
	public function setParent(?ISortingEntity $parent);

	/**
	 * @return ISortingEntity[]
	 */
	public function getChildren();

	/**
	 * @return bool
	 */
	public function isMoved(): bool;

	/**
	 * @param bool $moved
	 * @return mixed
	 */
	public function setMoved(bool $moved);

}
