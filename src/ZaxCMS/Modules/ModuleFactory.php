<?php

namespace ZaxCMS\Modules;

use Nette\Application\BadRequestException;
use Nette\DI\Container;
use Nette\InvalidStateException;
use Nette\Object;
use ZaxCMS\DI\CMSConfig;

interface IModuleUIFactory {

	/**
	 * @param string $name
	 * @param string $factoryClass
	 */
	public function addAvailableModule($name, $factoryClass);

	/**
	 * @param string $module
	 * @return IModuleUI
	 */
	public function create($module);

}

class ModuleUIFactory extends Object implements IModuleUIFactory {

	protected $container;

	protected $availableModules = [];

	public function __construct(Container $container) {
		$this->container = $container; // Use DI container for lazy loading
	}

	public function addAvailableModule($name, $factoryClass) {
		$this->availableModules[$name] = $factoryClass;
		return $this;
	}

	public function create($module) {
		if(!array_key_exists($module, $this->availableModules)) {
			throw new BadRequestException("Module '$module' not found. Did you register it in 'cms' section of your configuration file?");
		}

		$factory = $this->container->getByType($this->availableModules[$name]);
		$instance = $factory->create();

		if(!$instance instanceof IModuleUI) {
			throw new InvalidStateException("Object returned by factory doesn't implement 'ZaxCMS\\Modules\\IModuleUI'.");
		}

		return $instance;
	}

}
