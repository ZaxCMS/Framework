<?php

namespace Zax\Model;
use Zax;

/**
 * Defines methods for working with entities in model.
 */
interface IEntityService {

	public function get($id);

	public function findAll($orderBy = NULL, $limit = NULL, $offset = NULL);

	public function findBy($criteria, $orderBy = NULL, $limit = NULL, $offset = NULL);

	public function getBy($criteria, $orderBy = NULL);

	public function countBy($criteria = []);

	public function findPairs($key = NULL, $value = NULL, $criteria = [], $orderBy = []);

	public function persist($entity);

	public function remove($entity);

	public function flush();

	public function refresh($entity);

	public function create();

}
