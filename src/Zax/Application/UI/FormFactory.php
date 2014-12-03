<?php

namespace Zax\Application\UI;
use Zax,
	Nette,
	Nette\Application\UI as NetteUI,
	Kdyby;

/**
 * Class FormFactory
 *
 * Default Form factory
 *
 * @package Zax\Application\UI
 */
class FormFactory extends Nette\Object implements IFormFactory {

	protected $defaultClass = 'Zax\Application\UI\Form';

	/** @var Kdyby\Translation\Translator */
	protected $translator;

	/** @var Zax\Html\Icons\IIcons */
	protected $icons;

	public function __construct(Zax\Html\Icons\IIcons $icons, Nette\Localization\ITranslator $translator = NULL) {
		$this->icons = $icons;
		$this->translator = $translator;
	}

	public function setDefaultClass($class) {
		$this->defaultClass = $class;
		return $this;
	}

	/**
	 * Translated form factory
	 *
	 * @return Zax\Application\UI\Form|Nette\Forms\Form
	 * @throws Nette\InvalidArgumentException
	 */
	public function create() {
		$f = new $this->defaultClass;

		if(!$f instanceof Nette\Forms\Form) {
			throw new Nette\InvalidArgumentException("Class '$class' is not a valid Nette form.");
		}

		if($this->translator !== NULL) {
			$f->setTranslator($this->translator);
		}

		if($f instanceof Zax\Application\UI\Form) {
			$f->setIcons($this->icons);
		}

		return $f;
	}

}
