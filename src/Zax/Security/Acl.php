<?php

namespace Zax\Security;
use Zax,
	Nette,
	Nette\Security\IAuthorizator;

/**
 * Decorator
 */
class Acl extends Nette\Object implements IAuthorizator {

	public $onUndefinedResource = [];

	public $onUndefinedRole = [];

	/** @var Nette\Security\Permission|IAuthorizator */
	protected $acl;

	public function __construct(IAuthorizator $acl) {
		$this->acl = $acl;
	}

	public function isAllowed($role = IAuthorizator::ALL, $resource = IAuthorizator::ALL, $privilege = IAuthorizator::ALL) {
		if(!$this->acl->hasRole($role)) {
			$this->onUndefinedRole($role);
		}
		if(!$this->acl->hasResource($resource)) {
			$this->onUndefinedResource($resource);
		}

		return $this->acl->isAllowed($role, $resource, $privilege);
	}

	public function __call($name, $args = []) {
		if(method_exists($this->acl, $name)) {
			return call_user_func_array([$this->acl, $name], $args);
		}
		return parent::__call($name, $args);
	}

}
