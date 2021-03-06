<?php
/**
 * Default options
 *
 * @author hollodotme
 */

namespace hollodotme\TreeMDown\Misc;

/**
 * Class DefaultOptions
 *
 * @package hollodotme\TreeMDown\Misc
 */
class DefaultOptions extends Options
{
	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->set( Opt::EMPTY_FOLDERS_HIDDEN, false );
		$this->set( Opt::FILE_DEFAULT, 'index.md' );
		$this->set( Opt::FILENAME_SUFFIX_HIDDEN, false );
		$this->set( Opt::GITHUB_RIBBON_ENABLED, false );
		$this->set( Opt::NAME_COMPANY, 'hollodotme' );
		$this->set( Opt::NAME_PROJECT, 'TreeMDown' );
		$this->set( Opt::NAMES_PRETTYFIED, false );
		$this->set( Opt::PROJECT_ABSTRACT, "[triː <'em> daʊn]" );
		$this->set( Opt::PATH_EXCLUDE_PATTERNS, array( '.*' ) );
		$this->set( Opt::PATH_INCLUDE_PATTERNS, array( '*.md', '*.markdown' ) );
		$this->set( Opt::DIR_ROOT, '.' );
		$this->set( Opt::SEARCH_TERM, '' );
		$this->set( Opt::FILE_CURRENT, '' );
		$this->set( Opt::OUTPUT_TYPE, Opt::OUTPUT_TYPE_DOM );
		$this->set( Opt::GITHUB_RIBBON_URL, 'https://github.com/hollodotme/TreeMDown' );
		$this->set(
			Opt::BASE_PARAMS, array(
				'tmd_f' => 'index.md',
				'tmd_q' => '',
			)
		);
	}
}
