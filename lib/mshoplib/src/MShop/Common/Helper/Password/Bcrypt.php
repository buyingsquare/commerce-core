<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2014-2020
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Helper\Password;


/**
 * Bcrypt implementation of the password helper
 *
 * @package MShop
 * @subpackage Common
 */
class Bcrypt implements \Aimeos\MShop\Common\Helper\Password\Iface
{
	private $options = [];


	/**
	 * Initializes the password helper.
	 *
	 * @param array $options Associative list of key/value pairs of options specific for the hashing method
	 */
	public function __construct( array $options )
	{
		if( !function_exists( 'password_hash' ) ) {
			throw new \Aimeos\MShop\Exception( 'To use the BCrypt encoder, you need to upgrade to PHP 5.5 or install the "ircmaxell/password-compat" via Composer' );
		}

        if( !function_exists( 'password_verify' ) ) {
            throw new \Aimeos\MShop\Exception( 'To use the BCrypt verify, you need to upgrade to PHP 5.5 or install the "ircmaxell/password-compat" via Composer' );
        }

		$this->options = $options;
	}


	/**
	 * Returns the hashed password.
	 *
	 * @param string $password Clear text password string
	 * @param string|null $salt Password salt
	 * @return string Hashed password
	 */
	public function encode( string $password, string $salt = null ) : string
	{
	    return password_hash( $salt.$password.$salt, PASSWORD_BCRYPT );
	}

    /**
     * Returns the result to correct password.
     *
     * @param string $password Clear text password string
     * @param string $hash Encode text password string
     * @param string|null $salt Password salt
     * @return bool Verify for checking password
     */
    public function verify( string $password, string $hash, string $salt = null ) : bool
    {
        return password_verify( $salt.$password.$salt, $hash );
    }
}
