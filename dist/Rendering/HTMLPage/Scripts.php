<?php
/**
 * Scripts section
 * @author hwoltersdorf
 */

namespace hollodotme\TreeMDown\Rendering\HTMLPage;

use hollodotme\TreeMDown\Rendering\HTMLPage;

/**
 * Class Scripts
 * @package hollodotme\TreeMDown\Rendering\HTMLPage
 */
class Scripts extends AbstractSection
{
	/**
	 * Prepare the section
	 */
	public function prepare()
	{
		// TODO: Implement prepare() method.
	}

	/**
	 * Add the section nodes
	 */
	public function addNodes()
	{
		foreach ( $this->getAssets( HTMLPage::ASSET_JS ) as $js_file )
		{
			$file_content = file_get_contents( $js_file );
			$elem         = $this->getElementWithAttributes( 'script', array('type' => 'text/javascript') );
			$elem->appendChild( $this->getDom()->createCDATASection( $file_content ) );
			$this->getContainer()->appendChild( $elem );
		}

		$this->getContainer()->appendChild(
			$this->getElementWithAttributes(
				'script', array('type' => 'text/javascript'),
				'hljs.initHighlightingOnLoad();'
			)
		);
	}
}