<?php
/**
* @version		$Id: view.html.php
* @package		Joomla
* @subpackage	Link article
* @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
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
class KalturaViewPlaylist extends JView
{
    function display($tpl = null)
    {
        $entryid = JRequest::getVar('entryid');
        $this->assignRef( 'entryid', $entryid );
        $name = JRequest::getVar('name');
        $this->assignRef( 'name', $name );

        parent::display($tpl);
    }
}
	
