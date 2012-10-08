<?php 
  require_once(JPATH_COMPONENT . DS . 'lib.php');
  
  echo '<script type="text/javascript" src="' . JURI::root() . 'components/com_kalturavideo/js/swfobject.js"></script>';
  echo '<link rel="stylesheet" type="text/css" href="' . JURI::root() . 'components/com_kalturavideo/css/kaltura_styles.css"/>';
  echo '<!--[if IE 7]>';
  echo '<link rel="stylesheet" type="text/css" href="' . JURI::root() . 'components/com_kalturavideo/css/kaltura_ie7.css"/>';  
  echo '<![endif]-->';
  echo '<!--[if IE 6]>';
  echo '<link rel="stylesheet" type="text/css" href="' . JURI::root() . 'components/com_kalturavideo/css/kaltura_ie6.css"/>';  
  echo '<![endif]-->';  
// Report all errors except E_NOTICE
// This is the default value set in php.ini
  error_reporting(E_ALL ^ E_NOTICE);

echo get_se_js_functions(KalturaHelpers::getThumbnailUrl(null, $this->entryid, 140, 105));

echo get_se_wizard("divKalturaSe", 890, 546, $this->entryid);
?>