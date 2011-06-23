<?php
namespace Roadrunner\Model\Validator;


/**
 * Abstract Class AbstractValidator
 * 
 * Every Validator Class must extend this Class and implement the doValidate
 * Method.
 * 
 * @author matthias schmid
 * @date 18.06.2011
 *
 */
abstract class AbstractValidator implements Validator 
{
	
	/**
	 * Validation Type Registry
	 * @var array
	 */
	protected $typereg = array(
		ValidatorType::INTEGER => array(
			'pattern' => '/^-{0,1}\d+$/',
		),
		ValidatorType::FLOAT => array(
			'pattern' => '/^[-+]?[0-9]*\.?[0-9]+([eE][-+]?[0-9]+)?$/',
		),
		ValidatorType::STRING => array(
			'pattern' => '/^[\s\S]*$/',
		),	
	);
	
	/**
	 * The Validation constraints
	 * @var array
	 */
	protected $validation = array();
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->initConstraints();
	}
	
	/**
	 * Validates the $input
	 * 
	 * @param array $input (<input-name> => array( 'name' => <validator-name>, 'value' => <value> )
	 * @return array 
	 */
	public function validate(array $input) 
	{
		$errors = array();
		if (array_key_exists('eval', $this->validation)) {
			$validate = $this->validation['eval'];
			
			foreach ($input as $checkable => $v) {
				
				if (array_key_exists($v['name'], $validate)) {
			
					// validate datatype
					if (!$this->do_reg($v['value'], $this->typereg[$validate[$v['name']]['type']]['pattern'])) {
						$errors[$checkable] = $validate[$v['name']][ValidatorConstraint::CONSTRAINT_ERROR_MSG];
						continue;
					}
					
					// validate specific constraints
					foreach($validate[$v['name']]['constraints'] as $name => $value) {
						switch($name) {
							case ValidatorConstraint::MIN_STRING_LENGTH:
								if (strlen($v['value']) < $value) {
									$errors[$checkable] = $validate[$v['name']][ValidatorConstraint::CONSTRAINT_ERROR_MSG]; 
								}
								break;
							default:
								break;
						}
					}
				}
			}
		}
		return $errors;
	}
	
	/**
	 * Initialize the validation constraints
	 */
	protected abstract function initConstraints();
	
	/**
	 * Tests if the $text matches the Regular expression $regex
	 * 
	 * @param string $text
	 * @param string $regex
	 * @return boolean
	 */
	protected function do_reg($text, $regex)
	{
		if (preg_match($regex, $text)) {
			return true;
		}
		return false;
	}
	
	/**
	 * Creates a validatable entry
	 * @param string $name
	 * @param array $const
	 * @param string $value
	 * @return multitype:multitype:unknown  
	 */
	protected function createCheckable($validationName, $value, $prefix = '', $suffix = '')
	{	
		return array(
			$prefix . $validationName . $suffix => array(
				'name'  => $validationName,
				'value' => $value,
			),
		);
	}
}