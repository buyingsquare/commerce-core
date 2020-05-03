<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2020
 * @package MW
 * @subpackage Session
 */


namespace Aimeos\MW\Session;


/**
 * Implementation using secure PHP session.
 *
 * @package MW
 * @subpackage Session
 */
class SecurePHP extends Base implements \Aimeos\MW\Session\Iface
{
    private static $encrypter = null;

	/**
	 * Remove the given key from the session.
	 *
	 * @param string $name Key of the requested value in the session
	 * @return \Aimeos\MW\Session\Iface Session instance for method chaining
	 */
	public function del( string $name ) : Iface
	{
		unset( $_SESSION[$name] );
		return $this;
	}


	/**
	 * Returns the value of the requested session key.
	 *
	 * If the returned value wasn't a string, it's decoded from its JSON
	 * representation.
	 *
	 * @param string $name Key of the requested value in the session
	 * @param mixed $default Value returned if requested key isn't found
	 * @return mixed Value associated to the requested key
	 */
	public function get( string $name, $default = null )
	{
	    if (self::$encrypter == null) {
            $key = isset($_ENV['APP_KEY']) ? $_ENV['APP_KEY'] : 'app_key';
            self::$encrypter = new \CodeZero\Encrypter\DefaultEncrypter($key);
        }

		if( isset( $_SESSION[$name] ) ) {
            try {
                self::$encrypter->decrypt($_SESSION[$name]);
            } catch (\CodeZero\Encrypter\DecryptException $exception) {
                throw new \Aimeos\MW\Session\Exception( 'Decryption failed' );
            }
		}

		return $default;
	}


	/**
	 * Sets the value for the specified key.
	 *
	 * If the value isn't a string, it's encoded into a JSON representation and
	 * decoded again when using the get() method.
	 *
	 * @param string $name Key to the value which should be stored in the session
	 * @param mixed $value Value that should be associated with the given key
	 * @return \Aimeos\MW\Session\Iface Session instance for method chaining
	 */
	public function set( string $name, $value ) : Iface
	{
        if (self::$encrypter == null) {
            $key = isset($_ENV['APP_KEY']) ? $_ENV['APP_KEY'] : 'app_key';
            self::$encrypter = new \CodeZero\Encrypter\DefaultEncrypter($key);
        }

        try {
            $_SESSION[$name] = self::$encrypter->decrypt($value);
        } catch (\CodeZero\Encrypter\DecryptException $exception) {
            throw new \Aimeos\MW\Session\Exception( 'Decryption failed' );
        }

		return $this;
	}
}
