<?php


namespace pcfreak30\RocketDOM;


use DOMNode;
use DOMXPath;
use Iterator;

/**
 * Class DOMXPathCollection
 *
 * @package pcfreak30\RocketDOM
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class DOMXPathCollection implements Iterator {
	/**
	 * @var string
	 */
	private $query;
	/**
	 * @var \pcfreak30\RocketDOM\DOMDocument
	 */
	private $document;

	/**
	 * @var bool
	 */
	private $change = false;

	/**
	 * @var array
	 */
	private $list = [];

	/**
	 * @var int
	 */
	private $index = 0;
	/**
	 * @var \DOMXPath
	 */
	private $xpath;
	/**
	 * @var DOMNode
	 */
	private $context_node;

	/**
	 * DOMCollection constructor.
	 *
	 * @para string $query
	 *
	 * @param \pcfreak30\RocketDOM\DOMDocument $document
	 * @param                                  $query
	 * @param \DOMXPath                        $xpath
	 * @param \DOMNode|null                    $context_node
	 */
	public function __construct( DOMDocument $document, $query, DOMXPath $xpath, DOMNode $context_node = null ) {
		$this->document     = $document;
		$this->query        = $query;
		$this->xpath        = $xpath;
		$this->context_node = $context_node ?: $document;
	}

	/**
	 *
	 */
	public function fetch() {
		/** @var \pcfreak30\RocketDOM\DOMDocument $node */
		$this->list = $this->xpath->query( $this->query, $this->context_node );
		if ( $this->list ) {
			$this->list = iterator_to_array( $this->list );
			$this->list = array_combine( array_map( 'spl_object_hash', $this->list ), $this->list );
			$this->rewind();
		}

		return $this;
	}

	/**
	 * Rewind the Iterator to the first element
	 *
	 * @link  https://php.net/manual/en/iterator.rewind.php
	 * @return void Any returned value is ignored.
	 * @since 5.0.0
	 */
	public function rewind() {
		$this->index = 0;
	}

	/**
	 *
	 */
	public function remove() {
		$item = $this->current();
		if ( $item && $item->parentNode ) {
			$item->parentNode->removeChild( $item );
		}
		unset( $this->list[ $this->index ] );
		$this->index ++;
		$this->change = true;
		if ( ! $this->valid() ) {
			$this->rewind();
		}
	}

	/**
	 * Return the current element
	 *
	 * @link  https://php.net/manual/en/iterator.current.php
	 * @return \pcfreak30\RocketDOM\DOMElement
	 * @since 5.0.0
	 */
	public function current() {
		$indexes = array_keys( $this->list );

		return $this->list[ $indexes[ $this->index ] ];
	}

	/**
	 * Checks if current position is valid
	 *
	 * @link  https://php.net/manual/en/iterator.valid.php
	 * @return boolean The return value will be casted to boolean and then evaluated.
	 * Returns true on success or false on failure.
	 * @since 5.0.0
	 */
	public function valid() {
		$indexes = array_keys( $this->list );

		return $this->list && isset( $indexes[ $this->index ] ) && null !== isset( $indexes[ $this->index ] );
	}

	/**
	 * @param \pcfreak30\RocketDOM\DOMElement $node
	 * @param \pcfreak30\RocketDOM\DOMElement $existing_node
	 */
	public function insert_before( DOMElement $node, DOMElement $existing_node = null ) {
		if ( ! $existing_node ) {
			$existing_node = $this->current();
		}

		if ( $existing_node->parentNode ) {
			$existing_node->parentNode->insertBefore( $node, $existing_node );
			$this->insert_at( $node, $this->get_node_index( $node ) );
		}
	}

	/**
	 * @param \pcfreak30\RocketDOM\DOMElement $node
	 * @param                                 $index
	 */
	public function insert_at( DOMElement $node, $index ) {
		/** @noinspection AdditionOperationOnArraysInspection */
		$this->list = array_slice( $this->list, 0, $index, true ) + [ $node ] + array_slice( $this->list, $index, null, true );
		$this->changed();
	}

	/**
	 *
	 */
	public function changed() {
		$this->change = true;
	}

	/**
	 * @param \pcfreak30\RocketDOM\DOMElement $node
	 *
	 * @return false|int|string
	 */
	private function get_node_index( DOMElement $node ) {
		return array_search( spl_object_hash( $node ), array_keys( $this->list ), false );
	}

	/**
	 * Return the key of the current element
	 *
	 * @link  https://php.net/manual/en/iterator.key.php
	 * @return mixed scalar on success, or null on failure.
	 * @since 5.0.0
	 */
	public function key() {
		return $this->index;
	}

	/**
	 *
	 */
	public function __destruct() {
		$this->list = null;
	}

	/**
	 * @param \pcfreak30\RocketDOM\DOMElement $node
	 * @param \pcfreak30\RocketDOM\DOMElement $existing_node
	 */
	public function insert_after( DOMElement $node, DOMElement $existing_node = null ) {
		if ( ! $existing_node ) {
			$existing_node = $this->current();
		}

		if ( $existing_node->parentNode ) {
			$index = $this->get_node_index( $existing_node );
			if ( $index === $this->count() - 1 ) {
				$this->append( $node );

				return;
			}
			$existing_node->parentNode->insertBefore( $node, $existing_node );
			$this->insert_at( $node, $index + 1 );
		}
	}

	/**
	 * @param DOMElement $node
	 */
	public function append( $node ) {
		$this->document->appendChild( $node );
		$this->list[ spl_object_hash( $node ) ] = $node;
	}

	public function peek( $index ) {
		$indexes = array_keys( $this->list );

		if ( ! isset( $indexes[ $index ] ) ) {
			return false;
		}

		return $this->list[ $indexes[ $index ] ];
	}

	/**
	 * Move forward to next element
	 *
	 * @link  http://php.net/manual/en/iterator.next.php
	 * @return void Any returned value is ignored.
	 * @since 5.0.0
	 */
	public function next() {
		if ( ! $this->change ) {
			$this->index ++;
		}
		$this->change = false;
	}

	public function seek( DOMElement $node ) {
		$index = $this->get_node_index( $node );
		if ( $index ) {
			$this->index = $index;

			return $index;
		}

		return false;
	}

	public function seek_position( $index ) {
		$original    = $this->index;
		$this->index = $index;
		if ( ! $this->valid() ) {
			$this->index = $original;

			return false;
		}

		return true;
	}

	public function first() {
		if ( $this->count() ) {
			$list = array_keys( $this->list );

			return $this->list[ $list[0] ];
		}

		return false;
	}

	public function count() {
		if ( ! $this->list ) {
			return 0;
		}

		return count( $this->list );
	}

	public function last() {
		if ( $this->count() ) {
			$list = array_keys( $this->list );

			return $this->list[ end( $list ) ];
		}

		return false;
	}
}
