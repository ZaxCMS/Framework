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
		if($node->prefix === $node::PREFIX_TAG) {
			return $writer->write('if ($ajaxEnabled) {echo \' data-zax-ajax="true" \';}');
		}
	}

}
