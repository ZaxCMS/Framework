<?php

namespace Zax\Model\Doctrine;
use Zax,
	Kdyby,
	Gedmo\Tree\Entity\Repository,
	Nette;

/**
 * @method Repository\AbstractTreeRepository getRepository()
 * @method array|string childrenHierarchy($node = NULL, $direct = FALSE, array $options = [], $includeNode = FALSE)
 * @method array|string buildTree(array $nodes, array $options = [])
 * @method array buildTreeArray(array $nodes)
 * @method setChildrenIndex($childrenIndex)
 * @method getChildrenIndex()
 * @method array getRootNodes($sortByField = NULL, $direction = 'asc')
 * @method array getNodesHierarchy($node = NULL, $direct = FALSE, array $options = [], $includeNode = FALSE)
 * @method array getChildren($node = NULL, $direct = FALSE, $sortByField = NULL, $direction = 'ASC', $includeNode = FALSE)
 * @method int childCount($node = NULL, $direct = FALSE)
 */
abstract class GedmoTreeService extends DoctrineService {

}
