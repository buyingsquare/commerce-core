<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package Client
 * @subpackage Html
 */


/**
 * Default implementation of catalog detail bundle section for HTML clients.
 *
 * @package Client
 * @subpackage Html
 */
class Client_Html_Catalog_Detail_Bundle_Default
	extends Client_Html_Common_Client_Factory_Abstract
	implements Client_Html_Common_Client_Factory_Interface
{
	/** client/html/catalog/detail/bundle/default/subparts
	 * List of HTML sub-clients rendered within the catalog detail bundle section
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
	private $_subPartPath = 'client/html/catalog/detail/bundle/default/subparts';
	private $_subPartNames = array();
	private $_tags = array();
	private $_expire;
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
		$view = $this->_setViewParams( $this->getView(), $tags, $expire );

		$html = '';
		foreach( $this->_getSubClients() as $subclient ) {
			$html .= $subclient->setView( $view )->getBody( $uid, $tags, $expire );
		}
		$view->bundleBody = $html;

		/** client/html/catalog/detail/bundle/default/template-body
		 * Relative path to the HTML body template of the catalog detail bundle client.
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
		 * @see client/html/catalog/detail/bundle/default/template-header
		 */
		$tplconf = 'client/html/catalog/detail/bundle/default/template-body';
		$default = 'catalog/detail/bundle-body-default.html';

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
		$view = $this->_setViewParams( $this->getView(), $tags, $expire );

		$html = '';
		foreach( $this->_getSubClients() as $subclient ) {
			$html .= $subclient->setView( $view )->getHeader( $uid, $tags, $expire );
		}
		$view->bundleHeader = $html;

		/** client/html/catalog/detail/bundle/default/template-header
		 * Relative path to the HTML header template of the catalog detail bundle client.
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
		 * @see client/html/catalog/detail/bundle/default/template-body
		 */
		$tplconf = 'client/html/catalog/detail/bundle/default/template-header';
		$default = 'catalog/detail/bundle-header-default.html';

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
		/** client/html/catalog/detail/bundle/decorators/excludes
		 * Excludes decorators added by the "common" option from the catalog detail bundle html client
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
		 *  client/html/catalog/detail/bundle/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("Client_Html_Common_Decorator_*") added via
		 * "client/html/common/decorators/default" to the html client.
		 *
		 * @param array List of decorator names
		 * @since 2015.08
		 * @category Developer
		 * @see client/html/common/decorators/default
		 * @see client/html/catalog/detail/bundle/decorators/global
		 * @see client/html/catalog/detail/bundle/decorators/local
		 */

		/** client/html/catalog/detail/bundle/decorators/global
		 * Adds a list of globally available decorators only to the catalog detail bundle html client
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("Client_Html_Common_Decorator_*") around the html client.
		 *
		 *  client/html/catalog/detail/bundle/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "Client_Html_Common_Decorator_Decorator1" only to the html client.
		 *
		 * @param array List of decorator names
		 * @since 2015.08
		 * @category Developer
		 * @see client/html/common/decorators/default
		 * @see client/html/catalog/detail/bundle/decorators/excludes
		 * @see client/html/catalog/detail/bundle/decorators/local
		 */

		/** client/html/catalog/detail/bundle/decorators/local
		 * Adds a list of local decorators only to the catalog detail bundle html client
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("Client_Html_Catalog_Decorator_*") around the html client.
		 *
		 *  client/html/catalog/detail/bundle/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "Client_Html_Catalog_Decorator_Decorator2" only to the html client.
		 *
		 * @param array List of decorator names
		 * @since 2015.08
		 * @category Developer
		 * @see client/html/common/decorators/default
		 * @see client/html/catalog/detail/bundle/decorators/excludes
		 * @see client/html/catalog/detail/bundle/decorators/global
		 */

		return $this->_createSubClient( 'catalog/detail/bundle/' . $type, $name );
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
			if( isset( $view->detailProductItem ) && $view->detailProductItem->getType() === 'bundle' )
			{
				$context = $this->_getContext();
				$config = $context->getConfig();
				$domains = array( 'text', 'price', 'media' );
				$products = $view->detailProductItem->getRefItems( 'product', null, 'default' );

				/** client/html/catalog/detail/domains
				 * A list of domain names whose items should be available in the catalog view templates
				 *
				 * @see client/html/catalog/detail/domains
				 */
				$domains = $config->get( 'client/html/catalog/detail/domains', $domains );

				/** client/html/catalog/detail/bundle/domains
				 * A list of domain names whose items should be available in the bundle part of the catalog detail view templates
				 *
				 * The templates rendering bundle related data usually add
				 * the images and texts associated to each item. If you want to
				 * display additional content like the attributes, you can configure
				 * your own list of domains (attribute, media, price, product, text,
				 * etc. are domains) whose items are fetched from the storage.
				 * Please keep in mind that the more domains you add to the
				 * configuration, the more time is required for fetching the content!
				 *
				 * This configuration option can be overwritten by the
				 * "client/html/catalog/detail/domains" configuration option that
				 * allows to configure the domain names of the items fetched
				 * specifically for all types of product listings.
				 *
				 * @param array List of domain names
				 * @since 2015.09
				 * @category Developer
				 * @see client/html/catalog/detail/domains
				 */
				$domains = $config->get( 'client/html/catalog/detail/bundle/domains', $domains );

				$controller = Controller_Frontend_Factory::createController( $context, 'catalog' );

				$view->bundleItems = $controller->getProductItems( array_keys( $products ), $domains );
				$view->bundlePosItems = $products;

				$this->_addMetaItem( $view->bundleItems, 'product', $this->_expire, $this->_tags );
				$this->_addMetaList( array_keys( $view->bundleItems ), 'product', $this->_expire );
			}

			$this->_cache = $view;
		}

		$expire = $this->_expires( $this->_expire, $expire );
		$tags = array_merge( $tags, $this->_tags );

		return $this->_cache;
	}
}