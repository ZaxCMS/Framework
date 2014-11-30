<?php

namespace Zax\Application\Routers\NiceUrl;
use Zax,
	Zax\Application\Routers\Route,
	Zax\Application\Routers\RouteList,
	Nette;
/* TODO */
class NiceUrlPart extends Nette\ComponentModel\Container {

	const   TYPE_CONTROL = 0,
			TYPE_SCALAR = 1,
			TYPE_BOOLEAN = 2,
			TYPE_SLUG = 3;

	protected $type;

	protected $default;

	protected $filterTable = [];

	protected $prefix = '';

	public function __construct(Nette\ComponentModel\IContainer $parent = NULL, $name = NULL) {
		parent::__construct($parent, $name);

		$this->monitor('Zax\Application\Routers\NiceUrl\NiceUrl');
	}

	public function attached($niceUrl) {
		parent::attached($niceUrl);
		$this->prefix = $this->lookupPath('Zax\Application\Routers\NiceUrl\NiceUrl');
	}

	public function setType($type) {
		$this->type = $type;
		return $this;
	}

	public function getType($type) {
		return $this->type;
	}

	public function getFilterTable() {
		return $this->filterTable;
	}

	public function setDefault($default) {
		$this->default = $default;
		return $this;
	}

	public function setFilterTable($filterTable) {
		$this->filterTable = $filterTable;
		return $this;
	}

	public function addUrlPart($name, $type) {
		return $this[$name] = (new NiceUrlPart($this, $name))->setType($type);
	}

	public function addControl($name) {
		return $this->addUrlPart($name, self::TYPE_CONTROL);
	}

	public function addScalar($name) {
		return $this->addUrlPart($name, self::TYPE_SCALAR);
	}

	public function addBoolean($name) {
		return $this->addUrlPart($name, self::TYPE_BOOLEAN);
	}

	public function addSlug($name) {
		return $this->addUrlPart($name, self::TYPE_SLUG);
	}

	public function getFullName() {
		return $this->prefix . '-' . $this->name;
	}

	public function getDefinition() {
		return [
			Route::FILTER_TABLE => $this->filterTable,
			Route::VALUE => $this->default
		];
	}

}
