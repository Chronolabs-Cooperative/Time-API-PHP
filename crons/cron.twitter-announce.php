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

error_reporting(E_ALL);
require_once(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'apiconfig.php');
echo __LINE__ . "\n";
require_once(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'constants.php');
echo __LINE__ . "\n";
require_once(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'functions.php');
echo __LINE__ . "\n";
require_once(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'calendars.php');
echo __LINE__ . "\n";
require_once(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'class' . DIRECTORY_SEPARATOR . 'TwitterAPIExchange.php');
echo __LINE__ . "\n";
require_once(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'class' . DIRECTORY_SEPARATOR . 'apilists.php');
echo __LINE__ . "\n";
error_reporting(E_ALL);

foreach(APILists::getDirListAsArray($path = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'include'  . DIRECTORY_SEPARATOR . 'twitter') as $folder)
{
    echo __LINE__ . "\n";
    if ($settings = @include($path . DIRECTORY_SEPARATOR . $folder  . DIRECTORY_SEPARATOR . 'settings.php'))
    {
        echo __LINE__ . "\n";
        date_default_timezone_set($settings['timezone']);
        $seconds = date_offset_get(new DateTime);
        $zone = $seconds / 3600 - 1;
        $now = time() - ($zone * 3599);
        
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
        list($hebrewMonth, $hebrewDay, $hebrewYear) = explode('/',$hebrewDate);
    
        $roun 			        =		RounCalendar($now,$zone);
        
        $roun_egypt 			= 		EgyptianCalendar($now,$zone); 
        
        $roun_mayan 			= 		MayanTihkalCalendar($now,$zone);
        
        $roun_mayanhours 		= 		MayanLongCountHours($now,$zone,$roun_mayan);
        
        $roun_mayanminutes 		= 		MayanLongCountMinutes($now,$zone,$roun_mayan);
        
        $roun_mayanseconds 		= 		MayanLongCountSeconds($now,$zone,$roun_mayan);
        
        $roun_latin 			= 		LatinCalendar($now,$zone);
        
        $roun_roman 			= 		RomanCalendar($now,$zone);
        
        $roun_buddhist 		    = 		BuddhistCalendar($now,$zone);
        
        $roun_gregorian 		= 		GregorianCalendar($now,$zone);

	$japan                 =               JapaneseCalendar($now,$zone);

        
        $roun_vedic     		= 		VedicCalendar($now,$zone);
        
        $hijri 				    = 		HijriCalendar::GregorianToHijri( $now );
        
        list($y,$m,$d)		    =		gregorian_to_jalali(date('Y',$now ),date('m',$now ),date('d',$now ));
       
        $jules = explode('/', jdtojulian($julianDate));
        
        $tweet = array();
        $tweet['roun'] = "Roun Movement : " . $roun['strout'] . "\n\nRadial Ounion Movement.\nDay Name: " .  $roun['dayname'] . "\nMonth Name: " .  $roun['monthname'] . "\nStardate: " .  $roun['stardate'] . "\nRoun Floating Point: " .  $roun['rounfloat'] . "\n\nTimezone: " . $settings['timezone'];
        $tweet['jewish'] = "Jewish Calendar : " .  "$hebrewDay $hebrewMonthName $hebrewYear\n\nJewish Calendar Calendar.\nStardate: " .  $hebrewYear.'.'.$hebrewDay . "\n\nTimezone: " . $settings['timezone'];
        $tweet['vedic'] = "Vedic Calendar : " .  $roun_vedic['strout']. ' '.$roun_vedic['mname'].' '.$roun_vedic['dname'] . "\n\nHindu Vedic Calendar Calendar.\nStardate: " .  $roun_vedic['stardate']  . "\n\nTimezone: " . $settings['timezone'];
        $tweet['egyptian'] = "Egyptian : " .  $roun_egypt['strout']. ' '.$roun_egypt['mname'] . "\n\nEgyptian Calendar.\nStardate: " .  $roun_egypt['stardate'] . "\n\nTimezone: " . $settings['timezone'];
        $tweet['roman'] = "Roman : " .  $roun_roman['strout']. ' '.$roun_roman['mname'] . "\n\nRoman Calendar.\nStardate: " .  $roun_roman['stardate'] . "\n\nTimezone: " . $settings['timezone'];
        $tweet['latin'] = "Latin : " .  $roun_latin['strout']. ' '.$roun_latin['mname'] . "\n\nLatin Calendar.\nStardate: " .  $roun_latin['stardate'] . "\n\nTimezone: " . $settings['timezone'];
        $tweet['tikal'] = "Mayan Tikal : " .  $roun_mayan['strout']. ' ('.$roun_mayan['dayname'].' '. $roun_mayan['mname'].')' . "\n\nMayan Tikal Calendar.\nStardate: " .  $roun_mayan['stardate'] . "\n\nTimezone: " . $settings['timezone'];
        $tweet['long'] = "Mayan Long Count : " .  $roun_mayan['longcount']['ppo'] . "\n\nMayan Long Count.\nStardate: " .  $roun_mayan['longcount']['stardate'] . "\n\nTimezone: " . $settings['timezone'];
        $tweet['extended'] = "Mayan Extended Long Count : " .  $roun_mayan['longcount']['ppo'].'.'.$roun_mayanhours['ppo'].'.'.$roun_mayanminutes['ppo'].'.'.$roun_mayanseconds['ppo'] . "\n\nMayan Hour Long Count: " .  $roun_mayanhours['ppo'] . "\nMayan Minute Count: " .  $roun_mayanminutes['ppo'] . "\nMayan Seconds Count: " .  $roun_mayanseconds['ppo'] . "\nStardate: " .  $roun_mayan['stardate'] . "\n\nTimezone: " . $settings['timezone'];
        $tweet['japan'] = "Japanese Calendar : " .  $japan['date'].' '.$japan['time'] . "\n\nJapanese Calendar - elements = " .  $japan['cause'] . "\nStardate: " .  $japan['stardate'] . "\n\nTimezone: " . $settings['timezone'];
        $tweet['hijri'] = "Hijri Calendar : " .  $hijri[1].' '.HijriCalendar::monthName($hijri[0]).' '.$hijri[2] . "\n\nThe lunar calendar used by islamic culture.\nStardate: " .  $hijri[2].'.'.$hijri[1] . "\n\nTimezone: " . $settings['timezone'];
        $tweet['jalali'] = "Jalali Calendar : " .  $d.'-'.$m.'-'.$y . "\n\nThe lunar calendar used by Iranian.\nStardate: " .  $y.'.'.$d . "\n\nTimezone: " . $settings['timezone'];
        $tweet['julian'] = "Julian Day Calendar : " .  jdtojulian($julianDate) . "\n\nJulian Number Day Calendar.\nStar Date: " .  $jules[0].'.'.$jules[2] . "\nJulian Day Decimal : " .  $julianDate . "\n\nTimezone: " . $settings['timezone'];
        $tweet['buddha'] = "Buddhist Calendar : " .  $roun_buddhist['strout']. ' '.$roun_buddhist['mname'] . "\n\nBuddhist Calendar.\nStardate: " .  $roun_buddhist['stardate'] . "\n\nTimezone: " . $settings['timezone'];
        $tweet['gregorian'] = "Gregorian Calendar : " .  $roun_gregorian['strout'] . "\n\nGregorian Calendar.\nStardate: " .  $roun_gregorian['stardate'] . "\n\nTimezone: " . $settings['timezone'];
	
	echo "Printing Following Times:\n\n".implode("\n\n", $tweet);

        $keys = array_keys($tweet);
        shuffle($keys);
        shuffle($keys);
        shuffle($keys);
        shuffle($keys);
        shuffle($keys);
        foreach($keys as $key)
        {
            $twitter = new TwitterAPIExchange($settings['twitter']);
            $getfields = array('status'=>$tweet[$key], 'auto_populate_reply_metadata' => true);
            $postfields = array('status'=>$tweet[$key], 'auto_populate_reply_metadata' => true);
            $init = json_decode($twitter->buildOauth('https://api.twitter.com/1.1/statuses/update.json', 'POST')->setPostfields($postfields)->performRequest(), true);
	    if (!isset($init['errors'][0]['message']))
            {
                echo "\nTweeted: " . $tweet[$key];
            } else {
		echo "\nError: " . $init['errors'][0]['message'];
	    }
        }
    }
}
?>
