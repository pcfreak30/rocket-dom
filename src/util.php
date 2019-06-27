<?php

namespace pcfreak30\RocketDOM;

if ( ! function_exists( __NAMESPACE__ . '\rocket_dom_maybe_decode_script' ) ) {
	function rocket_dom_maybe_decode_script( $data ) {
		if ( rocket_dom_is_base64_encoded( $data ) ) {
			return json_decode( base64_decode( $data ) );
		}

		return $data;
	}
}

if ( ! function_exists( __NAMESPACE__ . '\rocket_dom_encode_script' ) ) {
	function rocket_dom_encode_script( $data ) {
		return base64_encode( json_encode( $data ) );
	}
}

if ( ! function_exists( __NAMESPACE__ . '\rocket_dom_is_base64_encoded' ) ) {
	function rocket_dom_is_base64_encoded( $data ) {
		return base64_decode( $data, true ) && json_decode( base64_decode( $data ) );
	}
}

if ( ! function_exists( __NAMESPACE__ . '\rocket_dom_node_map' ) ) {
	function rocket_dom_node_map( DOMElement $key, DOMElement $value = null ) {
		static $map;

		if ( null === $map ) {
			$map = new \SplObjectStorage();
		}

		if ( null !== $value && $value instanceof DOMElement ) {
			$map[ $key ] = $value;
		}

		return $map[ $key ];
	}
}
