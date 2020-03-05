<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020
 * @package MShop
 * @subpackage Index
 */


namespace Aimeos\MShop\Index\Manager\Text;


/**
 * SQL Server based index text for searching in product tables.
 *
 * @package MShop
 * @subpackage Index
 */
class SQLSrv
	extends \Aimeos\MShop\Index\Manager\Text\Standard
{
	private $searchConfig = array(
		'index.text:relevance' => array(
			'code' => 'index.text:relevance()',
/*			'internalcode' => ':site AND mindte."langid" = $1 AND (
				SELECT mindte_ft.RANK
				FROM CONTAINSTABLE("mshop_index_text", "content", $2) AS mindte_ft
				WHERE mindte."id" = mindte_ft."KEY"
			)',
*/			'internalcode' => ':site AND mindte."langid" = $1 AND CHARINDEX( $2, content )',
			'label' => 'Product texts, parameter(<language ID>,<search term>)',
			'type' => 'float',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_FLOAT,
			'public' => false,
		),
		'sort:index.text:relevance' => array(
			'code' => 'sort:index.text:relevance()',
//			'internalcode' => 'mindte_ft.RANK',
			'internalcode' => '-CHARINDEX( $2, content )',
			'label' => 'Product text sorting, parameter(<language ID>,<search term>)',
			'type' => 'float',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_FLOAT,
			'public' => false,
		),
	);


	/**
	 * Initializes the object
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context )
	{
		parent::__construct( $context );

		$level = \Aimeos\MShop\Locale\Manager\Base::SITE_ALL;
		$level = $context->getConfig()->get( 'mshop/index/manager/sitemode', $level );

		$func = function( $source, array $params ) {

			if( isset( $params[1] ) )
			{
				$strings = [];
				$regex = '/(\&|\||\!|\-|\+|\>|\<|\(|\)|\~|\*|\:|\"|\'|\@|\\| )+/';
				$search = trim( preg_replace( $regex, ' ', $params[1] ), "' \t\n\r\0\x0B" );

				foreach( explode( ' ', $search ) as $part )
				{
					$len = strlen( $part );

					if( $len > 0 ) {
						$strings[] = '"' . mb_strtolower( $part ) . '*"';
					}
				}

				$params[1] = '\'' . join( ' OR ', $strings ) . '\'';
			}

			return $params;
		};

		$name = 'index.text:relevance';
		$expr = $this->getSiteString( 'mindte."siteid"', $level );
		$this->searchConfig[$name]['internalcode'] = str_replace( ':site', $expr, $this->searchConfig[$name]['internalcode'] );
		$this->searchConfig['index.text:relevance']['function'] = $func;
	}


	/**
	 * Returns a list of objects describing the available criterias for searching.
	 *
	 * @param bool $withsub Return also attributes of sub-managers if true
	 * @return \Aimeos\MW\Criteria\Attribute\Iface[] List of search attriubte items
	 */
	public function getSearchAttributes( bool $withsub = true ) : array
	{
		$list = parent::getSearchAttributes( $withsub );

		foreach( $this->searchConfig as $key => $fields ) {
			$list[$key] = new \Aimeos\MW\Criteria\Attribute\Standard( $fields );
		}

		return $list;
	}
}
