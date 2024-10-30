<?php
namespace Bookslots\Includes;

use Carbon\Carbon;
use Bookslots\Includes\BaseModel;
use Carbon\CarbonImmutable;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

/**
 * The base CPT model for classes to extend.
 *
 * @package    Bookslot
 * @author     David Towoju <hello@pluginette.com>
 */
abstract class BaseCpt extends BaseModel {

	// All inheriting classes should set -- public static $cpt (custom post type)
	public static $cpt = '';

	/** Requires defaults to be set */
	protected function load_cpt( $id, $cpt, $attrs ) {
		$this->attrs = $attrs;
		$this->rec   = get_post( $id );
		// dump($id);
		// dump(get_post($id));
		if ( null === $this->rec ) {
			$this->initialize_new_cpt();
		} elseif ( $this->post_type != $cpt ) {
			$this->initialize_new_cpt();
		} else {
			$this->load_meta( $id );
		}
	}

	/** This should only be used if the model is using a custom post type **/
	protected function initialize_new_cpt() {
		$whos_calling = get_class( $this );

		if ( ! isset( $this->attrs ) or ! is_array( $this->attrs ) ) {
			$this->attrs = array();
		}

		$r = array(
			'ID'           => null,
			'post_content' => '',
			'post_title'   => null,
			'post_excerpt' => '',
			'post_name'    => null,
			'post_date'    => null,
			'post_status'  => 'publish', // We'll assume this is published if not coming through the post editor
			'post_parent'  => 0,
			'menu_order'   => 0,
			'post_type'    => get_property( $whos_calling, 'cpt' ),
		);

		// Initialize postmeta variables
		// Backwards compatible in case attrs has no default values
		foreach ( $this->attrs as $var => $default ) {
			$r[ $var ] = $default;
		}

		$this->rec = (object) $r;

		return $this->rec;
	}

	/** Requires defaults to be set */
	protected function load_meta( $id ) {
		$metas = get_post_custom( $id );

		$rec = array();
		// dump($this->attrs);
		// Unserialize and set appropriately
		foreach ( $this->attrs as $akey => $aval ) {
			$rclass = new \ReflectionClass( $this );
			// dump($rclass);
			// This requires that the static variable have the same name
			// as the attribute key with "_str" appended
			$rkey = $rclass->getStaticPropertyValue( "{$akey}_str" );
			if ( isset( $metas[ $rkey ] ) ) {
				if ( count( $metas[ $rkey ] ) > 1 ) {
					$rec[ $akey ] = array();
					foreach ( $metas[ $rkey ] as $skey => $sval ) {
						$rec[ $akey ][ $skey ] = maybe_unserialize( $sval );
					}
				} else {
					$mval = $metas[ $rkey ][0];
					if ( $mval === '' and is_bool( $this->attrs[ $akey ] ) ) {
						$rec[ $akey ] = false;
					} else {
						$rec[ $akey ] = maybe_unserialize( $mval );
					}
				}

				if ( property_exists( $this, 'casts' ) && is_array( $this->casts ) && isset( $this->casts[ $rkey ] ) && $this->casts[ $rkey ] == 'datetime' ) {
					// $v            = CarbonImmutable::parse( $rec[ $akey ] );
					// $rec[ $akey ] = $v->setTimeFromTimeString( $v->format( 'H:i:s' ) );
					$rec[ $akey ] = CarbonImmutable::parse( $rec[ $akey ] );
				}
			}
		}

		$this->rec = (object) array_merge( (array) $this->rec, $this->attrs, $rec );
	}
}
