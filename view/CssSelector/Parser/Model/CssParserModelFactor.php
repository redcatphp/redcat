<?php
namespace surikat\view\CssSelector\Parser\Model;
class CssParserModelFactor{
	const DESCENDANT_OPERATOR = "";
	const CHILD_OPERATOR = ">";
	const ADJACENT_OPERATOR = "+";
	private $_combinator;
	private $_element;
	public function __construct($combinator, $element){
		$this->_combinator = $combinator;
		$this->_element = $element;
	}
	public function getElement(){
		return $this->_element;
	}
	public function filter($node){
		$ret = [];
		$items = $this->_combinator->filter($node, $this->_element->getTagName());
		foreach ($items as $item)
			if ($this->_element->match($item))
				array_push($ret, $item);
		$filters = $this->_element->getFilters();
		foreach ($filters as $filter) {
			$items = [];
			foreach ($ret as $i => $item)
				if ($filter->match($item, $i, $ret))
					$items[] = $item;
			$ret = $items;
		}
		return $ret;
	}
}