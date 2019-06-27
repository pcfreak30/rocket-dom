<?php

namespace pcfreak30\RocketDOM;

use DOMNode;
use DOMXPath;

/**
 * Trait DOMElementTrait
 *
 * @package pcfreak30\RocketDOM
 * @property \pcfreak30\RocketDOM\DOMElement $parentNode
 */
trait DOMElementTrait {
	/**
	 * @param \DOMNode $newnode
	 */
	public function appendChild( DOMNode $newnode ) {
		$doc = $this->ownerDocument;
		if ( $this instanceof DOMDocument ) {
			$doc = $this;
		}
		if ( $doc && ! $newnode->ownerDocument->isSameNode( $this ) ) {
			/** @var \pcfreak30\RocketDOM\DOMElement $newnode_imported */
			$newnode_imported = $doc->importNode( $newnode, true );
			rocket_dom_node_map( $newnode_imported, $newnode );
			$newnode = $newnode_imported;
		}
		parent::appendChild( $newnode );
	}

	/**
	 *
	 */
	public function remove() {
		if ( $this->parentNode ) {
			$this->parentNode->removeChild( $this );
		}
	}

	/**
	 * @param $xpath_expr
	 *
	 * @return bool|\DOMElement
	 */
	public function next( $xpath_expr ) {
		$xpath      = new DOMXPath( $this->ownerDocument );
		$xpath_expr = trim( "following-sibling::{$xpath_expr}", ':' );
		if ( ( $result = $xpath->query( $xpath_expr, $this ) ) && 0 < $result->length ) {
			return $result->item( 0 );
		}

		return false;
	}

	/**
	 * @param $xpath_expr
	 *
	 * @return bool|\DOMElement
	 */
	public function prev( $xpath_expr ) {
		$xpath      = new DOMXPath( $this->ownerDocument );
		$xpath_expr = trim( "preceding-sibling::{$xpath_expr}", ':' );
		if ( ( $result = $xpath->query( $xpath_expr, $this ) ) && 0 < $result->length ) {
			return $result->item( 0 );
		}

		return false;
	}
}
