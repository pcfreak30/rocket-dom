<?php

use Codeception\Test\Unit;
use pcfreak30\RocketDOM\DOMDocument;

class DOMDocumentTest extends Unit {

	public function test_get_nodes_by_type() {

		$doc = new DOMDocument();
		$this->assertTrue( $doc->loadHTML( DOM_TEST_HTML ) );

		$collection = $doc->get_nodes_by_type( 'script' );
		$this->assertInstanceOf( '\pcfreak30\RocketDOM\DOMTagNameCollection', $collection );
		$this->assertEquals( 7, $collection->count() );
	}

	public function test_loadHTML() {
		$doc = new DOMDocument();
		$this->assertTrue( $doc->loadHTML( DOM_TEST_HTML ) );
	}

	public function test_saveHTML() {
		$doc = new DOMDocument();
		$doc->loadHTML( DOM_TEST_HTML );

		$html = $doc->saveHTML();

		$this->assertEquals( 8, substr_count( $html, '<script' ) );
	}

	public function test_get_nodes_by_xpath() {
		$doc = new DOMDocument();
		$doc->loadHTML( DOM_TEST_HTML );

		$collection = $doc->get_nodes_by_xpath( '//body' );
		$this->assertEquals( 1, $collection->count() );
	}
}
