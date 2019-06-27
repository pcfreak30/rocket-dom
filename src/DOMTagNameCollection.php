<?php

namespace pcfreak30\RocketDOM;


class DOMTagNameCollection extends DOMXPathCollection {
	public function __construct( DOMDocument $document, $tag_name ) {
		parent::__construct( $document, "//{$tag_name}}", new \DOMXPath( $document ) );
	}
}
