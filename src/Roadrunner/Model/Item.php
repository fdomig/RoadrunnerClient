<?php
namespace Roadrunner\Model;

/**
 * @Document(indexed=true)
 */
class Item {
	
	/** @Id @Field */
	public $code;
	
	/** @ReferenceOne(targetDocument="Container") */
	public $container;
	
	public function setCode($code) {
		$this->code = $code;
	}
	
}