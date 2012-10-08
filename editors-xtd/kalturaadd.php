<?php
/**
 * @version		$Id: kalturaadd.php 2012-01-28 $
 * @package		Joomla
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

/**
 * Editor Readmore button
 *
 * @package Editors-xtd
 * @since 1.5
 */
class plgButtonKalturaAdd extends JPlugin
{
	/**
	 * Constructor
	 *
	 * @param 	object $subject The object to observe
	 * @param 	array  $config  An array that holds the plugin configuration
	 * @since 1.5
	 */
	 public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}
	
	function plgButtonKalturaAdd(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	/**
	 * readmore button
	 * @return array A two element array of ( imageName, textToInsert )
	 */
	function onDisplay($name)
	{
		$app = JFactory::getApplication();
		
		$doc 	= & JFactory::getDocument();
		$template 	= $app->getTemplate();         	

		$doc->addStyleSheet( JURI::Base() . 'components/com_kalturavideo/css/kaltura_rt.css', 'text/css', null, array() );

		
		JPlugin::loadLanguage('plg_editors-xtd_kalturaadd', JPATH_ADMINISTRATOR );		

		$link = 'index.php?option=com_kalturavideo&view=kaltura&tmpl=component';
		
		JHTML::_('behavior.modal');
		
		$button = new JObject();
		$button->set('modal', true);
		$button->set('link', $link);
		$button->set('text', JText::_('KALTURAADD'));
		$button->set('name', 'kaltura');
		$button->set('options', "{handler: 'iframe', size: {x: 790, y: 440}}");

		return $button;
	}
}