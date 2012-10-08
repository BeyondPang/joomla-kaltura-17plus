<?php 
  require_once(JPATH_COMPONENT . DS . 'lib.php');

//	JPlugin::loadLanguage('com_kalturavideo', JPATH_ADMINISTRATOR );		

  jimport( 'joomla.html.parameter');
  jimport( 'joomla.html.html');
  
  function print_textfield($name, $value)
  {
    $param = new JParameter('');
    $node = new SimpleXMLElement ('<inst/>');
    echo $param->loadElement("text")->fetchElement($name, $value, $node, $name);
  }
  
  
  function print_passwordfield($name, $value)
  {
    $param = new JParameter('');
    $node = new SimpleXMLElement('<inst/>');
    echo $param->loadElement("password")->fetchElement($name, $value, $node,$name);
  }
  
  if (!empty($this->email))
  {
	print 'trying';
    try
    {
      $kClient = new KalturaClient(KalturaHelpers::getServiceConfiguration());
      $ksId = $kClient->adminUser->login($this->email,$this->password);
      $kClient -> setKs($ksId);

      $kInfo = $kClient -> partner -> getInfo();

      $entry = new stdClass;
      $entry->plugin="kaltura";

      $database = JFactory::getDBO();
      
      $database->setQuery("INSERT INTO #__kaltura_config VALUES('secret','" . $kInfo->secret ."');");
      $database->query();
      
      $database->setQuery("INSERT INTO #__kaltura_config VALUES('adminsecret','" . $kInfo->adminSecret ."');");
      $database->query();
      
      $database->setQuery("INSERT INTO #__kaltura_config VALUES('partner_id','" . $kInfo->id ."');");
      $database->query();
      
      $database->setQuery("INSERT INTO #__kaltura_config VALUES('email','" . $kInfo->adminEmail ."');");
      $database->query();
      
      echo '<script type="text/javascript">window.parent.location.reload(); window.parent.document.getElementById(\'sbox-window\').close();</script>';
    }
    catch(Exception $exp)
    {
      echo $exp->getMessage();
    }
  
  }
?>
<form method="post" action="index.php?option=com_kalturavideo&view=importsettings&tmpl=component" id="form">
  <input type="hidden" name="option" value="com_kalturavideo" />
  <input type="hidden" name="controller" value="controller" />
  <input type="hidden" name="task" value="import" />

  <h4>
    <?php echo KalturaHelpers::get_nls('importlabel', 'kaltura');?>
  </h4>

  <table>
  <tr>
    <td>
      <?php echo KalturaHelpers::get_nls('cmsemail', 'kaltura'); ?>
    </td>
    <td>
      <?php print_textfield("email", "");?>
    </td>
  </tr>
  <tr>
    <td>
      <?php echo KalturaHelpers::get_nls('password', 'kaltura');?>
    </td>
    <td>
        <?php print_passwordfield("password", "");?>
      </td>
  </tr>
  <tr>
    <td colspan="2" style="padding-top:10px;text-align:center">
      <input type="submit" id="id_export" value="<?php echo KalturaHelpers::get_nls('import','kaltura')?>" />
  </td>
  </tr>
</table>
</form>  