<?php
namespace RedBase;
class Table implements \ArrayAccess{
	private $name;
	private $primaryKey;
	private $dataSource;
	private $data = [];
	function __construct($name,$primaryKey='id',DataSourceInterface $dataSource){
		$this->name = $name;
		$this->primaryKey = $primaryKey;
		$this->dataSource = $dataSource;
	}
	function getPrimaryKey(){
		return $this->primaryKey;
	}
	function offsetExists($id){
		return (bool)$this->offsetGet($id);
	}
	function offsetGet($id){
		if(!array_key_exists($id,$this->data[$id]))
			$this->data[$id] = $this->readRow($id);
		return $this->data[$id];
	}
	function offsetSet($id,$obj){
		if(!$id)
			$id = $this->createRow($obj);
		else
			$this->updateRow($obj,$id);
		$this->data[$id] = $obj;
	}
	function offsetUnset($id){
		$this->deleteRow($id);
	}
	function createRow($obj){
		return $this->dataSource->createRow($obj);
	}
	function readRow($id){
		return $this->dataSource->readRow($id);
	}
	function updateRow($obj,$id=null){
		return $this->dataSource->updateRow($obj,$id);
	}
	function deleteRow($id){
		return $this->dataSource->deleteRow($id);
	}
}