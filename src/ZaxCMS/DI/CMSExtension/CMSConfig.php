<?php

namespace ZaxCMS\DI;

use ZaxCMS;
use Zax\DI\ExtensionConfig;

class CMSConfig extends ExtensionConfig {

	public function getEnabledModules() {
		return array_keys($this->config['modules']);
	}

	public function isModuleEnabled($name) {
		return in_array($name, $this->getEnabledModules());
	}

	public function getModuleFactoryClass($name) {
		return $this->config['modules'][$name];
	}

}