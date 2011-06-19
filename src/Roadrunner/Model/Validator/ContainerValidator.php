<?php
namespace Roadrunner\Model\Validator;

class ContainerValidator extends AbstractValidator 
{
	/**
	 * Initialize validation constraints
	 */
	protected function initConstraints()
	{
		// delivery form validation constraints
		$this->validation['eval'] = array(
			'container-name' => array(
				'constraints' => array(
					ValidatorConstraint::MIN_STRING_LENGTH => 1,
				),
				'type' => ValidatorType::STRING,
				ValidatorConstraint::CONSTRAINT_ERROR_MSG => 'Please insert a Container Name',
			),
			'sensor-uri' => array(
				'constraints' => array(
					ValidatorConstraint::MIN_STRING_LENGTH => 1,
				),
				'type' => ValidatorType::STRING,
				ValidatorConstraint::CONSTRAINT_ERROR_MSG => 'Please insert Sensor URI',
			),
		);
	}
	
	/**
	 * Validates a Container
	 * @param string $name
	 * @return array
	 */
	public function validateContainer($name)
	{
		return  $this->validate($this->createCheckable('container-name', $name, '', ''));
	}
}