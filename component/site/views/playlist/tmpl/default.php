<?php 
require_once(JPATH_COMPONENT . DS . 'lib.php');
$partner = KalturaHelpers::getPlatformKey("partner_id", "0");
$subpartner = $partner . "00";
$server = KalturaHelpers::getPlatformKey("server_uri", "http://www.kaltura.com");
?>

<div style="height:750px">
  <table align="center">
    <tr>
      <td>
        <span id="players">
        </span>
      </td>
    </tr>
    <tr >
      <td>
        <span id="content">
        </span>
      </td>
    </tr>
    <tr>
      <td align="center">
        <textarea  id='embed_code' cols='50' rows='5' readonly='true'></textarea>
      </td>
    </tr>
  </table>
</div>

<script type="text/javascript">
  var partner = "<?php echo $partner?>";
  var subpartner = "<?php echo $subpartner?>";
  var server = "<?php echo $server?>";
  var entryid="<?php echo $this->entryid; ?>";
  var name="<?php echo $this->name; ?>";
  var serverEncode = "<?php echo urlencode($server)?>";
  var ui_confs_playlist;
  <?php echo 'ui_confs_playlist=[{"id":48304,"name":"Vertical Light","width":"400","height":"600"},{"id":48305,"name":"Vertical Dark","width":"400","height":"600"},{"id":48306,"name":"Horizontal Light","width":"740","height":"335"},{"id":48307,"name":"Horizontal Dark","width":"740","height":"335"}';
        $list = KalturaHelpers::getSitePlayListsPlayers();
        foreach ($list as $id => $props)
        {
          echo ',{"id":' . $id . ',"name":"' . $props->name . '","width":"' .$props->width . '","height":"' . $props->height .' "}';
        }
        echo '];';
  ?>
  function get_embed(uiconf,width,height)
  {
  code ='<object height="' + height +'" width="' + width + '" type="application/x-shockwave-flash" data="' + server + '/kwidget/wid/_' + partner + '/ui_conf_id/' + uiconf + '" id="kaltura_playlist" style="visibility: visible;">' +
    '<param name="allowscriptaccess" value="always"/>' + 
    '<param name="allownetworking" value="all"/>' +
    '<param name="bgcolor" value="#000000"/>' +
    '<param name="wmode" value="opaque"/>' +
    '<param name="allowfullscreen" value="true"/>' +
    '<param name="movie" value="' + server + '/kwidget/wid/_' + partner + '/ui_conf_id/' + uiconf + '"/>' +
    '<param name="flashvars" value="layoutId=playlistLight&uid=0&partner_id=' + partner + '&subp_id=' + subpartner + '&k_pl_autoContinue=true&k_pl_autoInsertMedia=true&k_pl_0_name=aaa&k_pl_0_url=' + serverEncode + '%2Findex.php%2Fpartnerservices2%2Fexecuteplaylist%3Fuid%3D%26partner_id%3D' + partner + '%26subp_id%3D' + subpartner + '%26format%3D8%26ks%3D%7Bks%7D%26playlist_id%3D' + entryid + '"/>' +
  '</object>';
  return code;
  }

  function set_embed(uiconf,width,height)
  {
    var embed_code = get_embed(uiconf,width,height);
    document.getElementById("content").innerHTML=embed_code;
    document.getElementById("embed_code").value=embed_code;
  }

  function change_embed()
  {
    var slct = document.getElementById("slctPlayers");
    var i = slct.options[slct.selectedIndex].value;
    
    set_embed(ui_confs_playlist[i].id,ui_confs_playlist[i].width,ui_confs_playlist[i].height);
  }

  function get_players()
  {
  players = '<select id="slctPlayers" onchange="change_embed()">';
    for (i=0; i < ui_confs_playlist.length; i++)
    {
      if (i == 0)
      {
        selected=' selected="selected"';
      }
      else
      {
        selected='';
      }
      players +='<option value="'+i+selected+'">'+ui_confs_playlist[i].name+'</option>';
    }
    players += '</select>';
    return players;
  }

  set_embed(ui_confs_playlist[0].id,ui_confs_playlist[0].width,ui_confs_playlist[0].height);
  document.getElementById("players").innerHTML=get_players();
</script>