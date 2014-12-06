<?php

namespace ZaxCMS\Modules;

use Nette\Application\BadRequestException;
use Nette\DI\Container;
use Nette\InvalidStateException;
use Nette\Object;
use ZaxCMS\DI\CMSConfig;

class ModuleFactory extends Object {

	protected $config;

	protected $container;

	public function __construct(CMSConfig $config, Container $container) {
		$this->config = $config;
		$this->container = $container; // Use DI container for lazy loading
	}

	public function create($module) {
		if(!$this->config->isModuleEnabled($module)) {
			throw new BadRequestException("Module '$module' not found. Did you register it in 'cms' section of your configuration file?");
		}

		$factory = $this->container->getByType($this->config->getModuleFactoryClass($module));
		$instance = $factory->create();

		if(!$instance instanceof IModuleUI) {
			throw new InvalidStateException("Component returned by factory doesn't implement 'ZaxCMS\\Modules\\IModuleUI'.");
		}

		return $instance;
	}

}
