<?php
namespace Bookslots\Includes;

use Rakit\Validation\Rule;
use Rakit\Validation\Rules\Required;

class RequiredIfWith extends Required {

	/** @var bool */
	protected $implicit = true;

	/** @var string */
	protected $message = 'The :attribute is required';

	/**
	 * Given $params and assign $this->params
	 *
	 * @param array $params
	 * @return self
	 */
	public function fillParameters( array $params ): Rule {
		$this->params['fieldValues'] = array_chunk( $params, 2 );
		return $this;
	}

	/**
	 * Check the $value is valid
	 *
	 * @param mixed $value
	 * @return bool
	 */
	public function check( $value ): bool {
		$this->requireParameters( array( 'fieldValues' ) );
		$fieldValues       = $this->parameter( 'fieldValues' );
		$validator         = $this->validation->getValidator();
		$requiredValidator = $validator( 'required' );

		foreach ( $fieldValues as $fieldValue ) {
			$field        = $fieldValue[0];
			$value        = in_array( $fieldValue[1], array( 'true', 'false' ) ) ? (bool) $fieldValue[1] : $fieldValue[1];
			$anotherValue = $this->getAttribute()->getValue( $field );

			if ( str_starts_with( $value, '[' ) && str_ends_with( $value, ']' ) ) {
				$definedValues = explode( '/', substr( $value, 1, -1 ) );
				if ( ! in_array( $anotherValue, $definedValues ) ) {
					return true;
				}
			} else {
				if ( $value !== $anotherValue ) {
					return true;
				}
			}
		}

		$this->setAttributeAsRequired();
		return $requiredValidator->check( $value, array() );
	}
}
