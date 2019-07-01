<?php

use Codeception\Test\Unit;
use pcfreak30\RocketDOM\DOMDocument;

class DOMDocumentTest extends Unit {


	private $html = <<<HTML
<!doctype html>
<html class="no-js" lang="">

<head>
  <meta charset="utf-8">
  <title></title>
  <meta name="description" content="">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link rel="manifest" href="site.webmanifest">
  <link rel="apple-touch-icon" href="icon.png">
  <!-- Place favicon.ico in the root directory -->

  <link rel="stylesheet" href="css/normalize.css">
  <link rel="stylesheet" href="css/main.css">

  <meta name="theme-color" content="#fafafa">
</head>

<body>
  <!--[if IE]>
    <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="https://browsehappy.com/">upgrade your browser</a> to improve your experience and security.</p>
  <![endif]-->

  <!-- Add your site or application content here -->
  <p>Hello world! This is HTML5 Boilerplate.</p>
  <script src="js/vendor/modernizr-3.7.1.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
  <script>window.jQuery || document.write('<script src="js/vendor/jquery-3.4.1.min.js"><\/script>')</script>
  <script src="js/plugins.js"></script>
  <script src="js/main.js"></script>

  <!-- Google Analytics: change UA-XXXXX-Y to be your site's ID. -->
  <script>
    window.ga = function () { ga.q.push(arguments) }; ga.q = []; ga.l = +new Date;
    ga('create', 'UA-XXXXX-Y', 'auto'); ga('set','transport','beacon'); ga('send', 'pageview')
  </script>
  <script src="https://www.google-analytics.com/analytics.js" async></script>
</body>

</html>
HTML;


	public function test_get_nodes_by_type() {

		$doc = new DOMDocument();
		$this->assertTrue( $doc->loadHTML( $this->html ) );

		$collection = $doc->get_nodes_by_type( 'script' );
		$this->assertInstanceOf( '\pcfreak30\RocketDOM\DOMTagNameCollection', $collection );
		$this->assertEquals( 7, $collection->count() );
	}

	public function test_loadHTML() {
		$doc = new DOMDocument();
		$this->assertTrue( $doc->loadHTML( $this->html ) );
	}

	public function test_saveHTML() {
		$doc = new DOMDocument();
		$doc->loadHTML( $this->html );

		$html = $doc->saveHTML();

		$this->assertEquals( 8, substr_count( $html, '<script' ) );
	}

	public function test_get_nodes_by_xpath() {
		$doc = new DOMDocument();
		$doc->loadHTML( $this->html );

		$collection = $doc->get_nodes_by_type( 'body' );
		$this->assertEquals( 1, $collection->count() );
	}
}