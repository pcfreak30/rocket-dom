<?php

use pcfreak30\RocketDOM\DOMDocument;

/**
 * Class DOMElementTest
 */
class DOMElementTest extends \Codeception\Test\Unit {
	/**
	 * @var DOMDocument
	 */
	private $doc;

	/**
	 *
	 */
	public function test_appendChild() {
		$collection = $this->doc->get_nodes_by_type( 'script' );
		$el         = $this->doc->createElement( 'script', 'test' );
		$collection->current()->parentNode->appendChild( $el );
		$this->assertInstanceOf( '\pcfreak30\RocketDOM\DOMElement', $collection->current()->parentNode->lastChild );
		$this->assertEquals( $el, $collection->current()->parentNode->lastChild );

	}

	public function test_appendChild_doc() {
		$el = $this->doc->createElement( 'script', 'test' );
		$this->doc->appendChild( $el );
		$this->assertInstanceOf( '\pcfreak30\RocketDOM\DOMElement', $this->doc->lastChild );
		$this->assertEquals( $el, $this->doc->lastChild );

	}

	public function test_remove() {
		$collection = $this->doc->get_nodes_by_type( 'script' );
		$collection->current()->remove();
		$this->assertNull( $collection->current()->parentNode );

	}

	public function test_next() {
		$collection = $this->doc->get_nodes_by_type( 'script' );
		$this->assertInstanceOf( '\pcfreak30\RocketDOM\DOMElement', $collection->current()->next( 'script' ) );
	}

	public function test_next_not_found() {
		$collection = $this->doc->get_nodes_by_type( 'script' );
		$collection->seek( $collection->last() );
		$this->assertFalse( $collection->current()->next( 'style' ) );
	}

	public function test_prev() {
		$collection = $this->doc->get_nodes_by_type( 'script' );
		$this->assertInstanceOf( '\pcfreak30\RocketDOM\DOMElement', $collection->current()->prev( 'p' ) );
	}

	public function test_prev_not_found() {
		$collection = $this->doc->get_nodes_by_type( 'script' );
		$this->assertFalse( $collection->current()->prev( 'html' ) );
	}

	protected function _before() {
		$this->doc = new DOMDocument();
		$this->assertTrue( $this->doc->loadHTML( DOM_TEST_HTML ) );
	}

	protected function _after() {
		$this->doc = null;
	}
}
