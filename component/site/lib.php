<?php


require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_kalturavideo'.DS.'client/kaltura_settings.php');
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_kalturavideo'.DS.'client/KalturaClientBase.php');
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_kalturavideo'.DS.'client/KalturaClient.php');
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_kalturavideo'.DS.'client/kaltura_logger.php');
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_kalturavideo'.DS.'client/kaltura_helpers.php');
/*
require_js($CFG->wwwroot.'/mod/kaltura/js/jquery.js');
require_js($CFG->wwwroot.'/mod/kaltura/js/kvideo.js');
require_js($CFG->wwwroot.'/mod/kaltura/js/swfobject.js');
*/
class KalturaPlayerSize
{
    const LARGE = 1;
    const SMALL = 2;
    const CUSTOM = 3;
}

function get_global_vars()
{
	$javastring = '
    	<script type="text/javascript">
	    var MIX_TYPE = ' . KalturaEntryType::MIX .';
	    var CLIP_TYPE = ' . KalturaEntryType::MEDIA_CLIP .';
        var RATIO_3_4 = ' . KalturaAspectRatioType::ASPECT_4_3 .';
        var RATIO_9_16 = ' . KalturaAspectRatioType::ASPECT_16_9 .';
        var cwRegular = "' .   KalturaHelpers::getContributionWizardUrl(KalturaEntryType::MEDIA_CLIP) . '";
        var cwMix = "' .   KalturaHelpers::getContributionWizardUrl(KalturaEntryType::MIX) . '";
        var entryType = CLIP_TYPE;	
        var aspectRatio = RATIO_3_4;
        var entryId = "";
        var local_entry_id = "";
        var thumbUrl = "' . KalturaHelpers::getThumbnailUrl(null, "#entryid#", "#width#", "#height#") .  '/uiconf/#uiconf#/entrytype/#entrytype#";
        </script>
	';
	
	return $javastring;
}

function get_cw_wizard($div, $width, $height)
{
  $client = KalturaHelpers::getKalturaClient();
  $clipFlashVarsStr = KalturaHelpers::flashVarsToString(KalturaHelpers::getContributionWizardFlashVars($client->getKS(),KalturaEntryType::MEDIA_CLIP));
  $mixFlashVarsStr = KalturaHelpers::flashVarsToString(KalturaHelpers::getContributionWizardFlashVars($client->getKS(),KalturaEntryType::MIX));
  
  $flash_embed = '
    <script type="text/javascript">
        function ShowKalturaContributionWizard()
        {
		    var swfUrl = cwRegular;
		    var wdt = "400";
		    var hgt = "365";
  		
		    if (entryType == MIX_TYPE)
		    {
			    swfUrl = cwMix;
		    }
  			 
		    if (document.getElementById("chkLarge").checked)
		    {
			    wdt = document.getElementById("largeWidth").innerHTML;
			    hgt = "365";
		    }
		    if (document.getElementById("chkSmall").checked)
		    {
			    wdt = document.getElementById("smallWidth").innerHTML;
			    hgt = "260";
		    }
		    if (document.getElementById("chkCustom").checked)
		    {
			    wdt = document.getElementById("inpWidth").value;
          if (aspectRatio == RATIO_3_4)
          {
			      hgt = parseInt(parseInt(wdt,10)*3/4 + 65,10);
          }
          else
          {
			      hgt = parseInt(parseInt(wdt,10)*9/16 + 65,10);
          }
		    }
		    thumbUrl = thumbUrl.replace("#width#", wdt);
		    thumbUrl = thumbUrl.replace("#height#", hgt);
        document.getElementById("' . $div . '").className = "";
        document.getElementById("divWizardHeader").innerHTML = "' . KalturaHelpers::get_nls("addvideo_2nd_pane", "kaltura") . '";
			
		    var kso = new SWFObject(swfUrl, "KalturaCW", "'. $width .'", "'. $height .'", "9", "#ffffff");
        if (entryType == MIX_TYPE)
        {
          kso.addParam("flashVars","'. $mixFlashVarsStr . '");
        }
        else
        {
  		    kso.addParam("flashVars","'. $clipFlashVarsStr . '");
        }
/*		    kso.addParam("flashVars", (entryType == MIX_TYPE ? "'. $mixFlashVarsStr .'" : "' . $clipFlashVarsStr. '"));*/
		    kso.addParam("allowScriptAccess", "always");
		    kso.addParam("allowFullScreen", "TRUE");
		    kso.addParam("allowNetworking", "all");
		    if(kso.installedVer.major >= 9) 
		    {
				kso.write("' . $div . '");
			} 
			else 
			{
				document.getElementById("' . $div . '").innerHTML = "Flash player version 9 and above is required. <a href=\"http://get.adobe.com/flashplayer/\">Upgrade your flash version</a>";
			}

		}      
    </script>
  ';
  
    return $flash_embed;
}    

function get_cw_js_functions($divCW, $divProps='')
{
 // $defaultPlayer = KalturaHelpers::getDefaultPlayer(KalturaEntryType::MEDIA_CLIP);
  
 $javascript = '<script type="text/javascript">
    local_entry_id = "";
    
    function change_entry_player()
    {
      var design = document.getElementById("slctDesign");
    
      show_entry_player(local_entry_id, design.options[design.selectedIndex].value);
    }
    
    function insert_into_post()
    {
		  var tag;
      var design = document.getElementById("slctDesign");
      var title = document.getElementById("inpTitle").value;

		  thumbUrl = thumbUrl.replace("#entryid#", local_entry_id);
		  thumbUrl = thumbUrl.replace("#uiconf#", design.options[design.selectedIndex].value);
		  thumbUrl = thumbUrl.replace("#entrytype#", entryType);
		  tag = "<br/><span class=\"movie-title\">"+title+"</span><br/><img src=\"" + thumbUrl + "\" />";
	    window.parent.jInsertEditorText(tag, "jform_articletext");

//	    window.parent.document.getElementById(\'sbox-window\').close();
	    window.parent.parent.SqueezeBox.close();
    }
    
    function onContributionWizardAfterAddEntry(param) 
    {
       set_note(entryType);
      if (entryType == MIX_TYPE)
      {
        var entries = "";
  //      var name = get_field("id_name");
        var user = ' . KalturaHelpers::getSessionUser()->userId . ';

        document.getElementById("' . $divCW . '").style.display = "none";
        document.getElementById("divUserSlected").style.visibility = "hidden";
        document.getElementById("' . $divProps . '").style.display = "block";
        for (i=0; i < param.length; i++)
        {
            entryId = (param[i].uniqueID == null ? param[i].entryId : param[i].uniqueID);
            entries += entryId + ",";
        }
 				$.ajax({ 
				  type: "POST", 
				  url: "'.JURI::root().'index.php?option=com_kalturavideo&view=createmix&format=raw", 
				  data: "entries="+entries+ "&user=" + user, 
				  success: function(msg)
          { 
            if (msg.substr(0,2) == "y:")
            {
              entryId = msg.substr(2);
              local_entry_id = entryId;
              change_entry_player();
            }
            else
            {
              alert(msg.substr(2));
            }
          },
          error: function(msg)
          {
          }
				});           
      }
      else if (entryType == CLIP_TYPE)
      {
        entryId = (param[0].uniqueID == null ? param[0].entryId : param[0].uniqueID);
        if (\''. $divProps .'\'!= \'\')
        {
          document.getElementById("' . $divCW . '").style.display = "none";
          document.getElementById("divUserSlected").style.visibility = "hidden";
          document.getElementById("' . $divProps . '").style.display = "block";
          local_entry_id = entryId;
          change_entry_player();
        }

      }
    }
    
    function onContributionWizardClose(modified) 
    {
      if (modified[0] == 0)
      {
     //   setTimeout("window.parent.kalturaCloseModalBox();",0); 
      }
    }
    
    </script>';
    
    return $javascript;
}

function get_cw_props_player($div, $width, $height)
{
  $partnerId= KalturaHelpers::getPlatformKey("partner_id","0");
  $swfUrl = KalturaHelpers::getSwfUrlForWidget($partnerId);
  $swfUrl .=  "/uiconf_id/";
  
  $flash_embed = '
    <script type="text/javascript">
    function onKdpReady(div)
    {
        document.getElementById("divUserSlected").style.visibility = "visible"; 
		if (document.getElementById("idNoteMsg").innerHTML != "")
		{
			document.getElementById("idNoteMsg").style.visibility = "visible"; 
		}
    }
    
    function show_entry_player(entryId, design)
    {
      var kso = new SWFObject("'. $swfUrl .'" + design + "/entry_id/" + entryId, "' . $div. '", "'. $width .'", "'. $height .'", "9", "#ffffff");
      kso.addParam("allowScriptAccess", "always");
      kso.addParam("allowFullScreen", "TRUE");
      kso.addParam("allowNetworking", "all");
      kso.addParam("flashVars", "emptyF=onKdpReady&readyF=onKdpReady");
      if(kso.installedVer.major >= 9) {
        kso.write("' . $div . '");
      } else {
        document.getElementById("' . $div . '").innerHTML = "Flash player version 9 and above is required. <a href=\"http://get.adobe.com/flashplayer/\">Upgrade your flash version</a>";
      }
    }
    </script>
  ';
  
    return $flash_embed;
}    

function get_cw_properties_pane()
{
$javascript ='
    	<script type="text/javascript">
        function switchVidType(type)
        {
			    entryType = type;
			    document.getElementById("typeVidNorm").style.display = (type == CLIP_TYPE ? "block" : "none");
			    document.getElementById("typeVidEdit").style.display = (type ==  MIX_TYPE ? "block" : "none");
			    document.getElementById("inpVidNorm").className = (type == CLIP_TYPE ? "input selected" : "input");
			    document.getElementById("inpVidEdit").className = (type ==  MIX_TYPE ? "input selected" : "input");
			    document.getElementById("chkVidNormal").checked = (type ==  MIX_TYPE ? false : true);
			    document.getElementById("chkVidEdit").checked = (type ==  CLIP_TYPE ? false : true);
        }
        
        function SwitchAspect(aspect)
        {
          aspectRatio = aspect;
			    document.getElementById("aspctNorm").className = (aspect == RATIO_3_4 ? "selected" : "");
			    document.getElementById("aspctWide").className = (aspect == RATIO_9_16 ? "selected" : "");
			    document.getElementById("largeWidth").innerHTML = (aspect == RATIO_9_16 ? "533" : "400");
			    document.getElementById("smallWidth").innerHTML = (aspect == RATIO_9_16 ? "346" : "260");
        }

        function Cancel()
        {
			    window.parent.document.getElementById(\'sbox-window\').close();
        }
      </script>
      <form>
      <span id="divWizardHeader" class="kaltura_componentheading">' . KalturaHelpers::get_nls("addvideo", "kaltura") . '</span>
		  <div id="divKalturaCw" class="video-wrap">
      <div class="type">
	      <h2 class="kaltura_contentheading">' . KalturaHelpers::get_nls("CLIPTYPE", "kaltura") . '</h2>
		      <div class="normal">
			      <div id="inpVidNorm" class="input selected" onClick="switchVidType(CLIP_TYPE);"><input id="chkVidNormal" name="vidType" type="radio" value="normal"  checked>Normal</div>
		      </div>
		      <div class="editable">
			      <div id="inpVidEdit" class="input" onClick="switchVidType(MIX_TYPE);"><input name="vidType" id="chkVidEdit" type="radio" value="editable">Mix</div>
		      </div>
		      <div id="typeVidNorm" class="description-top">' . KalturaHelpers::get_nls("nomixdetail", "kaltura") . '</div>
		      <div id="typeVidEdit" class="description-bottom" style="display:none">' . KalturaHelpers::get_nls("mixdetail", "kaltura") . '</div>
      </div>

      <div class="aspect-ratio">
	      <h2 class="kaltura_contentheading">' . KalturaHelpers::get_nls("ASPECTRATIO", "kaltura") . '</h2>
	      <div class="normal">
		      <a class="selected" id="aspctNorm" href="#" onclick="SwitchAspect(RATIO_3_4);">' . KalturaHelpers::get_nls("NORMAL", "kaltura") . '<br/>
		      4:3</a>
	      </div>
	      <div class="widescreen">
		      <a id="aspctWide" href="#" onclick="SwitchAspect(RATIO_9_16);">' . KalturaHelpers::get_nls("WIDESCREEN", "kaltura") . '<br/>
		      16:9</a>
	      </div>
      </div>

      <div class="size">
	      <h2 class="kaltura_contentheading">' . KalturaHelpers::get_nls("SIZE", "kaltura") . '</h2>
	      <span class="radios">
	      <input name="vidSize"  id="chkLarge" class="radio" type="radio" value="large" checked>' . KalturaHelpers::get_nls("LARGEPLAYER", "kaltura") . '<br/>
	      <input name="vidSize" id="chkSmall" class="radio" type="radio" value="small">' . KalturaHelpers::get_nls("SMALLPLAYER", "kaltura") . '<br/>
	      <input name="vidSize" id="chkCustom" class="radio" type="radio" value="custom">' . KalturaHelpers::get_nls("CUSTOM", "kaltura") . '
	      <div class="custom">
		      ' . KalturaHelpers::get_nls("WIDTH", "kaltura") . ' <input  id="inpWidth" type="text"/>
	      </div>
	      </span>
      </div>

      <div class="buttons">
	      <button onclick="ShowKalturaContributionWizard()" class="next" style="white-space:nowrap;" value="' . KalturaHelpers::get_nls("next", "kaltura") . '">' . KalturaHelpers::get_nls("next", "kaltura") . '</button>
	      <button onclick="Cancel();" class="cancel" value="' . KalturaHelpers::get_nls("cancelpost", "kaltura") . '">' . KalturaHelpers::get_nls("cancelpost", "kaltura") . '</button>
      </div>
      </div>
      </form>
';

	return $javascript;
}

function get_cw_preview_pane()
{
  $designesMix = KalturaHelpers::getDesigns(KalturaEntryType::MIX);
  $designesClip = KalturaHelpers::getDesigns(KalturaEntryType::MEDIA_CLIP);
  $mixScript = "";
  $clipScript = "";
  
  $javascript= '
    <script type="text/javascript">
    function set_note(entryType)
    { ';
    if (count($designesMix) > 4) // we have 4 default players so only if we have custom we need the warning message
    {
      $javascript .= 'if (entryType == MIX_TYPE) 
        {
          document.getElementById("idNoteMsg").innerHTML="' . KalturaHelpers::get_nls("notemix", "kaltura") . '";
 //          document.getElementById("idNoteMsg").style.visibility="visible";
       }
      ';
    }
    if (count($designesClip) > 4) 
    {
      $javascript .= 'if (entryType == CLIP_TYPE) 
        {
          document.getElementById("idNoteMsg").innerHTML="' . KalturaHelpers::get_nls("notesingle", "kaltura") . '";           
//		  document.getElementById("idNoteMsg").style.visibility="visible";
        }
      ';    
    }
    $javascript .= 'var design = document.getElementById("slctDesign");
      if (entryType == MIX_TYPE)
      {';
      $count = 0;
      foreach ($designesMix as $desKey => $desValue)
      {                    
          $javascript .= 'design.options['.$count++.'] = new Option("'.$desValue.'","'.$desKey.'",false,false);';
      }
      $javascript .= '}
      else if (entryType == CLIP_TYPE)
      {';
      $count = 0;      
      foreach ($designesClip as $desKey => $desValue)
      {  
        $javascript .= 'design.options['.$count++.'] = new Option("'.$desValue.'","'.$desKey.'",false,false);';
      }
      $javascript .= '}
//      document.getElementById("slctDesign").innerHTML= ( entryType == CLIP_TYPE ? clipScript : mixScript);    
      document.getElementById("divWizardHeader").innerHTML = "' . KalturaHelpers::get_nls("addvideo_3rd_pane", "kaltura") . '"; 
    }
    </script>
    <div id="divClipProps" class="video-wrap" style="display:none">
        <div id="divClip">
        </div>
        <div id="divUserSlected" class="user_selected">
            <div id="divTitle" style="width: 350px;">        
                 <p><span class="kaltura_contentheading">' . KalturaHelpers::get_nls("title", "kaltura") . '</span>
                <input id="inpTitle" title="Title:" type="text" value=""/></p>
            </div>    
            <div id="divDesign" style="width: 350px; margin-top: 20px">
                <p><span class="kaltura_contentheading">' . KalturaHelpers::get_nls("playerdesign", "kaltura") . '</span>
                <select id="slctDesign" name="slctDesign" onchange="change_entry_player();"></select></p>
            </div>
            <div class="note" id="idNoteMsg" style="visibility:hidden"></div>
      <div id="kcwEndButtons" class="buttons">
	      <button id="btnInserResource" onclick="insert_into_post()" class="next" style="white-space:nowrap;" value="' . KalturaHelpers::get_nls("insertintopost", "kaltura") . '">' . KalturaHelpers::get_nls("insertintopost", "kaltura") . '</button>
	      <button id="btnCancelResource" onclick="Cancel();" class="cancel" value="' . KalturaHelpers::get_nls("cancelpost", "kaltura") . '">' . KalturaHelpers::get_nls("cancelpost", "kaltura") . '</button>
      </div>
            
     </div>
    </div>';
    
    return $javascript;
}

/*
 * helper function that is called in nodeAPI:alter
 *
 * this function gets the content to be displayed,
 * and returns the content with an embed tag instead of a "kaltura tag"
 *
 * some of the return content is a javascript with handler functions for the buttons on the player
 */
function kaltura_replace_tags($content, $content_owner = -1) {
//  global $user, $multibyte;
//  $length = drupal_strlen($content);
  // add PHP_EOL before each kaltura widget to ensure correct grep_match results
//  $content = str_replace('[kaltura-widget', PHP_EOL .'[kaltura-widget', $content);
  $found = FALSE;
//  preg_match_all('/\[kaltura-widget(.*)\/\]/', $content, $matches);
  $kaltura_server = KalturaHelpers::getKalturaServerUrl();
  preg_match_all('|<img[^>]src="'.$kaltura_server.'[^>]+/>|', $content, $matches);
  $kaltura_tags = array();
  foreach ($matches[0] as $key => $match) 
  {
      $kaltura_tags[] = $match;
  }
  
  $partnerId= KalturaHelpers::getPlatformKey("partner_id","0");
  $baseSwfUrl = KalturaHelpers::getSwfUrlForWidget($partnerId);
  
  foreach ($kaltura_tags as $kaltura_tag) {
    $found = TRUE;
    
	$start_tag = strpos($kaltura_tag, 'src="');
	$end_tag = strpos($kaltura_tag, '"',$start_tag+5);
    $tag_part = substr($kaltura_tag,$start_tag+5,$end_tag-($start_tag+4)-1);
    
    // parse the parameters from the tag
    $params = kaltura_get_params_from_tag($tag_part);
    $entryId = $params["entry_id"];
    $swfUrl =  $baseSwfUrl . "/uiconf_id/" . $params["uiconf"] . "/entry_id/" . $entryId;
    
    // get the embed options from the params
  //  $embed_options = kaltura_get_embed_options($params);
    
    $my_rand = mt_rand();
    $width = $params["width"];
    $height = $params["height"];
    $div_id = "kaltura_wrapper_". $entryId . "_" .$my_rand;
    $thumbnail_div_id = "kaltura_thumbnail_". $entryId . "_" .$my_rand;
    $player_id = "kaltura_player_". $entryId . "_" .$my_rand;
    $align = "";
	  $custom_style = "";
    $edit_style = "";
	  $edit_url = JURI::root() . "index.php?option=com_kalturavideo&view=editmix&tmpl=component&entryid=" . $entryId;
	
    if ($content_owner != KalturaHelpers::getSessionUser()->userId || $params["entrytype"] != KalturaEntryType::MIX)
    {
      $edit_style = "display:none";
    }
    
    $kaltura_poweredby = '<div style="width: '. $width .'px;" ><table width="100%"><tr><td>' . KalturaHelpers::get_nls("videomodule", "kaltura") . '</td><td style="text-align:right"><a class="modal" style="' . $edit_style .'" id="kal_edit_' . $entryId . "_" .$my_rand . '" title="Edit Video" href="' . $edit_url . '"  rel="{handler: \'iframe\', size: {x: 890, y: 547}}">' . KalturaHelpers::get_nls("editvideo", "kaltura") . '</a></td></td></table></div>';    
    $links = '<a href="http://corp.kaltura.com/download">open source video</a><a href="http://corp.kaltura.com/technology/">video platform</a>';
    
    $html =  '
      <div id="'. $div_id .'" class="kaltura_wrapper" style="'. $align . $custom_style .'">'. $links .'</div>'. $kaltura_poweredby .    
      '<script type="text/javascript">
          function kaltura_play_'. $my_rand.'()
          {
              var kaltura_swf = new SWFObject("'. $swfUrl .'", "'. $player_id .'", "'. $params["width"] .'", "'. $params["height"] .'", "9", "#000000");
              kaltura_swf.addParam("wmode", "opaque");
              kaltura_swf.addParam("allowScriptAccess", "always");
              kaltura_swf.addParam("allowFullScreen", "TRUE");
              kaltura_swf.addParam("allowNetworking", "all");
              kaltura_swf.write("'. $div_id .'"); 
          }
          kaltura_load_funcs[kaltura_load_funcs.length]="kaltura_play_'. $my_rand.'()";
      </script>
    ';
    
    // rebuild the html with our new code tag 
    $content = str_replace($kaltura_tag, $html, $content);
  }
  
  if ($found) {
	JHTML::_('behavior.modal');
  
    $js = '
	<script type="text/javascript">
  
        HTMLElement.prototype.click = function() {
        var evt = this.ownerDocument.createEvent(\'MouseEvents\');
        evt.initMouseEvent(\'click\', true, true, this.ownerDocument.defaultView, 1, 0, 0, 0, 0, false, false, false, false, 0, null);
        this.dispatchEvent(evt);
        }
        
      function gotoEditorWindow(param1)
      {
           onPlayerEditClick(param1);
      }
      function onPlayerEditClick(param1)
      {
			document.getElementById("kal_edit_"+param1).click();
/*            kalturaInitModalBox(\''. JURI::root() .'/mod/kaltura/keditor.php?entry_id=' . '\'+param1 , {width:890, height:546}); */
      }
		</script>';
  
    $content .= $js;
  }
  
  return $content;
}

/*
 * helper function that breaks the "kaltura tag" into parameters
 */
function kaltura_get_params_from_tag($tag) {
  $first_param = strpos($tag, "entry_id");
  $params_string = substr($tag, $first_param);
  $params = array();
  $attributes_array = explode('/', $params_string);
  for ($i = 0, $len = count($attributes_array); $i < $len; $i=$i+2) 
  {
	  $params[$attributes_array[$i]] = $attributes_array[$i+1];
  }
  return $params;
}

function get_se_wizard($div, $width, $height,$entryId)
{
  $params = "''";
  $url = "''";
  $platformUser = "\"" . KalturaHelpers::getSessionUser()->userId . "\"";
  $kalturaSecret = KalturaHelpers::getPlatformKey("secret","");

  if ($kalturaSecret != null && strlen($kalturaSecret) > 0)
  {
      try
	  {
		  $kClient = new KalturaClient(KalturaHelpers::getServiceConfiguration());
		  $ksId = $kClient -> session -> start($kalturaSecret, $platformUser, KalturaSessionType::USER, null, 86400, "*");
		  $kClient -> setKs($ksId );
		  $url = KalturaHelpers::getSimpleEditorUrl(KalturaHelpers::getPlatformKey("editor",null));
		  $params =  KalturaHelpers::flashVarsToString(KalturaHelpers::getSimpleEditorFlashVars($ksId,$entryId, "entry", ""));
	  }
     catch(Exception $exp)
	  {
		  $flash_embed = $exp->getMessage();
	  }
    $flash_embed = '
	      <div id="'. $div .'" style="width:'.$width.'px;height:'.$height.';">
    	  <script type="text/javascript">
        var kso = new SWFObject("'. $url .'", "KalturaSW", "'. $width .'", "'. $height .'", "9", "#ffffff");
        kso.addParam("flashVars", "'. $params .'");
        kso.addParam("allowScriptAccess", "always");
        kso.addParam("allowFullScreen", "TRUE");
        kso.addParam("allowNetworking", "all");
        if(kso.installedVer.major >= 9) {
          kso.write("' . $div . '");
        } else {
          document.getElementById("' . $div . '").innerHTML = "Flash player version 9 and above is required. <a href=\"http://get.adobe.com/flashplayer/\">Upgrade your flash version</a>";
        }      
	   	  </script>
      ';
      
    return $flash_embed;
  }
}

function get_se_js_functions($thumbUrl)
{
   $javascript = '<script type="text/javascript">
      function onSimpleEditorBackClick(param)
      {
 //         alert("onSimpleEditorBackClick" + param);
      }
      function onSimpleEditorBackClick(param)
      {
			window.parent.document.getElementById(\'sbox-window\').close();
     }
    </script>';
    
    return $javascript;
}

function get_option_key($key)
{
  $all_keys = array();
  $all_keys['registerdescself-opt1-key'] = 'Please select...';
  $all_keys['registerdescself-opt2-key'] = 'Integrator/Web developer';
  $all_keys['registerdescself-opt3-key'] = 'Ad Agency';
  $all_keys['registerdescself-opt4-key'] = 'Kaltura Plugin/Extension/Module Distributor';
  $all_keys['registerdescself-opt5-key'] = 'Social Network';
  $all_keys['registerdescself-opt6-key'] = 'Personal Site';
  $all_keys['registerdescself-opt7-key'] = 'Corporate Site';
  $all_keys['registerdescself-opt8-key'] = 'E-Commerce';
  $all_keys['registerdescself-opt9-key'] = 'E-Learning';
  $all_keys['registerdescself-opt10-key'] = 'Media Company/ Producer';
  $all_keys['registerdescself-opt11-key'] = 'Other';  
  $all_keys['registerebcontent-opt1-key'] = 'Arts & Literature';
  $all_keys['registerebcontent-opt2-key'] = 'Automotive';
  $all_keys['registerebcontent-opt3-key'] = 'Business';
  $all_keys['registerebcontent-opt4-key'] = 'Comedy';
  $all_keys['registerebcontent-opt5-key'] = 'Education';
  $all_keys['registerebcontent-opt6-key'] = 'Entertainment';
  $all_keys['registerebcontent-opt7-key'] = 'Film & Animation';
  $all_keys['registerebcontent-opt8-key'] = 'Gaming';
  $all_keys['registerebcontent-opt9-key'] = 'Howto & Style';
  $all_keys['registerebcontent-opt10-key'] = 'Lifestyle';
  $all_keys['registerebcontent-opt11-key'] = 'Men';
  $all_keys['registerebcontent-opt12-key'] = 'Music';
  $all_keys['registerebcontent-opt13-key'] = 'News & Politics';
  $all_keys['registerebcontent-opt14-key'] = 'Nonprofits & Activism';
  $all_keys['registerebcontent-opt15-key'] = 'People & Blogs';
  $all_keys['registerebcontent-opt16-key'] = 'Pets & Animals';
  $all_keys['registerebcontent-opt17-key'] = 'Science & Technology';
  $all_keys['registerebcontent-opt18-key'] = 'Sports';
  $all_keys['registerebcontent-opt19-key'] = 'Travel & Events';
  $all_keys['registerebcontent-opt20-key'] = 'Women';
  $all_keys['registerebcontent-opt21-key'] = 'N/A';
  $all_keys['registerebcontent-opt21-key'] = 'N/A';
  $all_keys['registeradult-opt1-key'] = 'Yes';
  $all_keys['registeradult-opt2-key'] = 'No';
  
  return $all_keys[$key];
}

?>