<?php
namespace Roadrunner\Model\Validator;


/**
 * Interface Validator
 * 
 * The Validator Interface for Validation patterns
 * 
 * @author matthias schmid
 *
 */
interface Validator 
{
	/**
	 * Validates the $input
	 * 
	 * @param array $input
	 * @param string $type
	 * @return array
	 */
	public function validate($input, $type);
}