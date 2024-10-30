<?php

namespace Bookslots\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

/**
 * The base model class.
 *
 * @package    Bookslot
 * @subpackage Bookslot/admin
 * @author     David Towoju <hello@pluginette.com>
 */
abstract class BaseModel {

	protected $rec, $attrs, $defaults;

	public function __get( $name ) {
		$value = null;

		if ( $this->magic_method_handler_exists( $name ) ) {
			$value = $this->call_magic_method_handler( 'get', $name );
		}

		$object_vars = array_keys( get_object_vars( $this ) );
		$rec_array   = (array) $this->rec;

		if ( in_array( $name, $object_vars ) ) {
			$value = $this->$name;
		} elseif ( array_key_exists( $name, $rec_array ) ) {
			if ( is_array( $this->rec ) ) {
				$value = $this->rec[ $name ];
			} else {
				$value = $this->rec->$name;
			}
		}

		// Alternative way to filter results through an sub class method
		$extend_fn = "get_extend_{$name}";
		if ( method_exists( $this, $extend_fn ) ) {
			$value = call_user_func( array( $this, $extend_fn ), $value );
		}

		return $value;
	}


	public function __set( $name, $value ) {
		// $value = $value;

		// Alternative way to filter results through an sub class method
		$extend_fn = "set_extend_{$name}";
		if ( method_exists( $this, $extend_fn ) ) {
			$value = call_user_func( array( $this, $extend_fn ), $value );
		}

		if ( $this->magic_method_handler_exists( $name ) ) {
			return $this->call_magic_method_handler( 'set', $name, $value );
		}

		$object_vars = array_keys( get_object_vars( $this ) );
		$rec_array   = (array) $this->rec;

		if ( in_array( $name, $object_vars ) ) {
			$this->$name = $value;
		} elseif ( array_key_exists( $name, $rec_array ) ) {
			if ( is_array( $this->rec ) ) {
				$this->rec[ $name ] = $value;
			} else {
				$this->rec->$name = $value;
			}
		} else {
			$this->$name = $value;
		}
	}

	public function __isset( $name ) {
		if ( $this->magic_method_handler_exists( $name ) ) {
			return $this->call_magic_method_handler( 'isset', $name );
		}

		if ( is_array( $this->rec ) ) {
			return isset( $this->rec[ $name ] );
		} elseif ( is_object( $this->rec ) ) {
			return isset( $this->rec->$name );
		} else {        return false;
		}
	}

	public function __unset( $name ) {
		if ( $this->magic_method_handler_exists( $name ) ) {
			return $this->call_magic_method_handler( 'unset', $name );
		}

		if ( is_array( $this->rec ) ) {
			unset( $this->rec[ $name ] );
		} elseif ( is_object( $this->rec ) ) {
			unset( $this->rec->$name );
		}
	}

	/** We just return a JSON encoding of the attributes in the model when we
	 * try to get a string for the model. */
	public function __toString() {
		return wp_json_encode( (array) $this->rec );
	}

	// If this function exists it will override the default behavior of looking in the rec object
	protected function magic_method_handler_exists( $name ) {
		return in_array( "mgm_{$name}", get_class_methods( $this ) );
	}

	protected function call_magic_method_handler( $mgm, $name, $value = '' ) {
		return call_user_func_array( array( $this, "mgm_{$name}" ), array( $mgm, $value ) );
	}

	public function get_values() {
		return filter_array_keys( (array) $this->rec, (array) $this->attrs );
	}

	public function get_attrs() {
		return (array) $this->attrs;
	}
}
