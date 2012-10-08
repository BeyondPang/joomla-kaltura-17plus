<?php
/**
 * @version		$Id: kalturavideo.php
 * @package		Joomla
 * @subpackage	Kaltura Content
 * @copyright	http://www.fsf.org/licensing/licenses/agpl-3.0.html 
 * @license	http://www.fsf.org/licensing/licenses/agpl-3.0.html 
*/


// no direct access
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
 
// Require the base controller
 
require_once( JPATH_COMPONENT.DS.'controller.php' );
 
// Require specific controller if requested
if($controller = JRequest::getVar('controller')) {
    $path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
    if (file_exists($path)) {
        require_once $path;
    } else {
        $controller = '';
    }
}
 
// Create the controller
//$classname    = 'KalturaController'.$controller;
//$controller   = new $classname( );
 
$controller = JController::getInstance('Kaltura');

// Perform the Request task
$controller->execute( JRequest::getCmd( 'task' ) );
 
// Redirect if set by the controller
$controller->redirect();

?>
