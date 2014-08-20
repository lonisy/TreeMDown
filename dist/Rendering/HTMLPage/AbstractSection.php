<?php
/**
 * Abstract section of HTMLPage
 *
 * @author h.woltersdorf
 */

namespace hollodotme\TreeMDown\Rendering\HTMLPage;

use hollodotme\TreeMDown\Rendering\HTMLTree;

/**
 * Class AbstractSection
 *
 * @package hollodotme\TreeMDown\Rendering\HTMLPage
 */
abstract class AbstractSection
{
	/**
	 * Assets
	 *
	 * @var array
	 */
	protected $_assets = array();

	/**
	 * Meta data
	 *
	 * @var array
	 */
	protected $_meta_data = array();

	/**
	 * The conatiner DOMElement
	 *
	 * @var \DOMElement|null
	 */
	protected $_container = null;

	/**
	 * The HTMLTree
	 *
	 * @var HTMLTree|null
	 */
	protected $_tree = null;

	/**
	 * Constructor
	 *
	 * @param \DOMElement  $container
	 * @param HTMLTree     $tree
	 */
	public function __construct( \DOMElement $container, HTMLTree $tree )
	{
		$this->_container = $container;
		$this->_tree      = $tree;
	}

	/**
	 * Return the DOMDocument
	 *
	 * @return \DOMDocument|null
	 */
	public function getDom()
	{
		return $this->_container->ownerDocument;
	}

	/**
	 * Return the container DOMElement
	 *
	 * @return \DOMElement|null
	 */
	public function getContainer()
	{
		return $this->_container;
	}

	/**
	 * Return the HTMLTree
	 *
	 * @return HTMLTree|null
	 */
	public function getTree()
	{
		return $this->_tree;
	}

	/**
	 * Add an asset with type
	 *
	 * @param string $type
	 * @param string $asset
	 */
	public function addAsset( $type, $asset )
	{
		$this->_assets[$type][] = $asset;
	}

	/**
	 * Set all assets by an array
	 *
	 * @param array $assets
	 */
	public function setAssetsArray( array $assets )
	{
		$this->_assets = $assets;
	}

	/**
	 * Get assets (of type)
	 *
	 * @param null|string $type
	 *
	 * @return array
	 */
	public function getAssets( $type = null )
	{
		$assets = array();
		if ( is_null( $type ) )
		{
			$assets = $this->_assets;
		}
		elseif ( array_key_exists( $type, $this->_assets ) )
		{
			$assets = $this->_assets[$type];
		}

		return $assets;
	}

	/**
	 * Set meta data of type with value
	 *
	 * @param string $type
	 * @param string $value
	 */
	public function setMetaData( $type, $value )
	{
		$this->_meta_data[$type] = $value;
	}

	/**
	 * Set a whole meta data array
	 *
	 * @param array $meta_data
	 */
	public function setMetaDataArray( array $meta_data )
	{
		$this->_meta_data = $meta_data;
	}

	/**
	 * Return meta data of type
	 *
	 * @param string $type
	 *
	 * @return null|string
	 */
	public function getMetaData( $type )
	{
		$meta_data = null;

		if ( array_key_exists( $type, $this->_meta_data ) )
		{
			$meta_data = $this->_meta_data[$type];
		}

		return $meta_data;
	}

	/**
	 * Return a new \DOMElement with attributes
	 *
	 * @param string      $name
	 * @param array       $attributes
	 * @param string|null $content
	 *
	 * @return \DOMElement
	 */
	public function getElementWithAttributes( $name, array $attributes, $content = null )
	{
		$elem = $this->getDom()->createElement( $name, $content );

		foreach ( $attributes as $attr_name => $attr_value )
		{
			$elem->setAttribute( $attr_name, $attr_value );
		}

		return $elem;
	}

	/**
	 * Prepare the section
	 */
	public function prepare()
	{
		// Override in extending classes
	}

	/**
	 * Add the section nodes
	 */
	abstract public function addNodes();
}