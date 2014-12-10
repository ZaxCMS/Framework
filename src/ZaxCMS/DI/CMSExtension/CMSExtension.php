<?php

namespace ZaxCMS\DI;

use ZaxCMS;
use Zax\DI\ConfigurableExtension;

/** Enables core of ZaxCMS
 *
 * @configClass ZaxCMS\DI\CMSConfig
 */
class CMSExtension extends ConfigurableExtension {

	const MODULE_NAME_TAG = 'zaxcms.module';

	public function loadConfiguration() {
		parent::loadConfiguration();

		$config = $this->getConfig($this->getDefaultConfig());
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('moduleFactory'))
			->setClass($config['moduleFactory']);

		if(!is_array($config['modules'])) {
			return;
		}
		
		foreach($config['modules'] as $name => $factory) {
			$builder->addDefinition($this->prefix(md5('module' . $name . $factory)))
				->setImplement($factory)
				->addTag(self::MODULE_NAME_TAG, $name);
		}
	}

	public function beforeCompile() {
		$config = $this->getConfig($this->getDefaultConfig());
		$builder = $this->getContainerBuilder();

		$moduleManager = $builder->getDefinition($this->prefix('moduleFactory'));
		foreach($builder->findByTag(self::MODULE_NAME_TAG) as $serviceName => $moduleName) {
			$moduleFactory = $builder->getDefinition($serviceName);
			/** @var \Nette\DI\ServiceDefinition $moduleFactory */
			$moduleManager->addSetup('addAvailableModule', [$moduleName, $moduleFactory->getImplement()]);
		}
	}

}
