<?php
/**
* @version		$Id: view.html.php
* @package		Joomla
* @subpackage	Link article
* @copyright	Copyright (C) 2005 - 2012 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

/**
 * HTML View class for link article component
 *
 * @static
 * @package		Joomla
 * @subpackage	Link article
 * @since 1.5
 */

class KalturaViewAdmin extends JView
{
	function getValue($field)
	{
		$val = JRequest::getVar($field);
		if (!empty($val) && !empty($val[$field]))
		{
			return $val[$field];
		}
		else if (!empty($val))
		{
			return $val;
		}
		else
		{
			return '';
		}
		
	}
	
    function display($tpl = null)
    {
		$this->assignRef( 'username', $this->getValue('username'));
		$this->assignRef( 'company', $this->getValue('company'));
		$this->assignRef( 'email', $this->getValue('email'));
		$this->assignRef( 'phone', $this->getValue('phone'));
		$this->assignRef( 'weburl', $this->getValue('weburl'));
		$this->assignRef( 'descself', $this->getValue('descself'));
		$this->assignRef( 'adult', $this->getValue('adult'));
		$this->assignRef( 'webcontent', $this->getValue('webcontent'));
		$this->assignRef( 'accpt', $this->getValue('accpt'));
		$this->assignRef( 'purpose', $this->getValue('purpose'));
		$this->assignRef( 'ceurl', $this->getValue('ceurl'));
		$this->assignRef( 'ceadminemail', $this->getValue('ceadminemail'));
		$this->assignRef( 'cecmspass', $this->getValue('cecmspass'));
		
		$val = JRequest::getVar("task");
		$val = empty($val) ? "" : $val;
		$this->assignRef( 'task', $val);
        parent::display($tpl);
    }
}
	
