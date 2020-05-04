<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2020
 * @package MW
 * @subpackage Cookie
 */


namespace Aimeos\MW\Cookie;


/**
 * Implementation using PHP cookie.
 *
 * @package MW
 * @subpackage Cookie
 */
class PHP extends Base implements \Aimeos\MW\Cookie\Iface
{
	/**
	 * Remove the given key from the cookie.
	 *
	 * @param string $name Key of the requested value in the cookie
	 * @return \Aimeos\MW\Cookie\Iface Cookie instance for method chaining
	 */
	public function del( string $name ) : Iface
	{
        setcookie($name, $_COOKIE[$name], time() - self::$ONE_MONTH_TIMEOUT, '/');
		return $this;
	}


	/**
	 * Returns the value of the requested cookie key.
	 *
	 * If the returned value wasn't a string, it's decoded from its JSON
	 * representation.
	 *
	 * @param string $name Key of the requested value in the cookie
	 * @param mixed $default Value returned if requested key isn't found
	 * @return mixed Value associated to the requested key
	 */
	public function get( string $name, $default = null )
	{
		if( isset( $_COOKIE[$name] ) ) {
			return $_COOKIE[$name];
		}

		return $default;
	}


	/**
	 * Sets the value for the specified key.
	 *
	 * If the value isn't a string, it's encoded into a JSON representation and
	 * decoded again when using the get() method.
	 *
	 * @param string $name Key to the value which should be stored in the cookie
	 * @param string $value Value that should be associated with the given key
     * @param int $timeout Seconds for the cookie lifetime
	 * @return \Aimeos\MW\Cookie\Iface Cookie instance for method chaining
	 */
	public function set( string $name, string $value, int $timeout ) : Iface
	{
        setcookie($name, $value, time() + $timeout, '/');
		return $this;
	}
}
