<?php


namespace pcfreak30\RocketDOM;


/**
 * Class DOMXPathCollection
 *
 * @package pcfreak30\RocketDOM
 */
class DOMXPathCollection implements \Iterator {
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
	 * DOMCollection constructor.
	 *
	 * @para string $query
	 *
	 * @param \pcfreak30\RocketDOM\DOMDocument $document
	 * @param \DOMXPath                        $xpath
	 * @param \DOMXPath                        $xpath
	 */
	public function __construct( DOMDocument $document, $query, \DOMXPath $xpath ) {
		$this->document = $document;
		$this->query    = $query;
		$this->xpath    = $xpath;
		$this->fetch();
	}

	/**
	 *
	 */
	private function fetch() {
		/** @var \pcfreak30\RocketDOM\DOMDocument $node */
		$this->list = iterator_to_array( $this->xpath->query( $this->query ) );
		$this->list = array_combine( array_map( 'spl_object_hash', $this->list ), $this->list );

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
	 * @return mixed Can return any type.
	 * @since 5.0.0
	 */
	public function current() {
		return $this->list[ $this->index ];
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
		return isset( $this->list[ $this->index ] ) && null !== isset( $this->list[ $this->index ] );
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
	 * @param DOMElement $node
	 */
	public function insert_before( DOMElement $node, DOMElement $existing_node ) {
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
		return array_search( spl_object_hash( $node ), array_keys( $this->list ) );
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
	public function insert_after( DOMElement $node, DOMElement $existing_node ) {
		if ( $existing_node->parentNode ) {
			if ( $this->index === count( $this->list ) - 1 ) {
				$this->append( $node );

				return;
			}
			$existing_node->parentNode->insertBefore( $node, $existing_node );
			$this->insert_at( $node, $this->get_node_index( $node ) );
		}
	}

	/**
	 * @param DOMElement $node
	 */
	public function append( $node ) {
		$this->document->appendChild( $node );
		$this->list[ spl_object_hash( $node ) ] = $node;
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

	public function count() {
		return count( $this->list );
	}
}
