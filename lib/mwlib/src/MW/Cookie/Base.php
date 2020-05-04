<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2020
 * @package MW
 * @subpackage Cookie
 */


namespace Aimeos\MW\Cookie;


/**
 * Base class for cookie adapters
 *
 * @package MW
 * @subpackage Cookie
 */
abstract class Base implements \Aimeos\MW\Cookie\Iface
{
    public static $ONE_DAY_TIMEOUT = 1 * 24 * 60 * 60;
    public static $ONE_WEEK_TIMEOUT = 7 * 24 * 60 * 60;
    public static $ONE_MONTH_TIMEOUT = 30 * 24 * 60 * 60;

	/**
	 * Sets a list of key/value pairs.
	 *
	 * @param array $values Associative list of key/value pairs
     * @param int $timeout Seconds for the cookie lifetime
	 * @return \Aimeos\MW\Cookie\Iface Cookie instance for method chaining
	 */
	public function apply( array $values, int $timeout ) : Iface
	{
		foreach( $values as $key => $value ) {
			$this->set( $key, $value, $timeout );
		}

		return $this;
	}


	/**
	 * Returns the value of the requested cookie key and remove it from the cookie.
	 *
	 * If the returned value wasn't a string, it's decoded from its serialized
	 * representation.
	 *
	 * @param string $name Key of the requested value in the cookie
	 * @param mixed $default Value returned if requested key isn't found
	 * @return mixed Value associated to the requested key
	 */
	public function pull( string $name, $default = null )
	{
		$value = $this->get( $name, $default );
		$this->del( $name );

		return $value;
	}


	/**
	 * Remove the list of keys from the cookie.
	 *
	 * @param array $name Keys to remove from the cookie
	 * @return \Aimeos\MW\Cookie\Iface Cookie instance for method chaining
	 */
	public function remove( array $names ) : Iface
	{
		foreach( $names as $name ) {
			$this->del( $name );
		}

		return $this;
	}


    /**
     * Remove all data from the cookie.
     */
    public function clear()
    {
        foreach ($_COOKIE as $name => $value) {
            setcookie($name, $value, time() - self::$ONE_MONTH_TIMEOUT, '/');
        }
    }
}
