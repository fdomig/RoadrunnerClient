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
	 * @param array $input (<input-name> => array( 'name' => <validator-name>, 'value' => <value> )
	 * @return array 
	 */
	public function validate(array $input);
}