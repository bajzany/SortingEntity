<?php

/**
 * Author: Radek Zíka
 * Email: radek.zika@dipcom.cz
 */

namespace Bajzany\SortingEntity\Exceptions;

use Bajzany\SortingEntity\Repository\SortingRepository;

class SortingException extends \Exception
{

	/**
	 * @return SortingException
	 */
	public static function parentValidationException()
	{
		return new self("Target can't be set into Parent, circular problem.");
	}

	/**
	 * @param string $repositoryClass
	 * @return SortingException
	 */
	public static function repositoryIsNotSortingRepository(string $repositoryClass)
	{
		return new self("Repository '{$repositoryClass}' is not " . SortingRepository::class);
	}

}
