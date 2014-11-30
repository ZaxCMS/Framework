<?php

namespace Zax\Application\Routers\NiceUrl;
use Zax,
	Zax\Application\Routers\Route,
	Zax\Application\Routers\RouteList,
	Nette;
/* TODO */
class Helpers extends Nette\Object {

	/**
	 * Search for strings inside '<' and '>'
	 *
	 * @param $name
	 * @return mixed
	 */
	static protected function findTokens($name) {
		$found = Nette\Utils\Strings::match($name, '~(\<[a-z-]\>)+~i');
		if($found) {
			return array_map(function ($str) {return substr($str, 1, -1);}, $found);
		}
		return FALSE;
	}

	/**
	 * Parse name containing variable tokens and return fully qualified name.
	 *
	 * @param array $tokens
	 * @param array $params
	 * @param array $aliases
	 * @param string $fullName
	 * @return mixed
	 */
	static protected function createFullName($tokens, $params, $aliases, $fullName) {
		foreach($tokens as $token) {
			if(isset($params[$token])) {
				$fullName = str_replace($token, $params[$token], $fullName);
			} else if(isset($aliases[$token2]) && isset($params[$aliases[$token]])) {
				$fullName = str_replace($token, $params[$aliases[$token]], $fullName);
			}
		}
		return $fullName;
	}

	static public function createAliases($aliases = [], $doAliases = []) {
		return [
			Route::FILTER_IN => function($params) use ($aliases, $doAliases) { // URL 2 Request

				// Convert 'do' parameter into its fully qualified name
				if(isset($params['do'])) {
					foreach($doAliases as $doAlias => $doFullName) {
						$doTokens = self::findTokens($doFullName);
						if($doTokens) {
							$doFullName = self::createFullName($doTokens, $params, $aliases, $doFullName);
						}
						if($params['do'] === $doAlias) {
							$params['do'] = $doFullName;
						}
					}
				}

				// Convert aliases into their fully qualified names
				foreach($aliases as $alias => $fullName) {
					if(isset($params[$alias])) {
						$tokens = self::findTokens($fullName);
						if($tokens) {
							$fullName = self::createFullName($tokens, $params, $aliases, $fullName);
						}
						$params[$fullName] = $params[$alias];
						unset($params[$alias]);
					}
				}

				return $params;
			},
			Route::FILTER_OUT => function($params) use ($aliases, $doAliases) { // Request 2 URL

				// Convert 'do' parameter into its alias
				if(isset($params['do'])) {
					foreach($doAliases as $doAlias => $doFullName) {
						$doTokens = self::findTokens($doFullName);
						if($doTokens) {
							$doFullName = self::createFullName($doTokens, $params, $aliases, $doFullName);
						}
						if($params['do'] === $doFullName) {
							$params['do'] = $doAlias;
						}
					}
				}

				// Convert parameters into their aliases
				foreach($aliases as $alias => $fullName) {
					$tokens = self::findTokens($fullName);
					if($tokens) {
						$fullName = self::createFullName($tokens, $params, $aliases, $fullName);
					}
					if(isset($params[$fullName])) {
						$params[$alias] = $params[$fullName];
						unset($params[$fullName]);
					}
				}

				return $params;
			}
		];
	}

	static public function combineAliases($metadata, $aliases = [], $doAliases = []) {
		$newMetadata = self::createAliases($aliases, $doAliases);
		foreach([Route::FILTER_IN, Route::FILTER_OUT] as $filter) {
			$oldCb = $metadata[NULL][$filter];
			$metadata[NULL][$filter] = function($params) use ($filter, $oldCb, $newMetadata) {
				$newCb = $newMetadata[$filter];
				return $oldCb($newCb($params));
			};
		}
		return $metadata;
	}

	static public function createMetadata($presenter, $action = NULL, $aliases = array(), $persistentBoolParams = array()) {

		if(is_array($presenter)) {
			$metadata = $presenter;
			list(,$aliases, $persistentBoolParams) = func_get_args();
		} else {
			$metadata = [
				'presenter' => $presenter
			];

			if(strlen($action) > 0) {
				$metadata['action'] = $action;
			}
		}


		if(count($aliases) > 0) {
			$metadata[NULL] = Route::createAliases($aliases);
		}

		if(count($persistentBoolParams) > 0) {
			foreach($persistentBoolParams as $param) {
				$metadata[$param] = [
					Route::FILTER_IN => function() {
						return TRUE;
					},
					Route::FILTER_OUT => function() use ($param) {
						return $param;
					}
				];
			}
		}

		return $metadata;
	}

}