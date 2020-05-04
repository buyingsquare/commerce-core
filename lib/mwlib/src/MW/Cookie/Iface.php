<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2020
 * @package MW
 * @subpackage Cookie
 */


namespace Aimeos\MW\Cookie;


/**
 * Generic minimal interface for managing cookie data.
 *
 * @package MW
 * @subpackage Cookie
 */
interface Iface
{
	/**
	 * Sets a list of key/value pairs.
	 *
	 * @param array $values Associative list of key/value pairs
     * @param int $timeout Seconds for the cookie lifetime
	 * @return \Aimeos\MW\Session\Iface Session instance for method chaining
	 */
	public function apply( array $values, int $timeout ) : Iface;

	/**
	 * Remove the given key from the cookie.
	 *
	 * @param string $name Key of the requested value in the cookie
	 * @return \Aimeos\MW\Session\Iface Session instance for method chaining
	 */
	public function del( string $name ) : Iface;

	/**
	 * Returns the value of the requested cookie key.
	 *
	 * If the returned value wasn't a string, it's decoded from its serialized
	 * representation.
	 *
	 * @param string $name Key of the requested value in the cookie
	 * @param mixed $default Value returned if requested key isn't found
	 * @return mixed Value associated to the requested key
	 */
	public function get( string $name, $default = null );

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
	public function pull( string $name, $default = null );

	/**
	 * Remove the list of keys from the cookie.
	 *
	 * @param array $name Keys to remove from the cookie
	 * @return \Aimeos\MW\Session\Iface Session instance for method chaining
	 */
	public function remove( array $names ) : Iface;

	/**
	 * Sets the value for the specified key.
	 *
	 * If the value isn't a string, it's encoded into a serialized representation
	 * and decoded again when using the get() method.
	 *
	 * @param string $name Key to the value which should be stored in the cookie
	 * @param string $value Value that should be associated with the given key
     * @param int $timeout Seconds for the cookie lifetime
	 * @return \Aimeos\MW\Session\Iface Session instance for method chaining
	 */
	public function set( string $name, string $value, int $timeout ) : Iface;
}
