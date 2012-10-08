<?php 
  require_once(JPATH_COMPONENT . DS . 'lib.php');
  
  echo '<script type="text/javascript" src="' . JURI::root() . 'components/com_kalturavideo/js/swfobject.js"></script>';
  echo '<script type="text/javascript" src="' . JURI::root() . 'components/com_kalturavideo/js/jquery.js"></script>';
  echo '<link rel="stylesheet" type="text/css" href="' . JURI::root() . 'components/com_kalturavideo/css/kaltura_styles.css"/>';
  echo '<!--[if IE 8]>';
  echo '<link rel="stylesheet" type="text/css" href="' . JURI::root() . 'components/com_kalturavideo/css/kaltura_ie7.css"/>';  
  echo '<![endif]-->';  
  echo '<!--[if IE 7]>';
  echo '<link rel="stylesheet" type="text/css" href="' . JURI::root() . 'components/com_kalturavideo/css/kaltura_ie7.css"/>';  
  echo '<![endif]-->';
  echo '<!--[if IE 6]>';
  echo '<link rel="stylesheet" type="text/css" href="' . JURI::root() . 'components/com_kalturavideo/css/kaltura_ie6.css"/>';  
  echo '<![endif]-->';  
// Report all errors except E_NOTICE
// This is the default value set in php.ini
  error_reporting(E_ALL ^ E_NOTICE);

  echo get_global_vars();
  echo get_cw_properties_pane();
 
echo get_cw_preview_pane();
echo get_cw_props_player("divClip", 400,332);

echo get_cw_wizard("divKalturaCw", 760, 402);

echo get_cw_js_functions("divKalturaCw","divClipProps");
?>