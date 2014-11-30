<?php


namespace Zax\Bootstraps;

use Nette\Configurator;
use Nette\Caching\Storages;
use Nette\Caching\Cache;
use Nette\Utils\Finder;
use Nette\Neon\Neon;
use Nette\Utils\Arrays;

/**
 * Bootstrap with config autoloading support
 */
class ZaxBootstrap extends AbstractBootstrap {

	const CACHE_NAMESPACE = 'configFiles';

	protected $autoloadConfig = FALSE;

	public function enableConfigAutoload($enable = TRUE) {
		$this->autoloadConfig = (bool) $enable;
		return $this;
	}

	/**
	 * @param Configurator $configurator
	 */
	protected function loadConfigFiles(Configurator $configurator) {
		if($this->autoloadConfig === TRUE || is_array($this->autoloadConfig)) {
			$scanDirs = $this->autoloadConfig === TRUE ? [$this->appDir] : $this->autoloadConfig;
			$cacheJournal = new Storages\FileJournal($this->tempDir);
			$cacheStorage = new Storages\FileStorage($this->tempDir . '/cache', $cacheJournal);
			$cache = new Cache($cacheStorage, self::CACHE_NAMESPACE);
			$files = $cache->load(self::CACHE_NAMESPACE);
			if($files === NULL) {
				$files = [0 => []];
				foreach(Finder::findFiles('*.neon')->from($scanDirs) as $path => $file) {
					$content = Neon::decode(file_get_contents($path));
					if(!array_key_exists('autoload', $content)) {
						continue;
					}
					$autoload = Arrays::get($content, ['autoload', 0], FALSE);
					if($autoload === FALSE) {
						continue;
					}
					$autoload = is_int($autoload) ? $autoload : 0;
					if(!isset($files[$autoload])) {
						$files[$autoload] = [];
					}
					$files[$autoload][] = $path;
				}
				$cache->save(self::CACHE_NAMESPACE, $files);
			}
			foreach($files as $priorityFiles) {
				foreach($priorityFiles as $config) {
					$configurator->addConfig($config);
				}
			}
		}
		foreach($this->configs as $config) {
			$configurator->addConfig($config);
		}
	}

	/**
	 * @param Configurator $configurator
	 */
	protected function setUpConfigurator(Configurator $configurator) {
		$configurator->defaultExtensions['autoload'] = 'Zax\DI\CompilerExtensions\EmptyExtension';
		$this->loadConfigFiles($configurator);
	}


}