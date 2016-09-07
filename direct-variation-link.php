<?php
/*
Plugin Name: WooCommerce Direct Variation Link
Plugin URI: http://www.wpbackoffice.com/plugins/woocommerce-direct-variation-link/
Description: Link directly to a specific WooCommerce product variation using get variables (yoursite.com/your-single-product?size=small&color=blue).
Version: 1.0.3
Author: WP BackOffice
Author URI: http://www.wpbackoffice.com
*/

/**
* 	Output the variable product add to cart area.
*
*	@access public
* 	@subpackage  Product
* 	@return void
*/

add_filter('woocommerce_product_default_attributes', 'setup_swatches', 10, 1);

function setup_swatches($selected_attributes) {
	$varation_names = wpbo_get_variation_values();
	$start_vals = wpbo_get_variation_start_values( $varation_names );

	if(!empty($start_vals)) {
		return $start_vals;
	} else {
		return $selected_attributes;
	}
}

/*
*	Returns an array of variations related to a product
*
*	@access 		public
*	@subpackage  	Product
*	@return array	variation_names
*
*/
function wpbo_get_variation_values() {
	global $product;

	// Create an array of possible variations
	$available_variations = $product->get_variation_attributes();
	$varation_names = array();

	foreach ( $available_variations as $key => $variations ) {
		array_push( $varation_names, $key );
	}

	return $varation_names;
}

/*
*	Returns an array of variations related to a product
*
*	@access 		public
*	@subpackage  	Product
*	@param	array	variation_names
*	@return array	start_vals
*
*/
function wpbo_get_variation_start_values( $varation_names ) {
	global $product;

	$all_variations = $product->get_variation_attributes();

	$_GET_lower = array_change_key_case($_GET, CASE_LOWER);

	// Check to see if any of the attributes are in $_GET vars
	$start_vals = array();

	foreach ( $varation_names as $name ) {

		// Get the lower case name and remove the pa_ if they have it
		$lower_name = strtolower( $name );
		$spaceless_name = str_replace( ' ', '-', $lower_name );
		$clean_name = str_replace( 'pa_', '', $lower_name );

		$flag = false;

		// Grab the right variation based on the full name
		if ( isset( $_GET_lower[ $lower_name ] ) ) {

			foreach( $all_variations[ $name ] as $val ) {
				if ( strtolower( $val ) == strtolower( $_GET_lower[ $lower_name ] ) ) {
					$flag = true;
				}
			}

			if ( $flag == true ) {
				$start_vals[ $lower_name ] = $_GET_lower[ $lower_name ];
			}

		// Grab the right variation if they attribute has a pa_ infronnt of it
		} elseif ( isset( $_GET_lower[ $clean_name ] ) ) {

			foreach( $all_variations[ $name ] as $val ) {
				if ( strtolower( $val ) == strtolower( $_GET_lower[ $clean_name ] ) ) {
					$flag = true;
				}
			}

			if ( $flag == true ) {
				$start_vals[ $lower_name ] = $_GET_lower[ $clean_name ];
			}

		// Grab the right variation if the attribute has a space in it
		} elseif ( isset( $_GET_lower[ $spaceless_name ] ) ) {

			foreach( $all_variations[ $name ] as $val ) {

				if ( strtolower( $val ) == strtolower( $_GET_lower[ $spaceless_name ] ) ) {
					$flag = true;
				}

				if ( $flag == true ) {
					$start_vals[ $spaceless_name ] = $_GET_lower[ $spaceless_name ];
				}

			}

		}

	}

	return $start_vals;
}
