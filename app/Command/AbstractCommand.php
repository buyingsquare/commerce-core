<?php

namespace App\Command;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;


/**
 * Common base class for all commands
 * @package laravel
 * @subpackage Command
 */
abstract class AbstractCommand extends Command
{
	/**
	 * Returns the enabled site items which may be limited by the input arguments.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context item object
	 * @param string|array $sites Unique site codes
	 * @return \Aimeos\Map List of site items implementing \Aimeos\MShop\Locale\Item\Site\Interface
	 */
	protected function getSiteItems( \Aimeos\MShop\Context\Item\Iface $context, $sites ) : \Aimeos\Map
	{
		$manager = \Aimeos\MShop::create( $context, 'locale/site' );
		$search = $manager->createSearch();

		if( is_scalar( $sites ) && $sites != '' ) {
			$sites = explode( ' ', $sites );
		}

		if( !empty( $sites ) ) {
			$search->setConditions( $search->compare( '==', 'locale.site.code', $sites ) );
		}

		return $manager->searchItems( $search );
	}
}