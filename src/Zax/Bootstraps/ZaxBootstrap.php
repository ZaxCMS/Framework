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

	/**
	 * @param bool|array $enable TRUE/FALSE or array of dirs to be scanned (TRUE = scan app dir)
	 * @return $this
	 */
	public function enableConfigAutoload($enable = TRUE) {
		$this->autoloadConfig = $enable;
		return $this;
	}

	/**
	 * @return Cache
	 */
	protected function createCache() {
		$cacheJournal = new Storages\FileJournal($this->tempDir);
		$cacheStorage = new Storages\FileStorage($this->tempDir . '/cache', $cacheJournal);
		return new Cache($cacheStorage, self::CACHE_NAMESPACE);
	}

	/**
	 * @param Configurator $configurator
	 */
	protected function loadConfigFiles(Configurator $configurator) {
		if($this->autoloadConfig === TRUE || is_array($this->autoloadConfig)) {
			$scanDirs = $this->autoloadConfig === TRUE ? [$this->appDir] : $this->autoloadConfig;
			$cache = $this->createCache();
			$files = $cache->load(self::CACHE_NAMESPACE);
			if($files === NULL) {
				$files = [0 => []];
				foreach(Finder::findFiles('*.neon')->from($scanDirs) as $path => $file) {
					$content = Neon::decode(file_get_contents($path));
					if(!is_array($content) || !array_key_exists('autoload', $content)) {
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