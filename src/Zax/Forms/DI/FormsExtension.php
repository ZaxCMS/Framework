<?php

namespace Zax\Forms\DI;

use Nette\DI\CompilerExtension;
use Nette\Forms\Rules;
use Nette\PhpGenerator\ClassType;


class FormsExtension extends CompilerExtension {

	public function loadConfiguration() {
		$builder = $this->getContainerBuilder();
		$builder->addDefinition($this->prefix('formExtension'))
			->setclass('Zax\Forms\FormExtension');
	}

	public function afterCompile(ClassType $class) {
		parent::afterCompile($class);

		$config = $this->getConfig();
		$init = $class->methods['initialize'];
		$init->addBody(
			'$this->getService(\'Zax\Forms\FormExtension\')->register(?);',
			array_key_exists('messages', $config) ? $config['messages'] : []
		);
	}

}
