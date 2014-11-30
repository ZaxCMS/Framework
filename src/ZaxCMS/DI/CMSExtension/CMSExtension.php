<?php

namespace ZaxCMS\DI;

use ZaxCMS;
use Zax\DI\ConfigurableExtension;

/** Enables core of ZaxCMS
 *
 * @configClass ZaxCMS\DI\CMSConfig
 */
class CMSExtension extends ConfigurableExtension {

	public function loadConfiguration() {
		parent::loadConfiguration();

		$config = $this->getConfig($this->getDefaultConfig());

		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('moduleFactory'))
			->setClass('ZaxCMS\Modules\ModuleFactory');

		if(!is_array($config['modules'])) {
			return;
		}
		
		foreach($config['modules'] as $name => $factory) {
			$builder->addDefinition($this->prefix(md5('module' . $name . $factory)))
				->setImplement($factory);
		}
	}

}
