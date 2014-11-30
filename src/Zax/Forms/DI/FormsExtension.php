<?php

namespace Zax\Forms\DI;

use Nette\DI\CompilerExtension;
use Nette\Forms\Rules;
use Nette\PhpGenerator\ClassType;

/**
 * This extension registers some additional form controls and also adds the ability to customize
 * default error messages on custom validators.
 */
class FormsExtension extends CompilerExtension {

	public function loadConfiguration() {
		$builder = $this->getContainerBuilder();
		$builder->addDefinition($this->prefix('formExtension'))
			->setClass('Zax\Forms\FormExtension');
	}

	public function afterCompile(ClassType $class) {
		parent::afterCompile($class);

		$config = $this->getConfig();
		$init = $class->methods['initialize'];
		$init->addBody(
			'$this->getByType(\'Zax\Forms\FormExtension\')->register(?);',
			array_key_exists('messages', $config) ? [$config['messages']] : [[]]
		);
	}

}
