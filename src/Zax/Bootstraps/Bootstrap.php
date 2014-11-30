<?php


namespace Zax\Bootstraps;
use Nette,
	Zax;

/**
 * Class Bootstrap
 *
 * Simple bootstrap implementation
 *
 * @package Zax\Bootstraps
 */
class Bootstrap implements IBootstrap {

	protected $appDir;

	protected $rootDir;

	protected $tempDir;

	protected $debuggers = [];

	protected $debugEmails = [];

	protected $debug = FALSE;

	protected $loaderPaths = [];

	protected $configs = [];

	protected $autoloadConfig = FALSE;

	protected $defaultTimezone = 'Europe/Prague';

	protected $isDebugger;

	protected $errorPresenter;

	protected $enableLog = TRUE;

	protected $catchExceptions = TRUE;

	protected $errorPresenters;

	/**
	 * @param $appDir
	 * @param $rootDir
	 * @param $tempDir
	 */
	public function __construct($appDir, $rootDir, $tempDir) {
		$this->appDir = (string)$appDir;
		$this->rootDir = (string)$rootDir;
		$this->tempDir = (string)$tempDir;
	}

	/** Debugger by IP
	 *
	 * @param $ip
	 * @return $this
	 */
	public function addDebugger($ip) {
		$this->debuggers[] = $ip;
		return $this;
	}

	/** Debugger by e-mail
	 *
	 * @param $email
	 * @return $this
	 */
	public function addDebuggerEmail($email) {
		$this->debugEmails[] = $email;
		return $this;
	}

	/** Add a .neon config file
	 *
	 * @param $config
	 * @return $this
	 */
	public function addConfig($config) {
		$this->configs[] = $config;
		return $this;
	}

	/** Scan app dir for all neon files containing 'autoload: [TRUE]'
	 *
	 * @param bool $autoload
	 * @return $this
	 */
	public function enableConfigAutoload($autoload = TRUE) {
		$this->autoloadConfig = $autoload;
		return $this;
	}

	/** Debuggers by IP and e-mail
	 *
	 * @param $ips
	 * @param $emails
	 * @return $this
	 */
	public function setDebuggers($ips, $emails) {
		foreach($ips as $ip) {
			$this->addDebugger($ip);
		}
		foreach($emails as $email) {
			$this->addDebuggerEmail($email);
		}
		return $this;
	}

	/**
	 * @param bool $enableTracy
	 * @param bool $catchExceptions
	 * @param bool $enableLog - log errors and send e-mails
	 * @return $this
	 */
	public function enableDebugger($enableTracy = TRUE, $catchExceptions = FALSE, $enableLog = TRUE) {
		$this->debug = $enableTracy;
		$this->catchExceptions = $catchExceptions;
		$this->enableLog = $enableLog;
		return $this;
	}

	/** Add a path for RobotLoader to sniff in
	 *
	 * @param $path
	 * @return $this
	 */
	public function addLoaderPath($path) {
		$this->loaderPaths[] = $path;
		return $this;
	}

	/**
	 * @param $timezone
	 * @return $this
	 */
	public function setDefaultTimezone($timezone) {
		$this->defaultTimezone = $timezone;
		return $this;
	}

	/**
	 * @param $errorPresenter
	 * @return $this
	 */
	public function setErrorPresenter($errorPresenter) {
		$this->errorPresenter = $errorPresenter;
		return $this;
	}

	public function setErrorPresenters($presenter, $control) {
		$this->errorPresenters = [
			'presenter' => $presenter,
			'control' => $control
		];
		return $this;
	}

	/** Does IP match any debugger's IP?
	 *
	 * @return bool
	 */
	protected function isDebugger() {
		if($this->isDebugger === NULL) {
			return $this->isDebugger = isset($_SERVER['REMOTE_ADDR']) && in_array($_SERVER['REMOTE_ADDR'], $this->debuggers);
		} else {
			return $this->isDebugger;
		}
	}

	/**
	 * @param Nette\Configurator $configurator
	 */
	protected function loadConfigFiles(Nette\Configurator $configurator) {
		if($this->autoloadConfig === TRUE || is_array($this->autoloadConfig)) {
			$scanDirs = $this->autoloadConfig === TRUE ? [$this->appDir] : $this->autoloadConfig;
			$cacheJournal = new Nette\Caching\Storages\FileJournal($this->tempDir);
			$cacheStorage = new Nette\Caching\Storages\FileStorage($this->tempDir . '/cache', $cacheJournal);
			$cache = new Nette\Caching\Cache($cacheStorage, 'configFiles');
			$files = $cache->load('configFiles');
			if($files === NULL) {
				$files = [0 => []];
				foreach(Nette\Utils\Finder::findFiles('*.neon')->from($scanDirs) as $path => $file) {
					$content = Nette\Neon\Neon::decode(file_get_contents($path));
					if(!array_key_exists('autoload', $content)) {
						continue;
					}
					$autoload = Nette\Utils\Arrays::get($content, ['autoload', 0], FALSE);
					if($autoload === FALSE) {
						continue;
					}
					$autoload = is_int($autoload) ? $autoload : 0;
					if(!isset($files[$autoload])) {
						$files[$autoload] = [];
					}
					$files[$autoload][] = $path;
				}
				$cache->save('configFiles', $files);
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

	/** Create and setup Nette\Configurator
	 *
	 * @return Nette\Configurator
	 */
	protected function setUpConfigurator() {
		$configurator = new Nette\Configurator;

		// Enable "autoload" section in config files
		$configurator->defaultExtensions['autoload'] = 'Zax\DI\CompilerExtensions\EmptyExtension';

		// Fix incorrectly initialized appDir
		$configurator->addParameters(['appDir' => $this->appDir]);

		// Add root dir
		$configurator->addParameters(['rootDir' => $this->rootDir]);

		// Add temp dir
		$configurator->addParameters(['tempDir' => $this->tempDir]);

		$configurator->setTempDirectory($this->tempDir);

		if($this->debug && $this->isDebugger()) {
			$configurator->setDebugMode($this->debuggers);
		} else {
			$configurator->setDebugMode(FALSE);
		}

		if($this->enableLog) {
			$configurator->enableDebugger($this->tempDir . '/log', $this->debugEmails);
		}

		// Load app
		$loader = $configurator->createRobotLoader()
			->addDirectory($this->appDir);

		// Load additional paths specified in index.php
		foreach($this->loaderPaths as $path) {
			$loader->addDirectory($path);
		}
		$loader->register();

		$this->loadConfigFiles($configurator);

		return $configurator;
	}

	/** Set up the environment and return DI container
	 *
	 * @return Nette\DI\Container
	 */
	public function setUp() {
		date_default_timezone_set($this->defaultTimezone);

		$configurator = $this->setUpConfigurator();

		/** @var Nette\DI\Container $container */
		$container = $configurator->createContainer();

		return $container;
	}

	/**
	 * Runs the application
	 */
	public function boot() {

		$container = $this->setUp();

		$app = $container->getByType('Nette\Application\Application');

		$app->catchExceptions = $this->catchExceptions;

		if($this->errorPresenter !== NULL) {
			$app->errorPresenter = $this->errorPresenter;
		}/* else if($this->errorPresenters !== NULL) { // old hack, might need it again though
			$app->errorPresenters = $this->errorPresenters;
		}*/

		$app->run();
	}


}