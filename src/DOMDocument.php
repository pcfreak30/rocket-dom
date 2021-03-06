<?php


namespace pcfreak30\RocketDOM;


/**
 * Class DOMDocument
 *
 * @package pcfreak30\RocketDOM
 */
class DOMDocument extends \DOMDocument {
	/**
	 *
	 */
	const SCRIPT_REGEX = '~(<script[^>]*>)(.*)(<\/script>)~isU';

	use /** @noinspection TraitsPropertiesConflictsInspection */
		DOMElementTrait;

	/**
	 * DOMDocument constructor.
	 *
	 * @param string $version
	 * @param string $encoding
	 */
	public function __construct( $version = '', $encoding = '' ) {
		parent::__construct( $version, $encoding );
		$this->registerNodeClass( '\DOMElement', '\pcfreak30\RocketDOM\DOMElement' );
	}

	/**
	 * @param $tag_type
	 *
	 * @return \pcfreak30\RocketDOM\DOMTagNameCollection
	 */
	public function get_nodes_by_type( $tag_type ) {
		return ( new DOMTagNameCollection( $this, $tag_type ) )->fetch();
	}

	/**
	 * @param $query
	 *
	 * @return \pcfreak30\RocketDOM\DOMXPathCollection
	 */
	public function get_nodes_by_xpath( $query, $node = null ) {
		return ( new DOMXPathCollection( $this, $query, new \DOMXPath( $this ), $node ) )->fetch();
	}

	/**
	 * @param string $name
	 * @param null   $value
	 *
	 * @return \pcfreak30\RocketDOM\DOMElement|void
	 */
	public function createElement( $name, $value = null ) {
		return parent::createElement( $name, $value );
	}


	/**
	 * @param string $source
	 * @param int    $options
	 *
	 * @return bool
	 */
	public function loadHTML( $source, $options = 0 ) {
		$source = $this->pre_process_scripts( $source );
		$source = $this->pre_process_styles( $source );
		$source = mb_convert_encoding( $source, 'HTML-ENTITIES', 'UTF-8' );

		return @parent::loadHTML( $source, $options );
	}

	/**
	 * @param $buffer
	 *
	 * @return null|string|string[]
	 */
	protected function pre_process_scripts( $buffer ) {
		return preg_replace_callback( self::SCRIPT_REGEX, [
			$this,
			'pre_process_scripts_callback',
		], $buffer );
	}

	public function pre_process_styles( $buffer ) {
		return preg_replace_callback( '~(<style[^>]*>)(.*)(<\/style>)~isU', [
			$this,
			'pre_process_styles_callback',
		], $buffer );
	}

	public function post_process_styles( $buffer ) {
		return preg_replace_callback( '~(<style[^>]*>)(.*)(<\/style>)~isU', [
			$this,
			'post_process_styles_callback',
		], $buffer );
	}

	/**
	 * @param \DOMNode|null $node
	 *
	 * @return null|string|string[]
	 */
	public function saveHTML( \DOMNode $node = null ) {
		$html = parent::saveHTML( $node );

		return $this->post_process_scripts( $html );
	}

	/**
	 * @param $buffer
	 *
	 * @return null|string|string[]
	 */
	protected function post_process_scripts( $buffer ) {
		return preg_replace_callback( self::SCRIPT_REGEX, [
			$this,
			'post_process_scripts_callback',
		], $buffer );
	}

	protected function pre_process_styles_callback( $match ) {
		if ( 0 === strlen( trim( $match[2] ) ) ) {
			return $match[0];
		}

		return "{$match[1]}" . encode_data( $match[2] ) . "{$match[3]}";
	}

	protected function post_process_styles_callback( $match ) {
		if ( 0 === strlen( trim( $match[2] ) ) ) {
			return $match[0];
		}

		return "{$match[1]}" . maybe_decode_data( $match[2] ) . "{$match[3]}";
	}

	/**
	 * @param $match
	 *
	 * @return string
	 */
	protected function pre_process_scripts_callback( $match ) {
		if ( trim( $match[2] ) === '' ) {
			return $match[0];
		}

		return $match[1] . encode_data( $match[2] ) . $match[3];
	}

	/**
	 * @param $match
	 *
	 * @return string
	 */
	protected function post_process_scripts_callback( $match ) {
		if ( trim( $match[2] ) === '' ) {
			return $match[0];
		}

		return $match[1] . maybe_decode_data( $match[2] ) . $match[3];
	}
}
