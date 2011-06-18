<?php
namespace Roadrunner\Model\Validator;

class DeliveryValidator extends AbstractValidator 
{
	/**
	 * Initialize validation constraints
	 */
	protected function initConstraints()
	{
		// delivery form validation constraints
		$this->validate['eval'] = array(
			'name' => array(
				'constraints' => array(
					ValidatorConstraint::MIN_STRING_LENGTH => 1,
				),
				'type' => ValidatorType::STRING,
				ValidatorConstraint::CONSTRAINT_ERROR_MSG => '',
			),
			'street' => array(
				'constraints' => array(
					ValidatorConstraint::MIN_STRING_LENGTH => 1,
				),
				'type' => ValidatorType::STRING,
				ValidatorConstraint::CONSTRAINT_ERROR_MSG => '',
			),
			'zip' => array(
				'constraints' => array(
					ValidatorConstraint::MIN_STRING_LENGTH => 1,
				),
				'type' => ValidatorType::STRING,
				ValidatorConstraint::CONSTRAINT_ERROR_MSG => '',
			),
			'city' => array(
				'constraints' => array(
					ValidatorConstraint::MIN_STRING_LENGTH => 1,
				),
				'type' => ValidatorType::STRING,
				ValidatorConstraint::CONSTRAINT_ERROR_MSG => '',
			),
			'country' => array(
				'constraints' => array(
					ValidatorConstraint::MIN_STRING_LENGTH => 1,
				),
				'type' => ValidatorType::STRING,
				ValidatorConstraint::CONSTRAINT_ERROR_MSG => '',
			),
			'item-min' => array(
				'constraints' => array(
					ValidatorConstraint::MIN_STRING_LENGTH => 1,
				),
				'type' => ValidatorType::FLOAT,
				ValidatorConstraint::CONSTRAINT_ERROR_MSG => '',
			),
			'item-max' => array(
				'constraints' => array(
					ValidatorConstraint::MIN_STRING_LENGTH => 1,
				),
				'type' => ValidatorType::FLOAT,
				ValidatorConstraint::CONSTRAINT_ERROR_MSG => '',
			),
			'item-name' => array(
				'constraints' => array(
					ValidatorConstraint::MIN_STRING_LENGTH => 1,
				),
				'type' => ValidatorType::STRING,
				ValidatorConstraint::CONSTRAINT_ERROR_MSG => '',
			),
		);
	}
}