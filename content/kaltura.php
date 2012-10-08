<?php
/**
* @version      $Id kaltura.php$
* @package      Joomla
* @copyright    Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
* @license      GNU General Public License, see LICENSE.php
*/

  require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_kalturavideo'.DS.'lib.php');
  
// No direct access allowed to this file
defined('_JEXEC') or die('Restricted access');
 
// Import Joomla! Plugin library file
jimport('joomla.plugin.plugin');
 
//The Content plugin Loadmodule
class plgContentKaltura extends JPlugin
{

		/**
		 * Constructor
		 *
		 * @param object $subject The object to observe
		 * @param object $params  The object that holds the plugin parameters
		 * @since 1.5
		 */
		 public function __construct(& $subject, $config)
		{
			parent::__construct($subject, $config);
			$this->loadLanguage();
			$doc 		=& JFactory::getDocument();

			$doc->addStyleSheet( JURI::Base() . 'components/com_kalturavideo/css/kaltura_rt.css', 'text/css', null, array() );
			$doc->addScript(JURI::Base() . 'components/com_kalturavideo/js/swfobject.js');

			$js = " 
			var kaltura_load_funcs= new Array();
			window.addEvent('domready', KalturaLoad);
			function KalturaLoad() 
			{
				for (i=0; i < kaltura_load_funcs.length; i++)
				{
				   eval(kaltura_load_funcs[i]);
				}
			}
			";

				$doc->addScriptDeclaration($js);
		}
        function plgContentKaltura( &$subject, $params )
        {
                parent::__construct( $subject, $params );

               
        }

/*		public function onContentPrepare($context, &$row, &$params, $page = 0)
		{
				$view = JRequest::getCmd('view');
            	JPlugin::loadLanguage('plg_content_kaltura', JPATH_ADMINISTRATOR );		
               
//				$row->text = kaltura_replace_tags($row->text, $row->created_by);
				$row->text = kaltura_replace_tags($row->text, -1);
				return '';
		}
	*/	
		function onContentBeforeDisplay($context, &$row, &$params, $page=0)
        {
 
				$view = JRequest::getCmd('view');

				if ($view == "article")
				{
                //add your plugin codes here
            		JPlugin::loadLanguage('plg_content_kaltura', JPATH_ADMINISTRATOR );		
               
					$row->text = kaltura_replace_tags($row->text, $row->created_by);
				}
                return '';
                //return a string value. Returned value from this event will be displayed in a placeholder. 
                // Most templates display this placeholder after the article separator. 
        }
    
}
?>