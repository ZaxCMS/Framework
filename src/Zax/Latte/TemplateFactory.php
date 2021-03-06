<?php

namespace Zax\Latte;
use Nette,
	Zax;

class TemplateFactory extends Nette\Bridges\ApplicationLatte\TemplateFactory {

	private $icons;

	private $translator;

	public function __construct(Nette\Bridges\ApplicationLatte\ILatteFactory $latteFactory,
								Nette\Http\IRequest $httpRequest,
								Nette\Http\IResponse $httpResponse,
								Nette\Security\User $user,
								Nette\Caching\IStorage $cacheStorage,
								Zax\Html\Icons\IIcons $icons,
								Nette\Localization\ITranslator $translator = NULL) {
		parent::__construct($latteFactory, $httpRequest, $httpResponse, $user, $cacheStorage);
		$this->icons = $icons;
		$this->translator = $translator;
	}

	public function createTemplateHelpers(Nette\Localization\ITranslator $translator = NULL) {
		$helpers = new Zax\Latte\Helpers;
		if($translator !== NULL) {
			$helpers->setTranslator($translator);
		}
		return $helpers;
	}

	public function createTemplate(Nette\Application\UI\Control $control) {
		$template = parent::createTemplate($control);

		$translator = $this->translator;

		if($control instanceof Zax\Application\UI\Control || $control instanceof Zax\Application\UI\Presenter) {
			$template->setTranslator($translator = $control->getTranslator());

			// TODO: interface and uncomment?
			//$template->currentLocale = $control->getLocale();
			//$template->availableLocales = $control->getAvailableLocales();
		}

		if($control instanceof Zax\Application\UI\Control) {
			$template->ajaxEnabled = $control->isAjaxEnabled();
			$template->view = $control->view;
			(new AjaxMacro)->install($template->getLatte());
		}

		$helpers = $this->createTemplateHelpers($translator);
		$template->getLatte()->addFilter(NULL, [$helpers, 'loader']);

		$template->icons = $this->icons;
		(new Zax\Html\Icons\LatteIcons($this->icons))->install($template->getLatte());

		return $template;
	}

}

