<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 */

namespace Bajzany\SortingEntity\Entity;

trait Sortable
{

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="`parent_id`", type="integer", options={"default": null}, nullable=true)
	 */
	private $parentId;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="`sorting`", type="integer", options={"default": 0}, nullable=false)
	 */
	private $sorting = 0;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="`lvl`", type="integer", options={"default": 0}, nullable=false)
	 */
	private $lvl;

	/**
	 * @var bool
	 */
	private $moved = FALSE;

	/**
	 * @return int
	 */
	public function getSorting(): int
	{
		return $this->sorting;
	}

	/**
	 * @param int $sorting
	 * @return $this
	 */
	public function setSorting(int $sorting)
	{
		$this->sorting = $sorting;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getLvl(): int
	{
		return $this->lvl;
	}

	/**
	 * @param int $lvl
	 * @return $this
	 */
	public function setLvl(int $lvl)
	{
		$this->lvl = $lvl;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isMoved(): bool
	{
		return $this->moved;
	}

	/**
	 * @param bool $moved
	 * @return $this
	 */
	public function setMoved(bool $moved)
	{
		$this->moved = $moved;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getParentId(): ?int
	{
		return $this->parentId;
	}

}
