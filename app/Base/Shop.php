<?php

namespace App\Base;

class Shop
{
	/**
	 * @var \Aimeos\MShop\Context\Item\Iface
	 */
	private $context;

	/**
	 * @var \Aimeos\MW\View\Iface
	 */
	private $view;

	/**
	 * @var array
	 */
	private $objects = [];


	/**
	 * Initializes the object
	 *
	 * @param \App\Base\Aimeos $aimeos Aimeos object
	 * @param \App\Base\Context $context Context object
	 * @param \App\Base\View $view View object
	 */
	public function __construct( \App\Base\Aimeos $aimeos,
		\App\Base\Context $context, \App\Base\View $view )
	{
		$this->context = $context->get();

		$langid = $this->context->getLocale()->getLanguageId();
		$tmplPaths = $aimeos->get()->getCustomPaths( 'client/html/templates' );

		$this->view = $view->create( $this->context, $tmplPaths, $langid );
		$this->context->setView( $this->view );
	}


	/**
	 * Returns the HTML client for the given name
	 *
	 * @param string $name Name of the shop component
	 * @return \Aimeos\Client\Html\Iface HTML client
	 */
	public function get( string $name ) : \Aimeos\Client\Html\Iface
	{
		if( !isset( $this->objects[$name] ) )
		{
			$client = \Aimeos\Client\Html::create( $this->context, $name );
			$client->setView( clone $this->view );
			$client->process();

			$this->objects[$name] = $client;
		}

		return $this->objects[$name];
	}
}