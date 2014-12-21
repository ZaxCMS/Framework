<?php

namespace Zax\Latte;
use Zax,
	Latte,
	Nette;

class AjaxMacro extends Nette\Object {

	public function install(Latte\Engine $latte) {
		$set = new Latte\Macros\MacroSet($latte->getCompiler());
		$set->addMacro('ajax', [$this, 'macroAjax'], '}');
	}

	public function macroAjax(Latte\MacroNode $node, Latte\PhpWriter $writer) {
		return $writer->write('echo Latte\Runtime\Filters::htmlAttributes(["data-zax-ajax" => TRUE])');
	}

}
