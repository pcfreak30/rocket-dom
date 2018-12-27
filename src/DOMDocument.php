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
	 * @return \DOMNodeList
	 */
	public function get_script_tags() {
		return $this->getElementsByTagName( 'script' );
	}

	/**
	 * @param string $source
	 * @param int    $options
	 *
	 * @return bool
	 */
	public function loadHTML( $source, $options = 0 ) {
		return @parent::loadHTML( $this->pre_process_scripts( $source ), $options );
	}

	/**
	 * @param $buffer
	 *
	 * @return null|string|string[]
	 */
	public function pre_process_scripts( $buffer ) {
		return preg_replace_callback( self::SCRIPT_REGEX, [
			$this,
			'pre_process_scripts_callback',
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
	public function post_process_scripts( $buffer ) {
		return preg_replace_callback( self::SCRIPT_REGEX, [
			$this,
			'post_process_scripts_callback',
		], $buffer );
	}

	/**
	 * @param $match
	 *
	 * @return string
	 */
	protected function pre_process_scripts_callback( $match ) {
		if ( 0 === strlen( trim( $match[2] ) ) ) {
			return $match[0];
		}

		return $match[1] . encode_script( $match[2] ) . $match[3];
	}

	/**
	 * @param $match
	 *
	 * @return string
	 */
	protected function post_process_scripts_callback( $match ) {
		if ( 0 === strlen( trim( $match[2] ) ) ) {
			return $match[0];
		}

		return $match[1] . maybe_decode_script( $match[2] ) . $match[3];
	}
}
