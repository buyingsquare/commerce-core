<?php

namespace App\Base;

class Aimeos
{
	/**
	 * @var \Illuminate\Contracts\Config\Repository
	 */
	private $config;

	/**
	 * @var \Aimeos\Bootstrap
	 */
	private $object;


	/**
	 * Initializes the object
	 *
	 * @param \Illuminate\Contracts\Config\Repository $config Configuration object
	 */
	public function __construct( \Illuminate\Contracts\Config\Repository $config )
	{
		$this->config = $config;
	}


	/**
	 * Returns the Aimeos object.
	 *
	 * @return \Aimeos\Bootstrap Aimeos bootstrap object
	 */
	public function get() : \Aimeos\Bootstrap
	{
		if( $this->object === null )
		{
			$dir = base_path( 'ext' );

			if( !is_dir( $dir ) ) {
				$dir = dirname( dirname( dirname( dirname( __DIR__ ) ) ) ) . DIRECTORY_SEPARATOR . 'ext';
			}

			$extDirs = (array) $this->config->get( 'shop.extdir', $dir );
			$this->object = new \Aimeos\Bootstrap( $extDirs, false );
		}

		return $this->object;
	}


	/**
	 * Returns the version of the Aimeos package
	 *
	 * @return string Version string
	 */
	public function getVersion() : string
	{
		if( ( $content = @file_get_contents( base_path( 'composer.lock' ) ) ) !== false
			&& ( $content = json_decode( $content, true ) ) !== null && isset( $content['packages'] )
		) {
			foreach( (array) $content['packages'] as $item )
			{
				if( $item['name'] === 'aimeos/aimeos-laravel' ) {
					return $item['version'];
				}
			}
		}

		return '';
	}
}