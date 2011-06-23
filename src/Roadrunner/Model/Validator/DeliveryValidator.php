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
		$this->validation['eval'] = array(
			'name' => array(
				'constraints' => array(
					ValidatorConstraint::MIN_STRING_LENGTH => 1,
				),
				'type' => ValidatorType::STRING,
				ValidatorConstraint::CONSTRAINT_ERROR_MSG => 'Please correct field Name',
			),
			'street' => array(
				'constraints' => array(
					ValidatorConstraint::MIN_STRING_LENGTH => 1,
				),
				'type' => ValidatorType::STRING,
				ValidatorConstraint::CONSTRAINT_ERROR_MSG => 'Please correct field Street',
			),
			'zip' => array(
				'constraints' => array(
					ValidatorConstraint::MIN_STRING_LENGTH => 1,
				),
				'type' => ValidatorType::STRING,
				ValidatorConstraint::CONSTRAINT_ERROR_MSG => 'Please correct field Zip',
			),
			'city' => array(
				'constraints' => array(
					ValidatorConstraint::MIN_STRING_LENGTH => 1,
				),
				'type' => ValidatorType::STRING,
				ValidatorConstraint::CONSTRAINT_ERROR_MSG => 'Please correct field City',
			),
			'country' => array(
				'constraints' => array(
					ValidatorConstraint::MIN_STRING_LENGTH => 1,
				),
				'type' => ValidatorType::STRING,
				ValidatorConstraint::CONSTRAINT_ERROR_MSG => 'Please correct field Country',
			),
			'item-min' => array(
				'constraints' => array(
					ValidatorConstraint::MIN_STRING_LENGTH => 1,
				),
				'type' => ValidatorType::FLOAT,
				ValidatorConstraint::CONSTRAINT_ERROR_MSG => 'Please correct the Minimum Temperature',
			),
			'item-max' => array(
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
	
	public function validateItem($name, $mintemp, $maxtemp, $index)
	{
		$input = $this->createCheckable('item-name', $name, $index, '');
		$input = array_merge($input, $this->createCheckable('item-min', $mintemp, $index, ''));
		$input = array_merge($input, $this->createCheckable('item-max', $maxtemp, $index, ''));
		
		return $this->validate($input);
	}
	
	/**
	 * Validates the Delivery Creation and Update Form
	 * @param array $address
	 * @param string $prefix
	 * @param Application $escaper
	 * @return array errors
	 */
	public function validateAddress($address, $prefix, $suffix, $escaper) 
	{
		$validateAddress = array(
			'name' => $escaper->escape($address['name']),
			'street' => $escaper->escape($address['street']),
			'zip' => $escaper->escape($address['zip']),
			'city' => $escaper->escape($address['city']),
			'country' => $escaper->escape($address['country']),
		);
		
		$input = $this->createCheckableAddress($validateAddress, $prefix, $suffix);
		return $this->validate($input);
	}
	
	/**
	 * Creates a checkable Address
	 * @param array $address
	 * @param string $prefix
	 * @return array
	 */
	private function createCheckableAddress($address, $prefix, $suffix)
	{
		$input = array();
		foreach($address as $k => $v) {
			$input = array_merge($input, $this->createCheckable($k, $v, $prefix, $suffix));
		}
		return $input;
	}
}