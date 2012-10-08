<?php
/**
 * @version		$Id: controller.php
 * @package		Joomla
 * @subpackage	Linkarticle
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.controller' );

/**
 *Kaltura Component Controller
 *
 * @package		Joomla
 * @subpackage	kaltura
 * @version 1.7
 */
class KalturaController extends JController
{
	/**
	 * Display the view
	 */
	function display()
	{
		global $mainframe;
		$document = &JFactory::getDocument();
		$vType	= $document->getType();
		// Get/Create the view
/*		$viewToUse=JRequest::getCmd( 'view', 'process' );
		$view = &$this->getView($viewToUse, $vType);
		// Display the view*
		$view->display();
		*/
		$cmdView = JRequest::getVar('view');
		if (empty($cmdView))
		{
			$viewToUse=JRequest::getCmd( 'view', 'admin' );
			$view = &$this->getView($viewToUse, $vType);
			// Display the view*
			$view->display();
		}
		else
		{
			parent::display();
		}
	}
}
