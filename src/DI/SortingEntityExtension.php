<?php

/**
 * Author: Radek Zíka
 * Email: radek.zika@dipcom.cz
 */

namespace Bajzany\SortingEntity\DI;

use Bajzany\SortingEntity\Listeners\SortingListener;
use Nette\Configurator;
use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;

class SortingEntityExtension extends CompilerExtension
{

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('sortingListener'))
			->addTag('kdyby.subscriber')
			->setFactory(SortingListener::class)
			->setInject(TRUE);
	}

	/**
	 * @param Configurator $configurator
	 */
	public static function register(Configurator $configurator)
	{
		$configurator->onCompile[] = function ($config, Compiler $compiler) {
			$compiler->addExtension('sortingEntity', new SortingEntityExtension());
		};
	}

}
