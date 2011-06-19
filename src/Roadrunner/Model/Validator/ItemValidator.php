<?php
namespace Roadrunner\Model\Validator;

class ItemValidator extends AbstractValidator 
{
	/**
	 * Initialize validation constraints
	 */
	protected function initConstraints()
	{
		// delivery form validation constraints
		$this->validation['eval'] = array(
			'item-mintemp' => array(
				'constraints' => array(
					ValidatorConstraint::MIN_STRING_LENGTH => 1,
				),
				'type' => ValidatorType::FLOAT,
				ValidatorConstraint::CONSTRAINT_ERROR_MSG => 'Please correct the Minimum Temperature',
			),
			'item-maxtemp' => array(
				'constraints' => array(
					ValidatorConstraint::MIN_STRING_LENGTH => 1,
				),
				'type' => ValidatorType::FLOAT,
				ValidatorConstraint::CONSTRAINT_ERROR_MSG => 'Please correct the Maximum Temperature',
			),
			'item-name' => array(
				'constraints' => array(
					ValidatorConstraint::MIN_STRING_LENGTH => 1,
				),
				'type' => ValidatorType::STRING,
				ValidatorConstraint::CONSTRAINT_ERROR_MSG => 'Please enter an Item name',
			),
		);
	}
	
	/**
	 * Validates a Single Item
	 * @param string $name
	 * @param string $mintemp
	 * @param string $maxtemp
	 * @return array
	 */
	public function validateSingleItem($name, $mintemp, $maxtemp)
	{
		$input = $this->createCheckable('item-name', $name, '', '');
		$input = array_merge($input, $this->createCheckable('item-mintemp', $mintemp, '', ''));
		$input = array_merge($input, $this->createCheckable('item-maxtemp', $maxtemp, '', ''));
		
		
		$errors =  $this->validate($input);
		
		if (count($errors) <= 0) {
			if ((int)$mintemp > (int)$maxtemp) {
				$errors['item-minmax'] = 'Minimum Temperature cannot be greater than maximum Temperature';
			}
		}
		return $errors;
	}
}