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
			'pattern' => '^-{0,1}\d+$',
		),
		ValidatorType::FLOAT => array(
			'pattern' => '^[-+]?[0-9]*\.?[0-9]+([eE][-+]?[0-9]+)?$',
		),
		ValidatorType::STRING => array(
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
	 * @param array $input
	 * @return array 
	 */
	public function validate($input) 
	{
		$errors = array();
		if (array_key_exists('eval', $this->validation)) {
			$validate = $this->validation['eval'];
			
			foreach ($input as $k => $v) {
				if (array_key_exists($k, $validate)) {
					
					// validate datatype
					if (!$this->do_reg($v, $this->typereg[$validate[$k]['type']]['pattern'])) {
						$errors[$k] = $validate[$k][ValidatorConstraint::CONSTRAINT_ERROR_MSG];
						continue;
					}
					
					// validate specific constraints
					foreach($validate[$k]['constraints'] as $name => $value) {
						switch($name) {
							case ValidatorConstraint::MIN_STRING_LENGTH:
								if (strlen($validate[$k][ValidatorConstraint::MIN_STRING_LENGTH]) < $value) {
									$errors[$k] = $validate[$k][ValidatorConstraint::CONSTRAINT_ERROR_MSG]; 
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
}