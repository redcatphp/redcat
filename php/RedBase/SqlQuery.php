<?php
namespace RedBase;
abstract class SqlQuery{
	protected $pdo;
	protected $frozen;
	protected $primaryKey;
	function __construct($pdo,$primaryKey='id',$frozen=null){
		$this->pdo = $pdo;
		$this->primaryKey = $primaryKey;
		$this->frozen = $frozen;
	}
	abstract function createRow($obj);
	abstract function readRow($id);
	abstract function updateRow($obj,$id=null);
	abstract function deleteRow($id);
}