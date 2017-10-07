<?php
/**
 * Chronolabs Feeds File
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       Chronolabs Cooperative http://labs.coop
 * @license         General Public License version 3 (http://labs.coop/briefs/legal/general-public-licence/13,3.html)
 * @package         feeds
 * @since           1.1.2
 * @author          Simon Roberts <wishcraft@users.sourceforge.net>
 * @version         $Id: index.php 1000 2013-06-07 01:20:22Z mynamesnot $
 * @subpackage		time
 * @description		Multiple Calendars on RSS Feed for the Here and Now
 */

require_once(__DIR__ . DIRECTORY_SEPARATOR . 'apiconfig.php');
require_once(__DIR__ . DIRECTORY_SEPARATOR . 'constants.php');
require_once(__DIR__ . DIRECTORY_SEPARATOR . 'functions.php');
require_once(__DIR__ . DIRECTORY_SEPARATOR . 'calendars.php');

error_reporting(E_ERRORS);
ini_set('display_errors', true);
ini_set("log_errors" , false);
ini_set("error_log" , __DIR__."/errors.log.".md5(__FILE__).".txt");

$seconds = 0;
$zones = 0;
if (strtolower($_SERVER["HTTP_HOST"]) !== FEED_ROOT_HOSTNAME) 
{
	$terms = explode(".", str_replace(strtolower(FEED_ROOT_HOSTNAME), "", strtolower($_SERVER["HTTP_HOST"])));
	if ($zoner = xoFindDateZone($terms, xoGetDateZones(FEED_DATEZONE_FILE)))
	{
		date_default_timezone_set($zoner);
		$seconds = $seconds + date_offset_get(new DateTime);
		$zones++;
	}
}


if (isset($_GET['area'])&&isset($_GET['place'])&&!empty($_GET['area'])&&!empty($_GET['place'])) {
	if ($_GET['place']!=$_GET['area'])
		date_default_timezone_set(ucfirst($_GET['area']).'/'.ucfirst($_GET['place']));
	else
		date_default_timezone_set(ucfirst($_GET['area']));
	$seconds = $seconds + date_offset_get(new DateTime);
	$zones++;
} 

if ($zones==0) {
	date_default_timezone_set('GMT-0');
	$seconds = $seconds + date_offset_get(new DateTime);
	$zones++;
} 

foreach(array('GMT', 'UTC', 'DST', 'gmt', 'utc', 'dst') as $area) {
	if (isset($_REQUEST[$area])){
		$mode = strtoupper($area);
		$zone = $_REQUEST[$area];
		$seconds = $seconds + ((60*60) * ((float)$zone));
		$now = time() - $seconds;
	}
}

if (!isset($mode)&&!isset($zone)) {
	$mode = 'GMT';
	$zone = $seconds / 3600 - 1;
	$now = time() - ($zone * 3600);
}

$jd = cal_to_jd(CAL_GREGORIAN,date('m',$now ),date('d',$now ),date('y',$now ));
$tmp=get_jd_dmy($jd);
$julian = $tmp[0].'-'.$tmp[1].'-'.$tmp[2] .' ('.jddayofweek($jd,1).'/'.jdmonthname($jd,1).')';
$dayfrac = date('G') / 24 - .5;
if ($dayfrac < 0) $dayfrac += 1;
$frac = $dayfrac + (date('i') + date('s') / 60) / 60 / 24;
$julianDate = $jd+$frac;
$gregorianMonth = date(n,$now);
$gregorianDay = date(j,$now);
$gregorianYear = date(Y,$now);
$arDateFrench = cal_from_jd(gregoriantojd($gregorianMonth,$gregorianDay,$gregorianYear), CAL_FRENCH);
$jdDate = gregoriantojd($gregorianMonth,$gregorianDay,$gregorianYear);
$hebrewMonthName = jdmonthname($jdDate,4);
$hebrewDate = jdtojewish($jdDate);
list($hebrewMonth, $hebrewDay, $hebrewYear) = split('/',$hebrewDate);


?>
<?php 
	
   $roun 			=		RounCalendar($now,$zone);
   
   $roun_egypt 			= 		EgyptianCalendar($now,$zone); 
   
   $roun_mayan 			= 		MayanTihkalCalendar($now,$zone);
   
   $roun_mayanhours 		= 		MayanLongCountHours($now,$zone,$roun_mayan);

   $roun_mayanminutes 		= 		MayanLongCountMinutes($now,$zone,$roun_mayan);

   $roun_mayanseconds 		= 		MayanLongCountSeconds($now,$zone,$roun_mayan);
   
   $roun_latin 			= 		LatinCalendar($now,$zone);
   
   $roun_roman 			= 		RomanCalendar($now,$zone);
   
   $roun_buddhist 		= 		BuddhistCalendar($now,$zone);
   
   $roun_gregorian 		= 		GregorianCalendar($now,$zone);
   
   $roun_vedic     		= 		VedicCalendar($now,$zone);
   
   $hijri 				= 		HijriCalendar::GregorianToHijri( $now );
   
   list($y,$m,$d)		=		gregorian_to_jalali(date('Y',$now ),date('m',$now ),date('d',$now ));
   


header( "Content-type: application/rss+xml" );
header( "Context-Pointer: ".sha1(gethostbyaddr($_SERVER['REMOTE_ADDR']).microtime().$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].$_SERVER['REMOTE_ADDR']).implode('|', $_REQUEST) );

?>
<?php echo '<?xml version="1.0"?>'.chr(10).chr(13); ?>
<rss version="2.0"> 

<channel> 
<title>Chronologistics shipping Chronolabs &amp; Xortify</title> 
<description>Chronologistics shipping Chronolabs Xortify Honeypot -- Roun Time JTL is <?php echo htmlspecialchars($roun['jtl']); ?>, Total amount of time left in cycle of roun calendar system. Document can be found on our site in the news section on how to run some of these calendars in PHP.</description> 
<link>http://labs.coop/</link>
<lastBuildDate><?php echo gmdate('D, d M Y H:i:s', time()); ?></lastBuildDate>
<docs>http://backend.userland.com/rss/</docs>
<generator>https://<?php echo $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; ?><?php echo ($_SERVER['REQUEST_URI']="/"?'#ahead':'#zoned'); ?></generator>
<category>Chronologistics in Time Motion's (Tickets Feeds)</category>
<managingEditor>cipher@labs.coop</managingEditor>
<webMaster>Simon Antony Roberts</webMaster>
<language>en</language>
<image>
  <title>Chronolabs Xortify Honeypot</title>
  <url>https://xortify.com/images/logo.png</url>
  <link>https://xortify.com/</link>
  <width>155</width>
  <height>155</height>
</image>
<?php ob_start(); ?>
<item> 
<title>Roun Movement : <?php echo htmlspecialchars($roun['strout']); ?></title> 
<description>Radial Ounion Movement.&lt;br&gt;Day Name: <?php echo $roun['dayname']; ?>&lt;br&gt;Month Name: <?php echo $roun['monthname']; ?>&lt;br&gt;Stardate: <?php echo $roun['stardate']; ?>&lt;br/&gt;Roun Floating Point: <?php echo $roun['rounfloat']; ?></description>
<category>Precision</category>
<pubDate><?php echo gmdate('D, d M Y H:i:s', time()); ?></pubDate>
<guid><?php echo xoStripeKey(sha1($roun['strout'])); ?></guid>
</item> 
<?php $timer[md5($data = ob_get_clean())] = $data; 
ob_start(); ?>
<item>
<title>Jewish Calendar : <?php echo htmlspecialchars("$hebrewDay $hebrewMonthName $hebrewYear"); ?></title>
<description>Jewish Calendar Calendar.&lt;br&gt;Stardate: <?php echo $hebrewYear.'.'.$hebrewDay; ?></description> 
<link>http://en.wikipedia.org/wiki/Jewish_calendar</link> 
<category>Religious</category>
<pubDate><?php echo gmdate('D, d M Y H:i:s', time()); ?></pubDate>
<guid><?php echo xoStripeKey(sha1($hebrewDay.$hebrewMonthName.$hebrewYear)); ?></guid>
</item>
<?php $timer[md5($data = ob_get_clean())] = $data; 
ob_start(); ?>
<item>
<title>Vedic Calendar : <?php echo htmlspecialchars($roun_vedic['strout']. ' '.$roun_vedic['mname'].' '.$roun_vedic['dname']); ?></title>
<description>Hindu Vedic Calendar Calendar.&lt;br&gt;Stardate: <?php echo $roun_vedic['stardate']; ?></description> 
<link>http://en.wikipedia.org/wiki/Hindu_calendar</link> 
<category>Religious</category>
<pubDate><?php echo gmdate('D, d M Y H:i:s', time()); ?></pubDate>
<guid><?php echo xoStripeKey(sha1($roun_vedic['strout']. ' '.$roun_vedic['mname'].' '.$roun_vedic['dname'])); ?></guid>
</item>
<?php $timer[md5($data = ob_get_clean())] = $data; 
ob_start(); ?>
<item> 
<title>Egyptian : <?php echo htmlspecialchars($roun_egypt['strout']). ' '.$roun_egypt['mname']; ?></title> 
<description>Egyptian Calendar.&lt;br&gt;Stardate: <?php echo $roun_egypt['stardate']; ?></description> 
<link>http://en.wikipedia.org/wiki/Egyptian_calendar</link> 
<category>Ancient History</category>
<pubDate><?php echo gmdate('D, d M Y H:i:s', time()); ?></pubDate>
<guid><?php echo xoStripeKey(sha1($roun_egypt['strout']. ' '.$roun_egypt['mname'].' '.$roun_egypt['dname'])); ?></guid>
</item> 
<?php $timer[md5($data = ob_get_clean())] = $data; 
ob_start(); ?>
<item> 
<title>Roman : <?php echo htmlspecialchars($roun_roman['strout']). ' '.$roun_roman['mname']; ?></title> 
<description>Roman Calendar.&lt;br&gt;Stardate: <?php echo $roun_roman['stardate']; ?></description> 
<link>http://en.wikipedia.org/wiki/Roman_calendar</link> 
<category>Ancient History</category>
<pubDate><?php echo gmdate('D, d M Y H:i:s', time()); ?></pubDate>
<guid><?php echo xoStripeKey(sha1($roun_roman['strout'].$roun_roman['mname'])); ?></guid>
</item> 
<?php $timer[md5($data = ob_get_clean())] = $data; 
ob_start(); ?>
<item> 
<title>Latin : <?php echo htmlspecialchars($roun_latin['strout']). ' '.$roun_latin['mname']; ?></title> 
<description>Latin Calendar.&lt;br&gt;Stardate: <?php echo $roun_latin['stardate']; ?></description> 
<link>http://en.wikipedia.org/wiki/Latin_calendar</link> 
<category>Ancient History</category>
<pubDate><?php echo gmdate('D, d M Y H:i:s', time()); ?></pubDate>
<guid><?php echo xoStripeKey(sha1($roun_latin['strout'])); ?></guid>
</item> 
<?php $timer[md5($data = ob_get_clean())] = $data; 
ob_start(); ?>
<item> 
<title>Mayan Tikal : <?php echo htmlspecialchars($roun_mayan['strout']). ' ('.$roun_mayan['dayname'].' '. $roun_mayan['mname'].')'; ?></title> 
<description>Mayan Tikal Calendar.&lt;br&gt;Stardate: <?php echo $roun_mayan['stardate']; ?></description> 
<link>http://en.wikipedia.org/wiki/Mayan_calendar</link> 
<category>Ancient History</category>
<pubDate><?php echo gmdate('D, d M Y H:i:s', time()); ?></pubDate>
<guid><?php echo xoStripeKey(sha1($roun_mayan['strout'].$roun_mayan['dayname'].$roun_mayan['mname'])); ?></guid>
</item> 
<?php $timer[md5($data = ob_get_clean())] = $data; 
ob_start(); ?>
<item> 
<title>Mayan Long Count : <?php echo htmlspecialchars($roun_mayan['longcount']['ppo']); ?></title> 
<description>Mayan Long Count.&lt;br&gt;Stardate: <?php echo htmlspecialchars($roun_mayan['longcount']['stardate']); ?></description> 
<link>http://en.wikipedia.org/wiki/Mayan_long_count</link> 
<category>Ancient History</category>
<pubDate><?php echo gmdate('D, d M Y H:i:s', time()); ?></pubDate>
<guid><?php echo xoStripeKey(sha1($roun_mayan['longcount']['ppo'])); ?></guid>
</item>
<?php $timer[md5($data = ob_get_clean())] = $data; 
ob_start(); ?>
<item> 
<title>Mayan Extended Long Count : <?php echo htmlspecialchars($roun_mayan['longcount']['ppo'].'.'.$roun_mayanhours['ppo'].'.'.$roun_mayanminutes['ppo'].'.'.$roun_mayanseconds['ppo']); ?></title> 
<description>Mayan Hour Long Count: <?php echo htmlspecialchars($roun_mayanhours['ppo']); ?>&lt;br&gt;Mayan Minute Count: <?php echo htmlspecialchars($roun_mayanminutes['ppo']); ?>&lt;br&gt;Mayan Seconds Count: <?php echo htmlspecialchars($roun_mayanseconds['ppo']); ?>&lt;br&gt;Stardate: <?php echo $roun_mayan['stardate']; ?></description> 
<link>http://en.wikipedia.org/wiki/Mayan_long_count</link> 
<category>Ancient History</category>
<pubDate><?php echo gmdate('D, d M Y H:i:s', time()); ?></pubDate>
<guid><?php echo xoStripeKey(sha1($roun_mayan['longcount']['ppo'].'.'.$roun_mayanextened['ppo'])); ?></guid>
</item>  
<?php $timer[md5($data = ob_get_clean())] = $data; 
ob_start(); ?>
<?php
	$japan = JapaneseCalendar($now,$gmt,1);
?>
<item> 
<title>Japanese Calendar : <?php echo $japan['date'].' '.$japan['time']; ?></title> 
<description>Japanese Calendar - elements = <?php echo $japan['cause']; ?>&lt;br&gt;Stardate: <?php echo $japan['stardate']; ?></description> 
<link>http://en.wikipedia.org/wiki/Japanese_calendar</link> 
<category>Precision</category>
<pubDate><?php echo gmdate('D, d M Y H:i:s', time()); ?></pubDate>
<guid><?php echo xoStripeKey(sha1($japan['date'].' '.$japan['time'])); ?></guid>
</item> 
<?php $timer[md5($data = ob_get_clean())] = $data; 
ob_start(); ?>
<item> 
<title>Hijri Calendar : <?php echo htmlspecialchars($hijri[1].' '.HijriCalendar::monthName($hijri[0]).' '.$hijri[2]); ?></title> 
<description>The lunar calendar used by islamic culture.&lt;br&gt;Stardate: <?php echo $hijri[2].'.'.$hijri[1]; ?></description> 
<link>http://en.wikipedia.org/wiki/Islamic_calendar</link> 
<category>Religious</category>
<pubDate><?php echo gmdate('D, d M Y H:i:s', time()); ?></pubDate>
<guid><?php echo xoStripeKey(sha1($hijri[1].' '.HijriCalendar::monthName($hijri[0]).' '.$hijri[2])); ?></guid>
</item> 
<?php $timer[md5($data = ob_get_clean())] = $data; 
ob_start(); 
?>
<item> 
<title>Jalali Calendar : <?php echo htmlspecialchars($d.'-'.$m.'-'.$y); ?></title> 
<description>The lunar calendar used by Iranian.&lt;br&gt;Stardate: <?php echo $y.'.'.$d; ?></description> 
<link>http://en.wikipedia.org/wiki/Iranian_calendar</link> 
<category>Religious</category>
<pubDate><?php echo gmdate('D, d M Y H:i:s', time()); ?></pubDate>
<guid><?php echo xoStripeKey(sha1($d.'-'.$m.'-'.$y)); ?></guid>
</item> 
<?php $timer[md5($data = ob_get_clean())] = $data; 
ob_start(); ?>
<item> 
<title>Julian Day Calendar : <?php echo htmlspecialchars(jdtojulian($julianDate)); ?></title> 
<?php $jules = explode('/', jdtojulian($julianDate)); ?>
<description>Julian Number Day Calendar.&lt;br/&gt;Star Date: <?php echo $jules[0].'.'.$jules[2]; ?>&lt;br/&gt;Julian Day Decimal : <?php echo $julianDate; ?></description> 
<link>http://en.wikipedia.org/wiki/Julian_calendar</link> 
<category>Precision</category>
<pubDate><?php echo gmdate('D, d M Y H:i:s', time()); ?></pubDate>
<guid><?php echo xoStripeKey(sha1(jdtojulian($julianDate))); ?></guid>
</item> 
<?php $timer[md5($data = ob_get_clean())] = $data; 
ob_start(); ?>
<item> 
<title>Buddhist Calendar : <?php echo htmlspecialchars($roun_buddhist['strout']). ' '.$roun_buddhist['mname']; ?></title> 
<description>Buddhist Calendar.&lt;br&gt;Stardate: <?php echo $roun_buddhist['stardate']; ?></description> 
<link>http://en.wikipedia.org/wiki/Buddhist_calendar</link> 
<category>Religious</category>
<pubDate><?php echo gmdate('D, d M Y H:i:s', time()); ?></pubDate>
<guid><?php echo xoStripeKey(sha1($roun_buddhist['strout'])); ?></guid>
</item> 
<?php $timer[md5($data = ob_get_clean())] = $data; 
ob_start(); ?>
<item> 
<title>Gregorian Calendar : <?php echo htmlspecialchars($roun_gregorian['strout']); ?></title> 
<description>Gregorian Calendar.&lt;br&gt;Stardate: <?php echo $roun_gregorian['stardate']; ?></description> 
<link>http://en.wikipedia.org/wiki/Gregorian_calendar</link> 
<category>Religious</category>
<pubDate><?php echo gmdate('D, d M Y H:i:s', time()); ?></pubDate>
<guid><?php echo xoStripeKey(sha1($roun_gregorian['strout'])); ?></guid>
</item> 
<?php $timer[md5($data = ob_get_clean())] = $data; ?>
<?php

	shuffle($timer);
	foreach($timer as $id => $time) {
		echo $time."\n";
	}

	echo '<item>';
	echo '<title>Specify Greenwich Mean Time (GMT) !</title>';
	echo '<description>'.htmlspecialchars('You can input a decimal gmt now by changing it on the end of the url, the name of the element to use on the URL is <strong>'.$mode.'</strong> for example this url at the moment as default would be <a href="'.FEED_PROT.FEED_ROOT_HOSTNAME.'/'.$mode.'/'.$zone.'">'.FEED_PROT.FEED_ROOT_HOSTNAME.'/'.$mode.'/'.$zone.'</a><br/><br/>All you have to do is change the symbol on the end remember that the earth is a circular motion through -12 to 12 - all numeric and will react to remainder (no more no less). So if you want to change the URL the gmt part you would be changine for revolutionary area of the time and this is currently set to <strong>'.$zone.'</strong><br/><br/>For example if you wanted to have a list of sydney australia\'s time you would specify a <strong>GMT</strong> of <strong>10</strong> - This would look like on the URL as <a href="'.FEED_PROT.FEED_ROOT_HOSTNAME.'/Australia/Sydney">'.FEED_PROT.FEED_ROOT_HOSTNAME.'/Australia/Sydney</a><br/><br/>You can normally have the GMT specified by doing the following <a href="'.FEED_PROT.FEED_ROOT_HOSTNAME.'/Australia/Sydney">'.FEED_PROT.FEED_ROOT_HOSTNAME.'/Australia/Sydney</a> for example for Sydney will have time zones specifiable, this will be done by the following being specified <a href="'.FEED_PROT.FEED_ROOT_HOSTNAME.'/Australia/Sydney/GMT/10.0001">'.FEED_PROT.FEED_ROOT_HOSTNAME.'/Australia/Sydney/GMT/10.0001</a> for an Australia, Sydney time zone which is the default. The following time zones are specifiable in the following spread sheets you will also find them on : <a href="http://blog.simonaroberts.com/mynamesnot/internet/2014/03/time-zones-for-httpstime-labs-coop/">http://blog.simonaroberts.com/mynamesnot/internet/2014/03/time-zones-for-httpstime-labs-coop/</a><br/><br/><em>Have you tried any of our other tickers like</em> <a href="http://seed.labs.coop/GMT/0.00">Randomisation Seeds</a> or <a href="http://spline.labs.coop/">GeoSpatial Spline</a>?').'<h2>Internet API Usage Statistics</h2><p>You can find the usage statistics for this API which is update within every ten minutes at the following URI :: <a target="_blank" href="http://time.labs.coop/stats/awstats.pl" target="_blank">http://time.labs.coop/stats/awstats.pl</a>. These should outline the overall load and frequencies of this application programmable interface!</p></description>';
	echo '<category>Help</category>';
	echo '<pubDate>'.gmdate('D, d M Y H:i:s', time()).'</pubDate>';
	echo '<guid>'.xoStripeKey(sha1('Specify Greenwich Mean Time (GMT) !')).'</guid>';
	echo '</item>';		
?>
</channel> 
</rss>
