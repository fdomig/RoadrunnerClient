<?php
namespace Roadrunner\Model\Validator;

class UserValidator extends AbstractValidator 
{
	/**
	 * Initialize validation constraints
	 */
	protected function initConstraints()
	{
		// delivery form validation constraints
		$this->validation['eval'] = array(
			'user-name' => array(
				'constraints' => array(
					ValidatorConstraint::MIN_STRING_LENGTH => 1,
				),
				'type' => ValidatorType::STRING,
				ValidatorConstraint::CONSTRAINT_ERROR_MSG => 'Please insert a User Name',
			),
			'user-pwd' => array(
				'constraints' => array(
					ValidatorConstraint::MIN_STRING_LENGTH => 1,
				),
				'type' => ValidatorType::STRING,
				ValidatorConstraint::CONSTRAINT_ERROR_MSG => '',
			),
		);
	}
	
	/**
	 * Validates a Container
	 * @param string $name
	 * @return array
	 */
	public function validateUser($name, $pwd, $roles)
	{
		$input = $this->createCheckable('user-name', $name, '', '');
		$input = array_merge($input, $this->createCheckable('user-pwd', $pwd));
		$errors = $this->validate($input);
		
		if ($roles == null || empty($roles)) {
			$errors['user-roles'] = 'Please select at least one Userrole'; 
		}
		return  $errors;
	}
}