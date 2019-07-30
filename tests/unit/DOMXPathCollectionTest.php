<?php

use Codeception\Test\Unit;
use pcfreak30\RocketDOM\DOMDocument;

class DOMXPathCollectionTest extends Unit {
	/**
	 * @var DOMDocument
	 */
	private $doc;
	/**
	 * @var \pcfreak30\RocketDOM\DOMTagNameCollection
	 */
	private $collection;

	public function test_changed() {
		$el = $this->collection->current();
		$this->collection->insert_after( $this->doc->createElement( 'script' ), $el );
		$this->collection->next();
		$this->assertEquals( $el, $this->collection->current() );
	}

	public function test_current() {
		$el = $this->collection->current();
		$this->assert_node( $el );
	}

	private function assert_node( $node ) {
		$this->assertInstanceOf( '\pcfreak30\RocketDOM\DOMElement', $node );
	}

	public function test_first() {
		$this->collection->next();
		$next = $this->collection->current();
		$el   = $this->collection->first();
		$this->assert_node( $el );
		$this->assertNotEquals( $next, $el );
	}

	public function test_append() {
		$el = $this->doc->createElement( 'script' );
		$this->collection->append( $el );
		$this->assert_node( $el );
		$this->assert_node( $this->collection->last() );
		$this->assertEquals( $el, $this->collection->last() );
	}

	public function test_insert_before() {
		$el = $this->doc->createElement( 'script' );
		$this->collection->insert_before( $el, $this->collection->current() );
		$this->assert_node( $el );
		$this->assert_node( $this->collection->first() );
		$this->assertEquals( $el, $this->collection->first() );
	}

	public function test_insert_before_current() {
		$el = $this->doc->createElement( 'script' );
		$this->collection->insert_before( $el );
		$this->assert_node( $el );
		$this->assert_node( $this->collection->first() );
		$this->assertEquals( $el, $this->collection->first() );
	}

	public function test_next() {
		$node = $this->collection->current();
		$this->collection->next();
		$this->assert_node( $node );
		$this->assertNotEquals( $this->collection->current(), $node );
	}

	public function test_remove() {
		$node = $this->collection->current();
		$this->collection->remove();
		$this->assertNull( $node->parentNode );
		$this->assertNotEquals( $this->collection->current(), $node );
	}

	public function test_remove_last() {
		$this->collection = $this->doc->get_nodes_by_type( 'body' );
		$node             = $this->collection->current();
		$this->collection->remove();
		$this->assertNull( $node->parentNode );
		$this->assertEquals( 0, $this->collection->count() );
	}

	public function test_rewind() {
		$this->collection->next();
		$this->assertEquals( 1, $this->collection->key() );
		$this->collection->rewind();
		$this->assertEquals( 0, $this->collection->key() );
	}

	public function test_seek() {
		$first = $this->collection->current();
		$seek  = random_int( 1, $this->collection->count() - 1 );
		$this->collection->seek_position( $seek );
		$node = $this->collection->current();
		$this->collection->rewind();
		$this->assertEquals( $seek, $this->collection->seek( $node ) );
		$this->assertEquals( $node, $this->collection->current() );
		$this->assertNotEquals( $node, $first );
	}

	public function test_count() {
		$this->assertEquals( 7, $this->collection->count() );
	}

	public function test_count_empty() {
		$this->dummy();
		$this->assertEquals( 0, $this->collection->count() );
	}

	private function dummy() {
		$this->collection = $this->doc->get_nodes_by_type( 'dummy' );
	}

	public function test_first_empty() {
		$this->dummy();
		$this->assertFalse( $this->collection->first() );
	}

	public function test_last_empty() {
		$this->dummy();
		$this->assertFalse( $this->collection->last() );
	}

	public function test_last() {
		$first = $this->collection->current();
		$el    = $this->collection->last();
		$this->assert_node( $el );
		$this->assertNotEquals( $el, $first );
	}

	public function test_valid() {
		$this->assertTrue( $this->collection->valid() );
		$this->collection->seek_position( $this->collection->count() - 1 );
		$this->collection->next();
		$this->assertFalse( $this->collection->valid() );
	}

	public function test_key() {
		$seek = random_int( 1, $this->collection->count() - 1 );
		$this->assertTrue( $this->collection->seek_position( $seek ) );
		$this->assertEquals( $seek, $this->collection->key() );
	}

	public function test_insert_at() {
		$seek = random_int( 1, $this->collection->count() - 1 );
		$el   = $this->doc->createElement( 'script' );
		$this->collection->insert_at( $el, $seek );
		$this->collection->seek_position( $seek );
		$this->assertEquals( $el, $this->collection->current() );
	}

	public function test_insert_after() {
		$seek = random_int( 1, $this->collection->count() - 2 );
		$el   = $this->doc->createElement( 'script' );
		$this->collection->insert_after( $el, $this->collection->peek( $seek ) );
		$this->collection->seek_position( $seek + 1 );
		$this->assertEquals( $el, $this->collection->current() );
	}

	public function test_insert_after_current() {
		$el = $this->doc->createElement( 'script' );
		$this->collection->insert_after( $el );
		$this->collection->next();
		$this->collection->next();
		$this->assertEquals( $el, $this->collection->current() );
	}

	public function test_insert_after_last() {
		$el = $this->doc->createElement( 'script' );
		$this->collection->insert_after( $el, $this->collection->last() );
		$this->assertEquals( $el, $this->collection->last() );
	}

	public function test_peek() {
		$seek = random_int( 1, $this->collection->count() - 2 );
		$this->assert_node( $this->collection->peek( $seek ) );
	}

	public function test_peek_bad() {
		$this->assertFalse( $this->collection->peek( 999 ) );
	}

	public function test_seek_position_bad() {
		$this->assertFalse( $this->collection->seek_position( 999 ) );
	}

	public function test_seek_bad() {
		$el = $this->collection->current();
		$this->collection->remove();
		$this->assertFalse( $this->collection->seek( $el ) );
	}

	protected function _before() {
		$this->doc = new DOMDocument();
		$this->assertTrue( $this->doc->loadHTML( DOM_TEST_HTML ) );
		$this->collection = $this->doc->get_nodes_by_type( 'script' );

	}

	protected function _after() {
		$this->doc = null;
	}
}
