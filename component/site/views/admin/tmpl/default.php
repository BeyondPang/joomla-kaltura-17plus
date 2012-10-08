<?php 
  require_once(JPATH_COMPONENT . DS . 'lib.php');
  echo '<link rel="stylesheet" type="text/css" href="' . JURI::root() . 'components/com_kalturavideo/css/kaltura_styles.css"/>';

//	JPlugin::loadLanguage('com_kalturavideo', JPATH_ADMINISTRATOR );		

  JToolBarHelper::title( JText::_( 'KALTURAADMIN' ), 'generic.png' );
  jimport( 'joomla.html.parameter');
  jimport( 'joomla.html.html');
  
  function print_textfield($name, $value)
  {
    $param = new JParameter('');
    $node = new SimpleXMLElement ('<inst/>');
    echo $param->loadElement("text")->fetchElement($name, $value, $node, $name);
  }
  
  function print_textarea($name, $value,$rows,$cols)
  {
     $param = new JParameter('');
    $node = new SimpleXMLElement ('<inst/>');
    $node->addAttribute('rows',$rows);
    $node->addAttribute('cols',$cols);
    echo $param->loadElement("textarea")->fetchElement($name, $value, $node, $name);
 }
  
  function print_select($values, $name, $multiple=false, $selectValue = NULL)
  {
      echo JHTML::_('select.genericlist',  $values, $name . ($multiple ? "[]" : "[".$name."]"), ($multiple ? 'multiple="multiple"' : ""),'key','text', $selectValue);
  }
  
  function print_radio($values, $name, $selected=null)
  {
      echo JHTML::_('select.radiolist',  $values, $name . "[".$name."]", null, 'value', 'text', $selected);
  }
  
  function print_checkbox($text, $name,$check=false)
  {
      echo '<input type="checkbox" name="'.$name.'['.$name.']'.'" id="'.$name.'" class="checkbox" value="yes" alt="'.$text.'" '.($check=="yes" ? "checked=checked":"").' />';
  }
  
  function print_playlist(&$list)
  {
  	JHTML::_('behavior.modal');
    $width = 600;
    $height = 600;
	$i=1;
    
    $players = KalturaHelpers::getSitePlayListsPlayers();
    foreach($players as $pId => $pObj)
    {
      if ($pObj->width > $width) $width = $pObj->width;
      if ($pObj->height > $height) $height = $pObj->height;
    }
    $width += 20;
    echo '<table width="100%"><tr class="pl_head_line"><td align="center" width="5%">#</td><td align="center">'.KalturaHelpers::get_nls('playlists', 'kaltura').'</td></tr>';
    foreach($list as $lId => $lName)
    {
	  $class = ($i % 2 == 0 ?  "pl_even_line" : "pl_odd_line");
      echo '<tr class="' . $class . '">';
      echo '<td align="center">';
      echo $i++;
      echo '</td>';
      echo '<td align="center">';
      echo '<a href="index.php?option=com_kalturavideo&view=playlist&tmpl=component&entryid='.$lId.'&name='.urlencode($lName).'") class="modal" rel="{handler: \'iframe\', size: {x: '.$width.', y: '.$height.'}}">' . $lName /*KalturaHelpers::get_nls('previewembed', 'kaltura')*/. '</a>';
      echo '</td>';
      echo '</tr>';
    }
    echo '</table><br/>';

  }
  
  function formerr($error)
  {
    if (!empty($error)) {
        echo '<span class="error">'. $error .'</span>';
    }
  }
  
  ?>

  <style type="text/css">
  td
  {
  padding-top:20px;
  }
</style>

<form method="post" action="index.php?option=com_kalturavideo" id="form">
  <div>
    <input type="hidden" name="option" value="com_kalturavideo" />                 
    <input type="hidden" name="controller" value="controller" />
    <input type="hidden" name="task" value="register" />


    <?php   
    
    
$errors = false;

   if (!empty($this->task) && !(empty($this->username) || empty($this->email) || empty($this->phone) || empty($this->weburl) ||
       empty($this->webcontent) || empty($this->adult) || empty($this->purpose) || empty($this->accpt)))
   {
    try
    {   
        $str_content = implode ( "," , $this->webcontent );
        $j = new JVersion;
        KalturaHelpers::register($this->username, $this->email, $secret, $adminSecret, $partner, 
                                  $this->phone, $this->purpose, $j->getLongVersion(), $this->descself, 
                                  $this->weburl, $str_content,($this->adult == "Yes" ? true: false));
                                  
        $database = JFactory::getDBO();
        
        $database->setQuery("INSERT INTO #__kaltura_config VALUES('secret','" . $secret ."');");
        $database->query();
        
        $database->setQuery("INSERT INTO #__kaltura_config VALUES('adminsecret','" . $adminSecret ."');");
        $database->query();
        
        $database->setQuery("INSERT INTO #__kaltura_config VALUES('partner_id','" . $partner ."');");
        $database->query();     
        
        $database->setQuery("INSERT INTO #__kaltura_config VALUES('email','" . $this->email ."');");
        $database->query();      
    }
    catch(Exception $exp)
    {
      formerr($exp->getMessage());
    }        
  }
  else if (!empty($this->ceurl) && !empty($this->ceadminemail) && !empty($this->cecmspass))
  {
	try
	{
		KalturaHelpers::importCE($this->ceurl, $this->ceadminemail, $this->cecmspass, $secret, $adminSecret, $partner);
		
		$database = JFactory::getDBO();

		$database->setQuery("INSERT INTO #__kaltura_config VALUES('email','" . $this->ceadminemail ."');");
        $database->query();  
		
        $database->setQuery("INSERT INTO #__kaltura_config VALUES('secret','" . $secret ."');");
        $database->query();
        
        $database->setQuery("INSERT INTO #__kaltura_config VALUES('adminsecret','" . $adminSecret ."');");
        $database->query();
        
        $database->setQuery("INSERT INTO #__kaltura_config VALUES('partner_id','" . $partner ."');");
        $database->query();     
        
        $database->setQuery("UPDATE #__kaltura_config SET value='". $this->ceurl ."'WHERE name='server_uri'");
        $database->query();    		
	}
    catch(Exception $exp)
    {
      formerr($exp->getMessage());
    }        
  }   
if (KalturaHelpers::getPlatformKey("partner_id","none") == "none")
{
  $contOpt = array();
  $i=1;
  $base_name = 'registerebcontent-opt';
  while (strstr(KalturaHelpers::get_nls($base_name . $i, 'kaltura'), $base_name) == FALSE)
  {
    $pair = new stdClass();
    $pair->key=get_option_key($base_name . $i . '-key');
    $pair->text=KalturaHelpers::get_nls($base_name . $i, 'kaltura');
    $contOpt[] = $pair;
    $i++;
  }
  
  $descOpt = array();
  $i=1;
  $base_name = 'registerdescself-opt';
  while (strstr(KalturaHelpers::get_nls($base_name . $i, 'kaltura'), $base_name) == FALSE)
  {
    $pair = new stdClass();
    $pair->key=get_option_key($base_name . $i . '-key');
    $pair->text=KalturaHelpers::get_nls($base_name . $i, 'kaltura');
    $descOpt[] = $pair;
    $i++;
  }

  $adultOpt = array();
  $i=1;
  $base_name = 'registeradult-opt';
  while (strstr(KalturaHelpers::get_nls($base_name . $i, 'kaltura'), $base_name) == FALSE)
  {
     $pair = new stdClass();
    $pair->key=get_option_key($base_name . $i . '-key');
    $pair->text=KalturaHelpers::get_nls($base_name . $i, 'kaltura');
    $pair->value=KalturaHelpers::get_nls($base_name . $i, 'kaltura');
    $adultOpt[] = $pair; 
    $i++;
  }

	JHTML::_('behavior.modal');
  $import_url = "index.php?option=com_kalturavideo&view=importsettings&tmpl=component";
 ?>
    <h2>
      <?php echo KalturaHelpers::get_nls('settingstitle','kaltura'); ?>
    </h2>
    <a class="modal" style="" id="kal_edit" title="import settings" href="<?php echo $import_url?>"  rel="{handler: 'iframe', size: {x: 250, y: 180}}"><?php echo KalturaHelpers::get_nls('existingcustomer','kaltura');?>
      </a>
    <br /><br />
    <?php echo KalturaHelpers::get_nls('settingsmarketing1', 'kaltura');?><br /><br />
    <h4>
      <?php echo KalturaHelpers::get_nls('settingsmarketing2', 'kaltura');?>
    </h4>
 <?php
   if (!empty($this->task) && (empty($this->username) || empty($this->email) || empty($this->phone) || empty($this->weburl) ||
       empty($this->webcontent) || empty($this->adult) || empty($this->purpose) ))
  {
      formerr(KalturaHelpers::get_nls('registrationmandatoryall','kaltura'));
  }
  else if (!empty($this->task) && empty($this->accpt))
  {
      formerr(KalturaHelpers::get_nls('registrationacceptterms','kaltura'));
  }
 
 ?>
	<table>
	<tr>
	<td>
    <table >
      <tr >
        <td>
          <?php echo KalturaHelpers::get_nls('registeruser', 'kaltura'); ?>
        </td>
        <td>
          <?php print_textfield("username", $this->username);?>
        </td>
      </tr>
      <tr>
        <td>
          <?php echo KalturaHelpers::get_nls('registercompany', 'kaltura'); ?>
        </td>
        <td>
          <?php print_textfield("company", $this->company);?>
        </td>
      </tr>
      <tr>
        <td>
          <?php echo KalturaHelpers::get_nls('registeremail', 'kaltura'); ?>
        </td>
        <td>
          <?php print_textfield("email", $this->email);?>
        </td>
      </tr>
      <tr>
        <td>
          <?php echo KalturaHelpers::get_nls('registerphone', 'kaltura'); ?>
        </td>
        <td>
          <?php print_textfield("phone", $this->phone);?>
        </td>
      </tr>
      <tr>
        <td>
          <?php echo KalturaHelpers::get_nls('registerdescself', 'kaltura'); ?>
        </td>
        <td>
          <?php print_select($descOpt, "descself", false, $this->descself); ?>
        </td>
      </tr>
      <tr>
        <td>
          <?php echo KalturaHelpers::get_nls('registerweburl', 'kaltura'); ?>
        </td>
        <td>
          <?php print_textfield("weburl", $this->weburl);?>
        </td>
      </tr>
      <tr>
        <td>
          <?php echo KalturaHelpers::get_nls('registerebcontent', 'kaltura'); ?>
        </td>
        <td>
          <?php print_select($contOpt, "webcontent", true, $this->webcontent); ?>
        </td>
      </tr>
      <tr>
        <td>
          <?php echo KalturaHelpers::get_nls('registeradult', 'kaltura'); ?>
        </td>
        <td>
          <?php print_radio ($adultOpt, "adult",$this->adult); ?>
        </td>
      </tr>
      <tr>
        <td style="vertical-align:text-top">
          <?php echo KalturaHelpers::get_nls('registerpurpose', 'kaltura'); ?>
        </td>
        <td>
          <?php print_textarea("purpose", $this->purpose, 5, 40); ?>
        </td>
      </tr>
      <tr>
        <td style="	vertical-align:text-top">
          <?php print_checkbox(KalturaHelpers::get_nls('accpetterms', 'kaltura'),"accpt",$this->accpt); echo KalturaHelpers::get_nls('acceptkalturaterms','kaltura');?>
        </td>
        <td>
          <br/><br/><br/>
          <input type="submit" value="<?php echo KalturaHelpers::get_nls('signupkaltura', 'kaltura') ?>" />
        </td>
        </tr>
    </table>
	</td>
	<td style="	vertical-align:text-top;padding-left:40px">
		<div style="background-color: #f4f6e0;padding:20px">
		<h4><?php echo KalturaHelpers::get_nls('kalturace_header', 'kaltura'); ?> </h4>
		<?php echo KalturaHelpers::get_nls('kalturace_prefix', 'kaltura'); ?> 
		<table >
		<tr><td style="vertical-align:text-top"><?php echo KalturaHelpers::get_nls('kalturace_url', 'kaltura'); ?></td><td style="vertical-align:text-top"><?php print_textfield("ceurl", "http://"); ?></br></br></td></tr>
		<tr><td style="vertical-align:text-top"><?php echo KalturaHelpers::get_nls('kalturace_admin_mail', 'kaltura'); ?></td><td style="vertical-align:text-top"><?php print_textfield("ceadminemail", $this->ceadminemail); ?></br></br></td></tr>
		<tr><td style="vertical-align:text-top"><?php echo KalturaHelpers::get_nls('kalturace_password', 'kaltura'); ?></td><td style="vertical-align:text-top"><?php print_textfield("cecmspass", $this->cecmspass); ?></br></br></td></tr>		
        <tr><td colspan="2" style="text-align:center" ><input type="submit" value="<?php echo KalturaHelpers::get_nls('submitcekaltura', 'kaltura') ?>" /> </td></tr>
		</table>
		</div>
	</td>
	</tr>
	</table>
    <?php
} //end of non registered user
else 
{
    $kClient = new KalturaClient(KalturaHelpers::getServiceConfiguration());
    $kalturaUser = KalturaHelpers::getPlatformKey("user","");
    $kalturaSecret = KalturaHelpers::getPlatformKey("adminsecret","");
    $ksId = $kClient -> session -> start($kalturaSecret, KalturaHelpers::getSessionUser()->userId, KalturaSessionType::ADMIN);
    $kClient -> setKs($ksId);

    $kalInfo = $kClient -> partner -> getinfo();
    $kalUsage = $kClient -> partner -> getUsage();
    
    $playlists = KalturaHelpers::getSitePlayLists();
    $hasPlayList = (count($playlists) > 0) ;
    if ($kalInfo->partnerPackage == 1)
    {
        if ($kalUsage->Percent == 100)
        {
             echo KalturaHelpers::get_nls('trialexpired','kaltura');
       }
        else
        {
            echo KalturaHelpers::get_nls('usingtrial','kaltura');
            echo KalturaHelpers::get_nls('partner','kaltura') . '&nbsp;' .KalturaHelpers::getPlatformKey("partner_id","") . '<br/><br/>';
            echo KalturaHelpers::get_nls('cmsemail','kaltura') . '&nbsp;' .KalturaHelpers::getPlatformKey("email","") . '<br/>';

            echo '<div id="element-box" style="margin-top:20px">';
            echo '<div class="t">
                  <div class="t">
                    <div class="t"></div>
                  </div>
                </div>';
            echo '<div class="m">';
            echo KalturaHelpers::get_nls('customizehead','kaltura');
            if ($hasPlayList)
            {
              print_playlist($playlists);
              echo KalturaHelpers::get_nls('customizeexisitng','kaltura');
            }
            else
            {
              echo KalturaHelpers::get_nls('customizeempty','kaltura');
            }
            echo '</div>';
            echo '<div class="b">
                  <div class="b">
                    <div class="b"></div>
                  </div>
                </div>';
            echo '</div>';
            echo '<div class="clr"></div>';
            
            echo '<div id="element-box" style="margin-top:20px">';
            echo '<div class="t">
                  <div class="t">
                    <div class="t"></div>
                  </div>
                </div>';
            echo '<div class="m">';
            echo KalturaHelpers::get_nls('accounthead','kaltura');
            echo KalturaHelpers::get_nls('trialpercent1','kaltura') . $kalUsage->Percent . KalturaHelpers::get_nls('trialpercent2','kaltura') ;
            echo KalturaHelpers::get_nls('trialaccount','kaltura');   
            echo '</div>';
            echo '<div class="b">
                  <div class="b">
                    <div class="b"></div>
                  </div>
                </div>';
            echo '</div>';
            echo '<div class="clr"></div>';
            echo KalturaHelpers::get_nls('supporthead','kaltura');    
            echo KalturaHelpers::get_nls('support','kaltura');    
        }
    }
    else
    {

         echo KalturaHelpers::get_nls('partner','kaltura') . '&nbsp;' .KalturaHelpers::getPlatformKey("partner_id","") . '<br/><br/>';
         echo KalturaHelpers::get_nls('cmsemail','kaltura') . '&nbsp;' .KalturaHelpers::getPlatformKey("email","none") . '<br/>';

         echo '<div id="element-box" style="margin-top:20px">';
         echo '<div class="t">
                  <div class="t">
                    <div class="t"></div>
                  </div>
                </div>';
         echo '<div class="m">';
         echo KalturaHelpers::get_nls('customizehead','kaltura');
         if ($hasPlayList)
         {
           print_playlist($playlists);
           echo KalturaHelpers::get_nls('customizeexisitng','kaltura');
         }
         else
         {
           echo KalturaHelpers::get_nls('customizeempty','kaltura');
         }
         echo '</div>';
         echo '<div class="b">
               <div class="b">
                 <div class="b"></div>
               </div>
             </div>';
         echo '</div>';
         echo '<div class="clr"></div>';
         
         echo '<div id="element-box" style="margin-top:20px">';
         echo '<div class="t">
                  <div class="t">
                    <div class="t"></div>
                  </div>
                </div>';
         echo '<div class="m">';
         echo KalturaHelpers::get_nls('accounthead','kaltura');
         echo KalturaHelpers::get_nls('regusage1','kaltura') . $kalUsage->usageGB . KalturaHelpers::get_nls('regusage2','kaltura') ;
         echo '</div>';
         echo '<div class="b">
               <div class="b">
                 <div class="b"></div>
               </div>
             </div>';
         echo '</div>';
         echo '<div class="clr"></div>';

         echo KalturaHelpers::get_nls('supporthead','kaltura');    
         echo KalturaHelpers::get_nls('support','kaltura');    
         echo '<div class="b"/>';
   }
   
?>
    <?php 
}
?>
  </div>

</form>