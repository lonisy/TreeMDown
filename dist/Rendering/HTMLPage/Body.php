<?php
/**
 * Body of HTMLPage
 *
 * @author h.woltersdorf
 */

namespace hollodotme\TreeMDown\Rendering\HTMLPage;

use hollodotme\TreeMDown\Rendering\HTMLPage;

/**
 * Class Body
 *
 * @package hollodotme\TreeMDown\Rendering\HTMLPage
 */
class Body extends AbstractSection
{

	/**
	 * The parsed markdown as HTML-string
	 *
	 * @var null|\DOMElement
	 */
	protected $_parsed_markdown = null;

	/**
	 * The TOC
	 *
	 * @var null|\DOMElement
	 */
	protected $_toc = null;

	/**
	 * User messages
	 *
	 * @var array
	 */
	protected $_user_messages = array();

	/**
	 * Prepare the content
	 */
	public function prepare()
	{
		// Prepare parsed markdown
		$this->_prepareParsedMarkdown();

		// Prepare the TOC
		$this->_prepareTOC();
	}

	/**
	 * Add nodes to the DOM
	 */
	public function addNodes()
	{
		// Add Body element
		$body = $this->getElementWithAttributes( 'body', array('role' => 'document') );
		$this->getContainer()->appendChild( $body );

		if ( !is_null( $this->_toc ) )
		{
			$body->setAttribute( 'data-spy', 'scroll' );
			$body->setAttribute( 'data-target', '#toc' );
			$body->setAttribute( 'data-offset', '75' );
		}

		// Add header nav section
		$header = new Header( $body, $this->_tree );
		$header->setMetaDataArray( $this->_meta_data );
		$header->prepare();
		$header->addNodes();

		$section = $this->getDom()->createElement( 'section' );
		$body->appendChild( $section );

		$container = $this->getDom()->createElement( 'div' );
		$container->setAttribute( 'class', 'container-fluid' );
		$container->setAttribute( 'role', 'main' );
		$section->appendChild( $container );

		$row = $this->getDom()->createElement( 'div' );
		$row->setAttribute( 'class', 'row' );
		$container->appendChild( $row );

		$sidebar_column = $this->getDom()->createElement( 'div' );
		$sidebar_column->setAttribute( 'class', 'col-lg-3 col-md-3 col-sm-4 hidden-xs' );
		$row->appendChild( $sidebar_column );

		$content_column = $this->getDom()->createElement( 'div' );
		$row->appendChild( $content_column );

		// TOC exists?
		if ( !is_null( $this->_toc ) )
		{
			$content_column->setAttribute( 'class', 'col-lg-7 col-md-7 col-sm-8 col-xs-12' );

			// Add TOC column
			$toc_column = $this->getDom()->createElement( 'div' );
			$toc_column->setAttribute( 'class', 'col-lg-2 col-md-2 hidden-sm hidden-xs' );
			$row->appendChild( $toc_column );

			$toc = new TOC( $toc_column, $this->_tree );
			$toc->setMetaDataArray( $this->_meta_data );
			$toc->setToc( $this->_toc );

			$toc->prepare();
			$toc->addNodes();
		}
		else
		{
			$content_column->setAttribute( 'class', 'col-lg-9 col-md-9 col-sm-8 col-xs-12' );
		}

		// Add sidebar section
		$sidebar = new Sidebar( $sidebar_column, $this->_tree );
		$sidebar->setMetaDataArray( $this->_meta_data );
		$sidebar->prepare();
		$sidebar->addNodes();

		// Add content section
		$content = new Content( $content_column, $this->_tree );
		$content->setMetaDataArray( $this->_meta_data );
		$content->setUserMessages( $this->_user_messages );
		$content->setParsedMarkdown( $this->_parsed_markdown );
		$content->prepare();
		$content->addNodes();

		// Add footer section
		$footer = new Footer( $body, $this->_tree );
		$footer->setMetaDataArray( $this->_meta_data );
		$footer->setTocExists( !is_null( $this->_toc ) );
		$footer->prepare();
		$footer->addNodes();

		// Add scripts section
		$scripts = new Scripts( $body, $this->_tree );
		$scripts->setMetaDataArray( $this->_meta_data );
		$scripts->setAssetsArray( $this->_assets );
		$scripts->prepare();
		$scripts->addNodes();
	}

	/**
	 * Prepare the parsed markdown and/or user messages
	 */
	protected function _prepareParsedMarkdown()
	{
		$curent_file_with_root    = $this->getTree()->getSearch()->getCurrentFile( false );
		$curent_file_without_root = $this->getTree()->getSearch()->getCurrentFile( true );

		// Prepare the parsedown content
		if ( empty($curent_file_without_root) )
		{
			if ( $this->_tree->getSearch()->isCurrentFileValid() )
			{
				$this->_user_messages['info'][] = array(
					'title'   => 'No file selected',
					'message' => 'Browse the file tree on the left and click a file.',
				);
			}
			else
			{
				$this->_user_messages['danger'][] = array(
					'title'   => 'Invalid request',
					'message' => 'The file you requested is not accessable by this application.',
				);
			}
		}
		elseif ( file_exists( $curent_file_with_root ) && is_dir( $curent_file_with_root ) )
		{
			$this->_user_messages['warning'][] = array(
				'title'   => 'Directory selected',
				'message' => 'Cannot display the content of directories.
							  Browse the file tree on the left and click a file.',
			);
		}
		elseif ( file_exists( $curent_file_with_root ) && is_readable( $curent_file_with_root ) )
		{
			try
			{
				// Parsedown execution
				$parser = new \ParsedownExtra();

				$finfo         = new \finfo( FILEINFO_MIME_ENCODING );
				$file_encoding = $finfo->file( $curent_file_with_root );
				$file_content  = iconv( $file_encoding, 'utf-8', file_get_contents( $curent_file_with_root ) );

				$markdown = $parser->text( utf8_decode( $file_content ) );

				if ( !empty($markdown) )
				{
					$dom_implementation = new \DOMImplementation();
					$doc_type           = $dom_implementation->createDocumentType( 'html', '', '' );
					$dom                = $dom_implementation->createDocument( '', 'html', $doc_type );
					libxml_use_internal_errors( true );

					$dom->loadHTML( $markdown );

					$errors = libxml_get_errors();

					if ( !empty($errors) )
					{
						$messages = array();

						/** @var \LibXMLError $error */
						foreach ( $errors as $error )
						{
							$messages[] = $error->message;
						}

						$this->_user_messages['warning'][] = array(
							'title'   => 'This markdown file contains erroneous code',
							'message' => join( ', ', $messages ),
						);
					}

					$this->_parsed_markdown = $dom->documentElement;
				}
				else
				{
					$this->_user_messages['warning'][] = array(
						'title'   => ":-( You're not done yet!",
						'message' => 'This file has no content at all.',
					);
				}
			}
			catch ( \Exception $e )
			{
				$this->_parsed_markdown           = null;
				$this->_user_messages['danger'][] = array(
					'title'   => "Oops! An error occured while parsing markdown file",
					'message' => $curent_file_without_root . ': ' . $e->getMessage(),
				);
			}
		}
		else
		{
			$this->_user_messages['danger'][] = array(
				'title'   => '404',
				'message' => 'The file you requested does not exist or is not readable.',
			);
		}
	}

	/**
	 * Prepare the TOC
	 */
	protected function _prepareTOC()
	{
		if ( !is_null( $this->_parsed_markdown ) )
		{
			// setup xpath, this can be factored out
			$xpath = new \DOMXPath( $this->_parsed_markdown->ownerDocument );

			// grab all headings h2 and down from the document
			$headings = array('h2', 'h3');
			foreach ( $headings as $k => $v )
			{
				$headings[$k] = "self::$v";
			}

			$query_headings = join( ' or ', $headings );
			$query          = "//*[$query_headings]";
			$headings       = $xpath->query( $query );

			if ( $headings->length > 0 )
			{
				$dom_implementation = new \DOMImplementation();
				$doc_type           = $dom_implementation->createDocumentType( 'html', '', '' );
				$dom                = $dom_implementation->createDocument( '', 'html', $doc_type );
				$container          = $dom->documentElement;

				$toc_headline = $dom->createElement( 'h2', 'Table of Contents' );
				$container->appendChild( $toc_headline );

				// setup the table of contents element
				$toc_list = $dom->createElement( 'ul' );
				$toc_list->setAttribute( 'class', 'nav tmd-toc-1' );
				$container->appendChild( $toc_list );

				// iterate through headings and build the table of contents
				$current_level = 2;

				/** @var array|\DOMNode[] $parents */
				$parents = array(false, $toc_list);
				$i       = 0;

				/** @var \DOMElement $headline */
				foreach ( $headings as $headline )
				{
					$level = (int)$headline->tagName[1];
					$name  = $headline->textContent; // no support for formatting

					while ( $level > $current_level )
					{
						if ( !$parents[$current_level - 1]->lastChild )
						{
							$li = $dom->createElement( 'li' );
							$parents[$current_level - 1]->appendChild( $li );
						}

						$sublist = $dom->createElement( 'ul' );
						$sublist->setAttribute( 'class', 'nav tmd-toc-2' );
						$parents[$current_level - 1]->lastChild->appendChild( $sublist );
						$parents[$current_level] = $sublist;
						$current_level++;
					}

					while ( $level < $current_level )
					{
						$current_level--;
					}

					$anchor_id = strtolower( preg_replace( "#[^0-9a-z]#i", '-', $name ) ) . '__' . ++$i;

					$line = $dom->createElement( 'li' );
					$link = $dom->createElement( 'a', $name );
					$line->appendChild( $link );
					$parents[$current_level - 1]->appendChild( $line );

					// setup the anchors
					$headline->setAttribute( 'id', $anchor_id );
					$link->setAttribute( 'href', '#' . $anchor_id );

					$top_link = $headline->ownerDocument->createElement( 'a', 'Back to top' );
					$top_link->setAttribute( 'class', 'tmd-h-toplink pull-right' );
					$top_link->setAttribute( 'href', '#' );

					$headline->appendChild( $top_link );
				}

				// Set the TOC
				$this->_toc = $container;
			}
		}
	}
}