<?php
// Request selected language - by Ciprian
// You need to tell the class which language do you use.
// L_LANG should be defined as en_US format!!! Next line is an example, just uncomment it and put your own language from the provided list
//define("L_LANG", "ja_JP");

$hl = (isset($_REQUEST["hl"])) ? $_REQUEST["hl"] : false;
if(!defined("L_LANG") || L_LANG == "L_LANG"){
if(isset($hl) && $hl != "") define("L_LANG", $hl);
else define("L_LANG", "en_US");
}
// START OF: Needed for the manual Language selector - not needed if you pass the LANG from your own script
		$langs='lang/';
		$langfiles = opendir($langs); #open directory
			$i = 0;
			while (false !== ($langfile = readdir($langfiles)))
			{
				if (!stristr($langfile,"html") && !stristr($langfile,"localization") && !stristr($langfile,"xx_YY") && $langfile!=='.' && $langfile!=='..')
				{
					$hl=str_replace("calendar.","",$langfile);
					$hl=str_replace(".","",$hl);
					$hl=str_replace("php","",$hl);
					$langsfile[]=$hl;
			 		$i++;
				}
			}
			closedir($langfiles);
			if ($langsfile)
			{
				array_push($langsfile, "en_US");
			  	natsort($langsfile);
			}
// END OF: Needed for the manual Language selector - not needed if you pass the LANG from your own script
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>

<title>Localized PHP DatePicker Calendar - &copy; Triconsole</title>

<link href="calendar.css" rel="stylesheet" type="text/css" />
<?php
require('tc_calendar.php');
?>
<style type="text/css">
body { font-size: 11px; font-family: "verdana"; }

pre { font-family: "verdana"; font-size: 10px; background-color: #FFFFCC; padding: 5px 5px 5px 5px; }
pre .comment { color: #008000; }
pre .builtin { color: #FF0000; }
</style>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>

<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<h3><a href="http://www.triconsole.com/php/calendar_datepicker.php" target="_blank">PHP - Calendar, DatePicker Calendar</a></h3>
<pre>- Credits go to TJ, the original Calendar Datepicker class developer on <a href="http://www.triconsole.com" target="_blank">http://www.triconsole.com</a> -</pre>
<h3><a href="calendar_localized.zip" target="_blank">Download this multi-language version here</a> (Last updated on <font color="green">30.07.2010</font>)</h3>
<pre>- Credits go to <a href="mailto:ciprianmp@yahoo.com?subject=Localized%20Calendar%20Class%20feedback" target="_blank">Ciprian Murariu</a> & <a href="http://ciprianmp.com/plus/" target="_blank">phpMyChat-Plus</a> translators, based on 3.1 version TJ's class above -</pre>
<table width="100%" border="0" cellspacing="0" cellpadding="5">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="0" cellpadding="5">
        <tr>
          <td><h3>Demo:</h3>
            <form name="form1" method="post" action="">
              <table border="0" cellspacing="0" cellpadding="2">
<!-- START OF: Needed for the manual Language selector - not needed if you pass the LANG from your own script -->
			  <tr>
                  <td nowrap>Select language : </td>
		<td valign=top>
        <input name="hl" type="hidden" id="hl" value="<?php echo($hl);?>" />
		<?php
		echo ("<select name=\"hl\">");
			$j = 1;
			foreach ($langsfile as $langname)
			{
				if ($langname == "es_AR") $LANG_NAME = "Español Argentina";
				elseif ($langname == "bg_BG") $LANG_NAME = "Български";
				elseif ($langname == "da_DK") $LANG_NAME = "Dansk";
				elseif ($langname == "nl_NL") $LANG_NAME = "Nederlands";
				elseif ($langname == "en_GB") $LANG_NAME = "English UK";
				elseif ($langname == "en_US") $LANG_NAME = "English US";
				elseif ($langname == "fr_FR") $LANG_NAME = "Français";
				elseif ($langname == "de_DE") $LANG_NAME = "Deutsch";
				elseif ($langname == "he_IL") $LANG_NAME = "עברית";
				elseif ($langname == "hu_HU") $LANG_NAME = "magyar";
				elseif ($langname == "id_ID") $LANG_NAME = "Bahasa Indonesia";
				elseif ($langname == "it_IT") $LANG_NAME = "Italiano";
				elseif ($langname == "ja_JP") $LANG_NAME = "日本語";
				elseif ($langname == "pt_BR") $LANG_NAME = "Português do Brasil";
				elseif ($langname == "ro_RO") $LANG_NAME = "Română";
				elseif ($langname == "sr_CS") $LANG_NAME = "Srpski";
				elseif ($langname == "es_ES") $LANG_NAME = "Español";
				elseif ($langname == "sv_SE") $LANG_NAME = "Svenska";
				elseif ($langname == "tr_TR") $LANG_NAME = "Türkçe";
				elseif ($langname == "vi_VN") $LANG_NAME = "Việt nam";
				echo("<option value=\"".($langname != "en_US" ? $langname : "")."\"");
				if((defined("L_LANG") && $langname == L_LANG) || ($langname == "en_US" && !defined("L_LANG"))) echo(" selected");
				echo(">".$LANG_NAME." (".$langname.")</option>");
			$j++;
			}
		unset($langsfile);
		?>
			<input type="submit" name="Submit" value="<?php echo(L_SEL_LANG); ?>" />
				  </select>
				  </td>
				</tr>
<!-- END OF: Needed for the manual Language selector - not needed if you pass the LANG from your own script -->
			  </table>
              <p class="largetxt"><b>Fixed Display Style </b></p>
              <table border="0" cellspacing="0" cellpadding="2">
                <tr>
                  <td valign="top" nowrap>Date 1 :</td>
                  <td valign="top"><?php
	  $myCalendar = new tc_calendar("date2");
	  $myCalendar->setPicture("images/iconCalendar.gif");
	  $myCalendar->setDate(date('d'), date('m'), date('Y'));
	  $myCalendar->setPath("./");
	  $myCalendar->setYearSelect(1970, 2020);
	  $myCalendar->dateAllow('2008-05-13', '2015-03-01', false);
	  //$myCalendar->autoSubmit(true, "", "test.php");
	  //$myCalendar->autoSubmit(true, "form1");
	  $myCalendar->disabledDay("sat");
	  $myCalendar->disabledDay("sun");
	  $myCalendar->writeScript();
	  ?></td>
                  <td valign="top"><ul>
                    <li> Set default date to today date (server time) </li>
                    <li> Set year navigate from 1970 to 2020</li>
                    <li> Allow date selectable from 13 May 2008 to 01 March 2015</li>
                    <li> Not allow to navigate other dates from above </li>
                    <li>Disabled Sat and Sun</li>
                  </ul></td>
                </tr>
              </table>
              <p><b>Code:</b></p>
              <pre>&lt;?php<br />	  $myCalendar = new tc_calendar(&quot;date2&quot;);<br />	  $myCalendar-&gt;SetPicture(&quot;images/iconCalendar.gif&quot;);<br />	  $myCalendar-&gt;setDate(date('d'), date('m'), date('Y'));<br />	  $myCalendar-&gt;setPath(&quot;./&quot;);<br />	  $myCalendar-&gt;setYearSelect(1970, 2020);<br />	  $myCalendar-&gt;dateAllow('2008-05-13', '2015-03-01', false);<br />    $myCalendar-&gt;disabledDay("sat");<br />    $myCalendar-&gt;disabledDay("sun");<br />	  $myCalendar-&gt;writeScript();<br />	  ?&gt;</pre>
                <p class="largetxt"><b>DatePicker Style </b></p>
              <table border="0" cellspacing="0" cellpadding="2">
                <tr>
                  <td nowrap>Date 2 :</td>
                  <td><?php
	  $myCalendar = new tc_calendar("date1", true);
	  $myCalendar->setPicture("images/iconCalendar.gif");
	  $myCalendar->setDate(date('d'), date('m'), date('Y'));
	  $myCalendar->setPath("./");
	  $myCalendar->setYearSelect(1960, date('Y'));
	  $myCalendar->dateAllow('1960-03-01', date('Y-m-d'));
	  //$myCalendar->autoSubmit(true, "form1");
	  $myCalendar->disabledDay("sun");
	  $myCalendar->writeScript();
	  ?></td>
<!-- START OF: Needed for checking the selected value - not needed in your own script -->
                  <td><input type="button" name="button" id="button" value="<?php echo(L_CHK_VAL); ?>" onClick="javascript:alert(this.form.date1.value);"></td>
<!-- END OF: Needed for checking the selected value - not needed in your own script -->
               </tr>
              </table>
              <ul>
                <li>Default date set to current date </li>
                <li>Set year navigate from 1960 to current year </li>
                <li>Allow date selectable from 01 March 1960 to current date</li>
                <li>Allow to navigate other dates from above </li>
                <li>Disabled Sun</li>
              </ul>
              <p><b>Code:</b></p>
              <pre>&lt;?php<br />	  $myCalendar = new tc_calendar(&quot;date1&quot;, true);<br />	  $myCalendar-&gt;SetPicture(&quot;images/iconCalendar.gif&quot;);<br />	  $myCalendar-&gt;setDate(date(&quot;d&quot;), date(&quot;m&quot;), date(&quot;Y&quot;));<br />	  $myCalendar-&gt;setPath(&quot;./&quot;);<br />	  $myCalendar-&gt;setYearSelect(1960, date(&quot;Y&quot;));<br />	  $myCalendar-&gt;dateAllow(&quot;1960-01-01&quot;, date(&quot;Y-m-d&quot;));<br />    $myCalendar-&gt;disabledDay("sun");<br />	  $myCalendar-&gt;writeScript();<br />	  ?&gt;</pre>
              <p class="largetxt"><b>DatePicker with no input box</b></p>
              <table border="0" cellspacing="0" cellpadding="2">
                <tr>
                  <td nowrap>Date 3 :</td>
                  <td><?php
	  $myCalendar = new tc_calendar("date5", true, false);
	  $myCalendar->setPicture('images/iconCalendar.gif');
	  $myCalendar->setDate(date('d'), date('m'), date('Y'));
	  $myCalendar->setPath("./");
	  $myCalendar->setYearSelect(1945, date('Y'));
	  $myCalendar->dateAllow('1945-01-01', date('Y-m-d'));
	  $myCalendar->setDateFormat(str_replace("%","",str_replace("B","F",str_replace("d","j",L_CAL_FORMAT))));
	  //$myCalendar->autoSubmit(true, "form1");
	  $myCalendar->writeScript();
	  ?></td>
<!-- START OF: Needed for checking the selected value - not needed in your own script -->
                  <td><input type="button" name="button" id="button" value="<?php echo(L_CHK_VAL); ?>" onClick="javascript:alert(this.form.date5.value);"></td>
<!-- END OF: Needed for checking the selected value - not needed in your own script -->
                </tr>
              </table>
              <ul>
                <li>Default date to current server date</li>
                <li>Set year navigate from 1945 to current year </li>
                <li>Allow date selectable from 13 May 2008 to today</li>
                <li>Allow to navigate other dates from above </li>
                <li>Date input box set to false </li>
              </ul>
              <p><b>Code:</b></p>
              <pre>&lt;?php<br />	  $myCalendar = new tc_calendar(&quot;date5&quot;, true, false);<br />	  $myCalendar-&gt;SetPicture(&quot;calendar/images/iconCalendar.gif&quot;);<br />	  $myCalendar-&gt;setDate(date('d'), date('m'), date('Y'));<br />	  $myCalendar-&gt;setPath(&quot;calendar/&quot;);<br />	  $myCalendar-&gt;setYearSelect(1945, date('Y'));<br />	  $myCalendar-&gt;dateAllow('1945-01-01', date('Y-m-d'));<br />	  $myCalendar-&gt;setDateFormat('***') - auto;<br />	  $myCalendar-&gt;writeScript();<br />	  ?&gt;<br />*** - each language takes it's own format from the localization files</pre>
              <p class="largetxt"><b>Date Pair Example</b></p>
              <div style="float: left;">
                <div style="float: left; padding-right: 3px; line-height: 18px;">Date 4 interval from:</div>
                <div style="float: left;">
                  <?php
	  $thisweek = date('W');
		$thisyear = date('Y');
		$dayTimes = getDaysInWeek($thisweek, $thisyear);
		//----------------------------------------

		$date1 = date('Y-m-d', $dayTimes[0]);
		$date2 = date('Y-m-d', $dayTimes[(sizeof($dayTimes)-1)]);

		function getDaysInWeek ($weekNumber, $year, $dayStart = 1) {
		  // Count from '0104' because January 4th is always in week 1
		  // (according to ISO 8601).
		  $time = strtotime($year . '0104 +' . ($weekNumber - 1).' weeks');
		  // Get the time of the first day of the week
		  $dayTime = strtotime('-' . (date('w', $time) - $dayStart) . ' days', $time);
		  // Get the times of days 0 -> 6
		  $dayTimes = array ();
		  for ($i = 0; $i < 7; ++$i) {
			$dayTimes[] = strtotime('+' . $i . ' days', $dayTime);
		  }
		  // Return timestamps for mon-sun.
		  return $dayTimes;
		}
	  $myCalendar = new tc_calendar("date3", true, false);
	  $myCalendar->SetPicture("images/iconCalendar.gif");
	  $myCalendar->setDate(date('d', strtotime($date1)), date('m', strtotime($date1)), date('Y', strtotime($date1)));
	  $myCalendar->setPath("./");
	  $myCalendar->setYearSelect(1970, 2020);
	  //$myCalendar->dateAllow('2009-02-20', "", false);
	  $myCalendar->writeScript();
	  ?>
                </div>
              </div>
              <div style="float: left;">
                <div style="float: left; padding-left: 3px; padding-right: 3px; line-height: 18px;">to</div>
                <div style="float: left;">
                  <?php
	  $myCalendar = new tc_calendar("date4", true, false);
	  $myCalendar->SetPicture("images/iconCalendar.gif");
	  $myCalendar->setDate(date('d', strtotime($date2)), date('m', strtotime($date2)), date('Y', strtotime($date2)));
	  $myCalendar->setPath("./");
	  $myCalendar->setYearSelect(1970, 2020);
	  //$myCalendar->dateAllow("", '2009-11-03', false);
	  $myCalendar->writeScript();
	  ?>
                </div>
              </div>
              <p>
                <input type="button" name="button2" id="button2" value="<?php echo(L_CHK_VAL); ?>" onClick="javascript:alert('Date interval selected from '+this.form.date3.value+' to '+this.form.date4.value);">
              </p>
              <p><b>Code:</b></p>
              <pre>&lt;?php					<br />	  $myCalendar = new tc_calendar(&quot;date3&quot;, true, false);<br />	  $myCalendar-&gt;SetPicture(&quot;images/iconCalendar.gif&quot;);<br />	  $myCalendar-&gt;setDate(date('d', strtotime($date1))
            , date('m', strtotime($date1))
            , date('Y', strtotime($date1)));<br />	  $myCalendar-&gt;setPath(&quot;./&quot;);<br />	  $myCalendar-&gt;setYearSelect(1970, 2020);<br />	  $myCalendar-&gt;writeScript();	  <br />	  <br />	  $myCalendar = new tc_calendar(&quot;date4&quot;, true, false);<br />	  $myCalendar-&gt;SetPicture(&quot;images/iconCalendar.gif&quot;);<br />	  $myCalendar-&gt;setDate(date('d', strtotime($date2))
           , date('m', strtotime($date2))
           , date('Y', strtotime($date2)));<br />	  $myCalendar-&gt;setPath(&quot;./&quot;);<br />	  $myCalendar-&gt;setYearSelect(1970, 2020);<br />	  $myCalendar-&gt;writeScript();	  <br />	  ?&gt;</pre>
            </form>
      <p align="center">&copy; Triconsole (<a href="http://www.triconsole.com/" target="_blank">triconsole.com</a>)</p>
		  </td>
		  </tr>
		</table>
	  </td>
  </tr>
</table>
</body>
</html>
