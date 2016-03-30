<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package Client
 * @subpackage Html
 */


/**
 * Default implementation of standard basket HTML client.
 *
 * @package Client
 * @subpackage Html
 */
class Client_Html_Basket_Standard_Default
	extends Client_Html_Basket_Abstract
	implements Client_Html_Common_Client_Factory_Interface
{
	/** client/html/basket/standard/default/subparts
	 * List of HTML sub-clients rendered within the basket standard section
	 *
	 * The output of the frontend is composed of the code generated by the HTML
	 * clients. Each HTML client can consist of serveral (or none) sub-clients
	 * that are responsible for rendering certain sub-parts of the output. The
	 * sub-clients can contain HTML clients themselves and therefore a
	 * hierarchical tree of HTML clients is composed. Each HTML client creates
	 * the output that is placed inside the container of its parent.
	 *
	 * At first, always the HTML code generated by the parent is printed, then
	 * the HTML code of its sub-clients. The order of the HTML sub-clients
	 * determines the order of the output of these sub-clients inside the parent
	 * container. If the configured list of clients is
	 *
	 *  array( "subclient1", "subclient2" )
	 *
	 * you can easily change the order of the output by reordering the subparts:
	 *
	 *  client/html/<clients>/subparts = array( "subclient1", "subclient2" )
	 *
	 * You can also remove one or more parts if they shouldn't be rendered:
	 *
	 *  client/html/<clients>/subparts = array( "subclient1" )
	 *
	 * As the clients only generates structural HTML, the layout defined via CSS
	 * should support adding, removing or reordering content by a fluid like
	 * design.
	 *
	 * @param array List of sub-client names
	 * @since 2014.03
	 * @category Developer
	 */
	private $_subPartPath = 'client/html/basket/standard/default/subparts';

	/** client/html/basket/standard/detail/name
	 * Name of the detail part used by the basket standard detail client implementation
	 *
	 * Use "Myname" if your class is named "Client_Html_Basket_Standard_Detail_Myname".
	 * The name is case-sensitive and you should avoid camel case names like "MyName".
	 *
	 * @param string Last part of the client class name
	 * @since 2014.03
	 * @category Developer
	 */

	/** client/html/basket/standard/coupon/name
	 * Name of the detail part used by the basket standard coupon client implementation
	 *
	 * Use "Myname" if your class is named "Client_Html_Basket_Standard_Detail_Myname".
	 * The name is case-sensitive and you should avoid camel case names like "MyName".
	 *
	 * @param string Last part of the client class name
	 * @since 2014.03
	 * @category Developer
	 */
	private $_subPartNames = array( 'detail', 'coupon' );
	private $_cache;


	/**
	 * Returns the HTML code for insertion into the body.
	 *
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @param array &$tags Result array for the list of tags that are associated to the output
	 * @param string|null &$expire Result variable for the expiration date of the output (null for no expiry)
	 * @return string HTML code
	 */
	public function getBody( $uid = '', array &$tags = array(), &$expire = null )
	{
		$context = $this->_getContext();
		$view = $this->getView();

		try
		{
			$view = $this->_setViewParams( $view, $tags, $expire );

			$html = '';
			foreach( $this->_getSubClients() as $subclient ) {
				$html .= $subclient->setView( $view )->getBody( $uid, $tags, $expire );
			}
			$view->standardBody = $html;
		}
		catch( Client_Html_Exception $e )
		{
			$error = array( $this->_getContext()->getI18n()->dt( 'client/html', $e->getMessage() ) );
			$view->standardErrorList = $view->get( 'standardErrorList', array() ) + $error;
		}
		catch( Controller_Frontend_Exception $e )
		{
			$error = array( $this->_getContext()->getI18n()->dt( 'controller/frontend', $e->getMessage() ) );
			$view->standardErrorList = $view->get( 'standardErrorList', array() ) + $error;
		}
		catch( MShop_Exception $e )
		{
			$error = array( $this->_getContext()->getI18n()->dt( 'mshop', $e->getMessage() ) );
			$view->standardErrorList = $view->get( 'standardErrorList', array() ) + $error;
		}
		catch( Exception $e )
		{
			$context->getLogger()->log( $e->getMessage() . PHP_EOL . $e->getTraceAsString() );

			$error = array( $context->getI18n()->dt( 'client/html', 'A non-recoverable error occured' ) );
			$view->standardErrorList = $view->get( 'standardErrorList', array() ) + $error;
		}

		/** client/html/basket/standard/default/template-body
		 * Relative path to the HTML body template of the basket standard client.
		 *
		 * The template file contains the HTML code and processing instructions
		 * to generate the result shown in the body of the frontend. The
		 * configuration string is the path to the template file relative
		 * to the layouts directory (usually in client/html/layouts).
		 *
		 * You can overwrite the template file configuration in extensions and
		 * provide alternative templates. These alternative templates should be
		 * named like the default one but with the string "default" replaced by
		 * an unique name. You may use the name of your project for this. If
		 * you've implemented an alternative client class as well, "default"
		 * should be replaced by the name of the new class.
		 *
		 * @param string Relative path to the template creating code for the HTML page body
		 * @since 2014.03
		 * @category Developer
		 * @see client/html/basket/standard/default/template-header
		 */
		$tplconf = 'client/html/basket/standard/default/template-body';
		$default = 'basket/standard/body-default.html';

		return $view->render( $this->_getTemplate( $tplconf, $default ) );
	}


	/**
	 * Returns the HTML string for insertion into the header.
	 *
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @param array &$tags Result array for the list of tags that are associated to the output
	 * @param string|null &$expire Result variable for the expiration date of the output (null for no expiry)
	 * @return string|null String including HTML tags for the header on error
	 */
	public function getHeader( $uid = '', array &$tags = array(), &$expire = null )
	{
		try
		{
			$view = $this->_setViewParams( $this->getView(), $tags, $expire );

			$html = '';
			foreach( $this->_getSubClients() as $subclient ) {
				$html .= $subclient->setView( $view )->getHeader( $uid, $tags, $expire );
			}
			$view->standardHeader = $html;
		}
		catch( Exception $e )
		{
			$this->_getContext()->getLogger()->log( $e->getMessage() . PHP_EOL . $e->getTraceAsString() );
			return '';
		}

		/** client/html/basket/standard/default/template-header
		 * Relative path to the HTML header template of the basket standard client.
		 *
		 * The template file contains the HTML code and processing instructions
		 * to generate the HTML code that is inserted into the HTML page header
		 * of the rendered page in the frontend. The configuration string is the
		 * path to the template file relative to the layouts directory (usually
		 * in client/html/layouts).
		 *
		 * You can overwrite the template file configuration in extensions and
		 * provide alternative templates. These alternative templates should be
		 * named like the default one but with the string "default" replaced by
		 * an unique name. You may use the name of your project for this. If
		 * you've implemented an alternative client class as well, "default"
		 * should be replaced by the name of the new class.
		 *
		 * @param string Relative path to the template creating code for the HTML page head
		 * @since 2014.03
		 * @category Developer
		 * @see client/html/basket/standard/default/template-body
		 */
		$tplconf = 'client/html/basket/standard/default/template-header';
		$default = 'basket/standard/header-default.html';

		return $view->render( $this->_getTemplate( $tplconf, $default ) );
	}


	/**
	 * Returns the sub-client given by its name.
	 *
	 * @param string $type Name of the client type
	 * @param string|null $name Name of the sub-client (Default if null)
	 * @return Client_Html_Interface Sub-client object
	 */
	public function getSubClient( $type, $name = null )
	{
		/** client/html/basket/standard/decorators/excludes
		 * Excludes decorators added by the "common" option from the basket standard html client
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "client/html/common/decorators/default" before they are wrapped
		 * around the html client.
		 *
		 *  client/html/basket/standard/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("Client_Html_Common_Decorator_*") added via
		 * "client/html/common/decorators/default" to the html client.
		 *
		 * @param array List of decorator names
		 * @since 2014.05
		 * @category Developer
		 * @see client/html/common/decorators/default
		 * @see client/html/basket/standard/decorators/global
		 * @see client/html/basket/standard/decorators/local
		 */

		/** client/html/basket/standard/decorators/global
		 * Adds a list of globally available decorators only to the basket standard html client
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("Client_Html_Common_Decorator_*") around the html client.
		 *
		 *  client/html/basket/standard/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "Client_Html_Common_Decorator_Decorator1" only to the html client.
		 *
		 * @param array List of decorator names
		 * @since 2014.05
		 * @category Developer
		 * @see client/html/common/decorators/default
		 * @see client/html/basket/standard/decorators/excludes
		 * @see client/html/basket/standard/decorators/local
		 */

		/** client/html/basket/standard/decorators/local
		 * Adds a list of local decorators only to the basket standard html client
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("Client_Html_Basket_Decorator_*") around the html client.
		 *
		 *  client/html/basket/standard/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "Client_Html_Basket_Decorator_Decorator2" only to the html client.
		 *
		 * @param array List of decorator names
		 * @since 2014.05
		 * @category Developer
		 * @see client/html/common/decorators/default
		 * @see client/html/basket/standard/decorators/excludes
		 * @see client/html/basket/standard/decorators/global
		 */

		return $this->_createSubClient( 'basket/standard/' . $type, $name );
	}


	/**
	 * Sets the necessary parameter values in the view.
	 */
	public function process()
	{
		$view = $this->getView();
		$context = $this->_getContext();

		try
		{
			/** client/html/basket/standard/require-stock
			 * Customers can order products only if there are enough products in stock
			 *
			 * @deprecated Use "client/html/basket/require-stock" instead
			 * @see client/html/basket/require-stock
			 */
			$reqstock = $view->config( 'client/html/basket/standard/require-stock', true );

			/** client/html/basket/standard/require-variant
			 * A variant of a selection product must be chosen
			 *
			 * @deprecated Use "client/html/basket/require-variant" instead
			 * @see client/html/basket/require-variant
			 */
			$reqvariant = $view->config( 'client/html/basket/standard/require-variant', true );

			$options = array(

				/** client/html/basket/require-stock
				 * Customers can order products only if there are enough products in stock
				 *
				 * Checks that the requested product quantity is in stock before
				 * the customer can add them to his basket and order them. If there
				 * are not enough products available, the customer will get a notice.
				 *
				 * @param boolean True if products must be in stock, false if products can be sold without stock
				 * @since 2014.03
				 * @category Developer
				 * @category User
				 */
				'stock' => $view->config( 'client/html/basket/require-stock', $reqstock ),

				/** client/html/basket/require-variant
				 * A variant of a selection product must be chosen
				 *
				 * Selection products normally consist of several article variants and by default
				 * exactly one article variant of a selection product can be put into the basket.
				 *
				 * By setting this option to false, the selection product including the chosen
				 * attributes (if any attribute values were selected) can be put into the basket
				 * as well. This makes it possible to get all articles or a subset of articles
				 * (e.g. all of a color) at once.
				 *
				 * @param boolean True if a variant must be chosen, false if also the selection product with attributes can be added
				 * @since 2014.03
				 * @category Developer
				 * @category User
				 */
				'variant' => $view->config( 'client/html/basket/require-variant', $reqvariant ),
			);

			switch( $view->param( 'b_action' ) )
			{
				case 'add':
					$this->_addProducts( $view, $options );
					break;
				case 'delete':
					$this->_deleteProducts( $view );
					break;
				default:
					$this->_editProducts( $view, $options );
			}

			parent::process();

			$controller = Controller_Frontend_Factory::createController( $context, 'basket' );
			$controller->get()->check( MShop_Order_Item_Base_Abstract::PARTS_PRODUCT );
		}
		catch( Client_Html_Exception $e )
		{
			$error = array( $context->getI18n()->dt( 'client/html', $e->getMessage() ) );
			$view->standardErrorList = $view->get( 'standardErrorList', array() ) + $error;
		}
		catch( Controller_Frontend_Exception $e )
		{
			$error = array( $context->getI18n()->dt( 'controller/frontend', $e->getMessage() ) );
			$view->standardErrorList = $view->get( 'standardErrorList', array() ) + $error;
		}
		catch( MShop_Plugin_Provider_Exception $e )
		{
			$errors = array( $context->getI18n()->dt( 'mshop', $e->getMessage() ) );
			$errors = array_merge( $errors, $this->_translatePluginErrorCodes( $e->getErrorCodes() ) );

			$view->summaryErrorCodes = $e->getErrorCodes();
			$view->standardErrorList = $view->get( 'standardErrorList', array() ) + $errors;
		}
		catch( MShop_Exception $e )
		{
			$error = array( $context->getI18n()->dt( 'mshop', $e->getMessage() ) );
			$view->standardErrorList = $view->get( 'standardErrorList', array() ) + $error;
		}
		catch( Exception $e )
		{
			$context->getLogger()->log( $e->getMessage() . PHP_EOL . $e->getTraceAsString() );

			$error = array( $context->getI18n()->dt( 'client/html', 'A non-recoverable error occured' ) );
			$view->standardErrorList = $view->get( 'standardErrorList', array() ) + $error;
		}
	}


	/**
	 * Returns the list of sub-client names configured for the client.
	 *
	 * @return array List of HTML client names
	 */
	protected function _getSubClientNames()
	{
		return $this->_getContext()->getConfig()->get( $this->_subPartPath, $this->_subPartNames );
	}


	/**
	 * Sets the necessary parameter values in the view.
	 *
	 * @param MW_View_Interface $view The view object which generates the HTML output
	 * @param array &$tags Result array for the list of tags that are associated to the output
	 * @param string|null &$expire Result variable for the expiration date of the output (null for no expiry)
	 * @return MW_View_Interface Modified view object
	 */
	protected function _setViewParams( MW_View_Interface $view, array &$tags = array(), &$expire = null )
	{
		if( !isset( $this->_cache ) )
		{
			$context = $this->_getContext();
			$params = $context->getSession()->get( 'arcavias/catalog/detail/params/last', array() );

			$target = $view->config( 'client/html/catalog/detail/url/target' );
			$controller = $view->config( 'client/html/catalog/detail/url/controller', 'catalog' );
			$action = $view->config( 'client/html/catalog/detail/url/action', 'detail' );
			$config = $view->config( 'client/html/catalog/detail/url/config', array() );

			$view->standardParams = $this->_getClientParams( $view->param() );
			$view->standardBackUrl = $view->url( $target, $controller, $action, $params, array(), $config );
			$view->standardBasket = Controller_Frontend_Factory::createController( $context, 'basket' )->get();

			$this->_cache = $view;
		}

		return $this->_cache;
	}


	/**
	 * Adds the products specified by the view parameters to the basket.
	 *
	 * @param MW_View_Interface $view View object
	 * @param array $options List of options for addProducts() in basket controller
	 */
	protected function _addProducts( MW_View_Interface $view, array $options )
	{
		$this->_clearCached();
		$products = (array) $view->param( 'b_prod', array() );
		$controller = Controller_Frontend_Factory::createController( $this->_getContext(), 'basket' );

		if( ( $prodid = $view->param( 'b_prodid', '' ) ) !== '' )
		{
			$products[] = array(
				'prodid' => $prodid,
				'quantity' => $view->param( 'b_quantity', 1 ),
				'attrvarid' => array_filter( (array) $view->param( 'b_attrvarid', array() ) ),
				'attrconfid' => array_filter( (array) $view->param( 'b_attrconfid', array() ) ),
				'attrhideid' => array_filter( (array) $view->param( 'b_attrhideid', array() ) ),
				'attrcustid' => array_filter( (array) $view->param( 'b_attrcustid', array() ) ),
				'warehouse' => $view->param( 'b_warehouse', 'default' ),
			);
		}

		foreach( $products as $values ) {
			$this->_addProduct( $controller, $values, $options );
		}
	}


	/**
	 * Adds a single product specified by its values to the basket.
	 *
	 * @param Controller_Frontend_Interface $controller Basket frontend controller
	 * @param array $values Associative list of key/value pairs from the view specifying the product
	 * @param array $options List of options for addProducts() in basket frontend controller
	 */
	protected function _addProduct( Controller_Frontend_Interface $controller, array $values, array $options )
	{
		$controller->addProduct(
			( isset( $values['prodid'] ) ? (string) $values['prodid'] : '' ),
			( isset( $values['quantity'] ) ? (int) $values['quantity'] : 1 ),
			$options,
			( isset( $values['attrvarid'] ) ? array_filter( (array) $values['attrvarid'] ) : array() ),
			( isset( $values['attrconfid'] ) ? array_filter( (array) $values['attrconfid'] ) : array() ),
			( isset( $values['attrhideid'] ) ? array_filter( (array) $values['attrhideid'] ) : array() ),
			( isset( $values['attrcustid'] ) ? array_filter( (array) $values['attrcustid'] ) : array() ),
			( isset( $values['warehouse'] ) ? (string) $values['warehouse'] : 'default' )
		);
	}


	/**
	 * Removes the products specified by the view parameters from the basket.
	 *
	 * @param MW_View_Interface $view View object
	 */
	protected function _deleteProducts( MW_View_Interface $view )
	{
		$this->_clearCached();
		$products = (array) $view->param( 'b_position', array() );
		$controller = Controller_Frontend_Factory::createController( $this->_getContext(), 'basket' );

		foreach( $products as $position ) {
			$controller->deleteProduct( $position );
		}
	}


	/**
	 * Edits the products specified by the view parameters to the basket.
	 *
	 * @param MW_View_Interface $view View object
	 * @param array $options List of options for editProducts() in basket controller
	 */
	protected function _editProducts( MW_View_Interface $view, array $options )
	{
		$this->_clearCached();
		$products = (array) $view->param( 'b_prod', array() );
		$controller = Controller_Frontend_Factory::createController( $this->_getContext(), 'basket' );

		if( ( $position = $view->param( 'b_position', '' ) ) !== '' )
		{
			$products[] = array(
				'position' => $position,
				'quantity' => $view->param( 'b_quantity', 1 ),
				'attrconf-code' => array_filter( (array) $view->param( 'b_attrconfcode', array() ) )
			);
		}

		foreach( $products as $values )
		{
			$controller->editProduct(
				( isset( $values['position'] ) ? (int) $values['position'] : 0 ),
				( isset( $values['quantity'] ) ? (int) $values['quantity'] : 1 ),
				$options,
				( isset( $values['attrconf-code'] ) ? array_filter( (array) $values['attrconf-code'] ) : array() )
			);
		}
	}
}