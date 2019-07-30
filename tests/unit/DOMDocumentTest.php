<?php

use Codeception\Test\Unit;
use pcfreak30\RocketDOM\DOMDocument;

class DOMDocumentTest extends Unit {
	/**
	 * @var DOMDocument
	 */
	private $doc;

	public function test_get_nodes_by_type() {

		$this->assertTrue( $this->doc->loadHTML( DOM_TEST_HTML ) );

		$collection = $this->doc->get_nodes_by_type( 'script' );
		$this->assertInstanceOf( '\pcfreak30\RocketDOM\DOMTagNameCollection', $collection );
		$this->assertEquals( 7, $collection->count() );
	}

	public function test_loadHTML() {
		$this->assertTrue( $this->doc->loadHTML( DOM_TEST_HTML ) );
	}

	public function test_saveHTML() {
		$this->doc->loadHTML( DOM_TEST_HTML );

		$html = $this->doc->saveHTML();

		$this->assertEquals( 8, substr_count( $html, '<script' ) );
	}

	public function test_get_nodes_by_xpath() {
		$this->doc->loadHTML( DOM_TEST_HTML );

		$collection = $this->doc->get_nodes_by_xpath( '//body' );
		$this->assertEquals( 1, $collection->count() );
	}

	protected function _before() {
		$this->doc = new DOMDocument();
	}

	protected function _after() {
		$this->doc = null;
	}
}
