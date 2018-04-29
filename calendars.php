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
 * @version         $Id: calendars.php 1000 2013-06-07 01:20:22Z mynamesnot $
 * @subpackage		time
 * @description		Multiple Calendars on RSS Feed for the Here and Now
 */


error_reporting(E_ERROR);
ini_set('display_errors', true);
ini_set("log_errors" , "1");
ini_set("error_log" , dirname(__FILE__)."/errors.log.".md5(__FILE__).".txt");


/**
 * Hijri Calendar Class Factory
 *
 * @author     Simon Roberts <wishcraft@users.sourceforge.net>
 * @package    feeds
 * @subpackage time
 */


class HijriCalendar
{

	/**
	 * monthName()
	 * Returns a Month Name
	 *
	 * @param integer $i			$i = 1..12 for month
	 * @return string
	 */
   	function monthName($i) 
   	{
    	static $month  = array(	"Muharram ul Haram", "Safar", "Rabi' al-awwal", "Rabi' al-akhir",
        						"Jumada al-awwal", "Jumada al-akhir", "Rajab", "Sha'aban",
           						"Ramadan", "Shawwal", "Dhu al-Qi'dah", "Dhu al-Hijjah");
       	return $month[$i-1];
   	}


   	/**
   	 * GregorianToHijri()
   	 * Converts Gregorian time to Hijri
   	 *
   	 * @param integer $time			Gregorian Unix Time Stamp
   	 * @return integer
   	 */
   	function GregorianToHijri($time = null)
   	{
    	if ($time === null) $time = time();
       	$m = date('m', $time);
       	$d = date('d', $time);
       	$y = date('Y', $time);
       	return HijriCalendar::JDToHijri(cal_to_jd(CAL_GREGORIAN, $m, $d, $y));
   	}

   	/**
   	 * HijriToGregorian()
   	 * Converts Hijri to Gregorian time 
   	 *
   	 * @param integer $m			Hijri month number
   	 * @param integer $d			Hijri day number
   	 * @param integer $y			Hijri year number
   	 * @return integer
   	 */
   	function HijriToGregorian($m, $d, $y)
   	{
    	return jd_to_cal(CAL_GREGORIAN, HijriCalendar::HijriToJD($m, $d, $y));
   	}

   	/**
   	 * JDToHijri()
   	 * Julian Day Count To Hijri
   	 *
   	 * @param integer $jd			Julian Date Stamp
   	 * @return integer
   	 */
   	function JDToHijri($jd)
   	{
       	$jd = $jd - 1948440 + 10632;
       	$n  = (integer)(($jd - 1) / 10631);
       	$jd = $jd - 10631 * $n + 354;
       	$j  = ((integer)((10985 - $jd) / 5316)) *
        	  	((integer)(50 * $jd / 17719)) +
        		((integer)($jd / 5670)) *
           		((integer)(43 * $jd / 15238));
       	$jd = $jd - ((integer)((30 - $j) / 15)) *
           		((integer)((17719 * $j) / 50)) -
           		((integer)($j / 16)) *
           		((integer)((15238 * $j) / 43)) + 29;
       	$m  = (integer)(24 * $jd / 709);
       	$d  = $jd - (integer)(709 * $m / 24);
       	$y  = 30*$n + $j - 30;
       	return array($m, $d, $y);
   	}


   	/**
   	 * HijriToJD()
   	 * Hijri To Julian Day Count
   	 *
   	 * @param integer $m			Hijri month number
   	 * @param integer $d			Hijri day number
   	 * @param integer $y			Hijri year number
   	 * @return integer
   	 */
   	function HijriToJD($m, $d, $y)
   	{
    	return (integer)((11 * $y + 3) / 30) +
           		354 * $y + 30 * $m -
           		(integer)(($m - 1) / 2) + $d + 1948440 - 385;
   	}
};


/**
 * JapaneseCalendar()
 * Japanese Calendar using Unix Timestamp with Ounion Movement Sequence
 *
 * @param integer $unix_time			Unix Date/Time Stamp to Convert
 * @param double $gmt					GMT/UTC/DST of $unix_time stamp
 * @param integer $monthsystem			Month System to Use
 * @param string $poffset				Depreciative Monnthly Calendar to Use (depreciated)
 * @param float $pweight				Weighting Balancing Variable
 * @return array
 */
function JapaneseCalendar($unix_time, $gmt, $monthsystem = 0, $poffset = 'NOW', $pweight = 0)
    {
    // Code Segment 1 â€“ Calculate Floating Point
    $tme = $unix_time;

    if ($gmt>0){
        $gmt=-$gmt;
    } else {
        $gmt=abs($gmt);
    }
    
	if ($poffset == 'NOW') {
		$diff = 0+(60*60*$gmt);
	} else {
		$diff = abs(time()-strtotime($poffset))+(60*60*$gmt);
	}
	
	$roun = (($unix_time - $diff) - $pweight);	

	$months[0]['name'] = array(	'ichigatsu','nigatsu','sangatsu',
								'shigatsu','gogatsu','rokugatsu',
								'shichigatsu','hachigatsu','kugatsu',
								'jugatsu','juichigatsu','junigatsu');
								
	$months[0]['cause'] = array(	'first month',
								'second month',
								'third month',
								'forth month',
								'fifth month',
								'six month',
								'seventh month',
								'eighth month',
								'ninth month',
								'tenth month',
								'elventh month',
								'twelve month');

	$months[1]['name'] = array(	'mutsuki','kinusaragi','yayoi',
								'uzuki','satsuki','minatsuki',
								'fumizuki','hazuki','nagatsuki',
								'kaminazuki','shimotsuki','shiwasu');
								
	$months[1]['cause'] = array(	'affection month',
								'changing clothes',
								'new life',
								'u-no-hana month',
								'fast month',
								'month of water',
								'book month',
								'leaf month',
								'long month',
								'month of gods',
								'frost month',
								'priests run');
								
	$days['roman'] = array(	'nichiyobi','getsuyobi','kayobi',
							'suiyobi','mokuyobi','kin\'yobi','doyobi');
	$days['element'] = array('Sun','Moon','Fire','Water','Wood','Gold','Earth');
	$days['day'] = array( 'tsuitachi','futsuka','mikka','yokka','itsuka',
							'muika','nanoka','yoka','kokonoka','toka',
							'juichinichi','juninichi','jusannichi','juyokka',
							'jugonichi','jurokunichi','jushichinichi','juhachinichi',
							'jukunichi','hatsuka','nijuichinichi',
							'nijuninichi','nijusannichi','nijuyokka','nijugonichi',
							'nijurokunichi','nijushichinichi','nijuhachinichi',
							'nijukunichi','sanjunichi','sanjuichinichi');
	
	$day_count = round(($roun - strtotime('31/12/'.(intval(date('Y', $roun))-1)))/86400);
	
	return  array('stardate'=>date('Y', $roun).".$day_count", "date"=>$days['roman'][date('w', $roun)].' '.$days['day'][date('d', $roun)-1].' '.
			$months[$monthsystem]['name'][date('n', $roun)-1].' '.date('Y', $roun),
			"cause" => $days['element'][date('w', $roun)].' '.$months[$monthsystem]['cause'][date('n', $roun)-1],
			"time" => date('h:i:s A', $roun));
}




/**
* RounCalendar()
* @summary Roun(ion) Calendar using Unix Timestamp with Ounion Movement Sequence
*
* @param integer $unix_time			Unix Date/Time Stamp to Convert
* @param double $gmt					GMT/UTC/DST of $unix_time stamp
* @param string $poffset				Core Break Point of Calendar Cycle to Unix Timestamp and $pweight
* @param float $pweight				Weighting Days Balancing Variable
* @param float $defiency				Defeiency Monthly Cycle to Use
* @param array $timeset				Maximum Hours, Minutes, Seconds in a Time Cycle
* @return array
*/
function RounCalendar($unix_time, $gmt, $poffset = '2008-05-11 14:45:38', $pweight = '-1.599999991', $defiency='deficient', $timeset= array("hours" => 24, "minutes" => 60, "seconds" => 60))
{
	// Code Segment 1 â€“ Calculate Floating Point
	$tme = $unix_time;
	if ($gmt>0){
		$gmt=-$gmt;
	} else {
		$gmt=abs($gmt);
	}
	$ptime = strtotime($poffset)+(60*60*$gmt);
	$weight = $pweight+(1*$gmt);
	$roun_xa = ($tme)/(24*60*60);
	$roun_ya = $ptime/(24*60*60);
	$roun = (($roun_xa -$roun_ya) - $weight)+(microtime(true)/999999);
	
	// Code Segment 2 - Set month day arrays
	$nonedeficient = array(	"seq1" => array(31,30,31,30,30,30,31,30,31,30,31,30),
							"seq2" => array(31,30,31,30,31,30,31,30,31,30,31,30),
							"seq3" => array(31,30,31,30,30,30,31,30,31,30,31,30),
							"seq4" => array(31,30,31,30,30,30,31,30,31,30,31,30));
	$deficient =     array(	"seq1" => array(31,30,31,30,31,30,30,30,31,30,31,30),
							"seq2" => array(31,30,31,30,31,30,31,30,31,30,31,30),
							"seq3" => array(31,30,31,30,31,30,31,30,30,30,31,30),
							"seq4" => array(30,30,31,30,31,30,31,30,31,30,31,30),
							"seq5" => array(31,30,31,30,31,30,31,30,31,30,30,30),
							"seq6" => array(31,30,31,30,31,30,31,30,31,30,31,30),
							"seq7" => array(31,30,31,30,31,30,31,30,31,30,31,30),
							"seq8" => array(31,30,31,30,31,30,31,30,31,30,31,30),
							"seq9" => array(30,30,31,30,31,30,31,30,31,30,31,30),
							"seq10" => array(31,30,31,30,31,30,31,30,31,30,31,30),
							"seq11" => array(31,30,31,30,31,30,31,30,31,30,31,30),
							"seq12" => array(31,30,31,30,31,30,31,30,31,30,31,30),
							"seq13" => array(31,30,30,30,31,30,31,30,31,30,31,30),
							"seq14" => array(31,30,31,30,31,30,31,30,31,30,31,30),
							"seq15" => array(31,30,31,30,31,30,31,30,31,30,31,30),
							"seq16" => array(31,30,31,30,31,30,31,30,31,30,31,30));
	
	$daynames =		array(	1 => 'Caturday', 2 => 'Woofaday', 3 => 'Tweeturday',
							4 => 'Neighurday', 5 => 'Roarurday', 6 => 'Bloopurday',
							7 => 'Noneday');
	
	$monthnames =	array(	1 => 'Newaweeks', 2 => 'Treaturweeks', 3 => 'Stallionise',
							4 => 'Scientifica', 5 => 'Newdigitals', 6 => 'Midway',
							7 => 'Sportific', 8 => 'Believation', 9 => 'Alchemicism',
							10 => 'Foodrific', 11 => 'Activitism', 12 => 'Endrificism');
	
	$monthusage = isset($defiency) ? ${$defiency} : $deficient;
	
	// Code Segment 3 - Calculate month number, day number, day count etc.
	$i = 0;
	$ii = 0;
	$ttl_num = 0;
	$ttl_num_months = 0;
	$nodaycount = 0;
	$month=0;
	foreach($monthusage as $key => $item){
		$i++;
		foreach($item as $numdays){
			$ttl_num=$ttl_num+$numdays;
			$ttl_num_months++;
		}
	}
	// As well as Function RounCalendar
	$daypos = 0;
	$revolutionsperyear = $ttl_num / $i;
	$numyears = floor((ceil($roun) / $revolutionsperyear));
	$avg_num_month = $ttl_num_months/$i;
	$jtl = abs(abs($roun) - ceil($revolutionsperyear*($numyears+1)));
	while($month==0){
		$day=0;
		$u=0;
		foreach($monthusage as $key => $item){
			$t=0;
			foreach($item as $numdays){
				$t++;
				$tt=0;
				for($sh=1;$sh<=$numdays;$sh++){
					$ii=$ii+1;
					$tt++;
					if ($ii==floor($jtl)){
						if ($roun<0){
							$daynum = $tt;
							$month = $t;
						} else {
							$daynum = $numdays-($tt-1);
							$month = $avg_num_month-($t-1);
						}
						$sequence = $key;
						$nodaycount=true;
					}
				}
				if ($nodaycount==false)
				{
					$day++;
					$daypos++;
					if ($daypos>count($daynames))
						$daypos=1;
				}
			}
			$u++;
		}
	}
	$timer = substr($roun, strpos($roun,'.')+1,strlen($roun)-strpos($roun,'.')-1);
	$roun_out= $numyears.'/'.$month.'/'.$daynum.' '.$day.'.'. floor(intval(substr($timer,0,2))/100*$timeset['hours']).':'. floor(intval(substr($timer,2,2))/100*$timeset['minutes']).':'. floor(intval(substr($timer,4,2))/100*$timeset['seconds']).'.'.substr($timer,6,strlen($timer)-6) . ' (' . $monthnames[$month] . ' ' . $daynames[$daypos] .')';
	$roun_obj = array('dayname'=>$daynames[$daypos], 'monthname'=>$monthnames[$month], 'stardate'=>"$numyears.$day", 'rounfloat' => $roun, 'year'=>$numyears,'month'=>$month, 'day'=>$daynum, 'jtl'=>$jtl, 'day_count'=>$day,'hours'=>floor(intval(substr($timer,0,2))/100*$timeset['hours']),'minute'=> floor(intval(substr($timer,2,2))/100*$timeset['minutes']),'seconds'=> floor(intval(substr($timer,4,2))/100*$timeset['seconds']),'microtime'=>substr($timer,6,strlen($timer)-6),'strout'=>$roun_out);
	return $roun_obj;
}
 



/**
 * BuddhistCalendar()
 * Buddhist Calendar using Unix Timestamp with Ounion Movement Sequence
 *
 * @param integer $unix_time			Unix Date/Time Stamp to Convert
 * @param double $gmt					GMT/UTC/DST of $unix_time stamp
 * @param string $poffset				Core Break Point of Calendar Cycle to Unix Timestamp and $pweight
 * @param float $pweight				Weighting Days Balancing Variable
 * @param float $defiency				Defeiency Monthly Cycle to Use
 * @param array $timeset				Maximum Hours, Minutes, Seconds in a Time Cycle
 * @return array
 */


function BuddhistCalendar($unix_time, $gmt, $poffset = '1970-03-10 12:00 PM', $pweight = '-960995.7712501', $defiency='nonedeficient', $timeset= array("hours" => 24, "minutes" => 60, "seconds" => 60))
    {
    // Code Segment 1 â€“ Calculate Floating Point
    $tme = $unix_time;

    if ($gmt>0){
        $gmt=-$gmt;
    } else {
        $gmt=abs($gmt);
    }
    
    $ptime = strtotime($poffset)+(60*60*$gmt);
    $weight = $pweight+(1*$gmt);

    $roun_xa = ($tme)/(24*60*60);
    $roun_ya = $ptime/(24*60*60);
    $roun = (($roun_xa -$roun_ya) - $weight)+(microtime(true)/999999);
    
    // Code Segment 2 â€“ Set month day arrays
	$nonedeficient = array("seq1" => array(29,30,29,30,29,30,29,30,29,30,29,30),
						   "seq2" => array(29,30,30,30,30,29,30,29,30,29,30,29,30),
						   "seq3" => array(29,30,29,30,29,30,29,30,29,30,29,30));

	$monthnames = array("seq1" => array('Tagu','Kason','Waso','Wagaung','Tawthalin',
										'Thadingyut','Tazaungmon','Natdaw','Pyatho',
										'Tabodwe','Tabaung'),
						"seq2" => array('Tagu','Kason','1st Waso','2nd Waso','Wagaung','Tawthalin',
										'Thadingyut','Tazaungmon','Natdaw','Pyatho',
										'Tabodwe','Tabaung'),
						"seq3" => array('Tagu','Kason','Waso','Wagaung','Tawthalin',
										'Thadingyut','Tazaungmon','Natdaw','Pyatho',
										'Tabodwe','Tabaung'));
										
    $monthusage = isset($defiency) ? ${$defiency} : $deficient;
    
    // Code Segment 3 â€“ Calculate month number, day number, day count etc
	$i = 0;
	$ii = 0;
	$ttl_num = 0;
	$ttl_num_months = 0;
	$nodaycount = 0;
	$month=0;
    foreach($monthusage as $key => $item){
        $i++;
        foreach($item as $numdays){
            $ttl_num=$ttl_num+$numdays;
            $ttl_num_months++;
        }
    }
    
   // You need to replace this section in Function EgyptianCalendar
	// As well as Function MayanTihkalCalendar
	$revolutionsperyear = $ttl_num / $i;
	$numyears = floor((ceil($roun) / $revolutionsperyear));
	$avg_num_month = $ttl_num_months/$i;
	$jtl = abs(abs($roun) - ceil($revolutionsperyear*($numyears+1)));
	while($month==0){
		$day=0;
		$u=0;
		foreach($monthusage as $key => $item){
			$t=0;   
			foreach($item as $numdays){
				$t++;
				$tt=0;
				for($sh=1;$sh<=$numdays;$sh++){
					$ii=$ii+1;
					$tt++;
					if ($ii==floor($jtl)){
						if ($roun<0){
							$daynum = floor($tt);
							$month = floor($t);
						} else {
							$daynum = floor($numdays-($tt-1));
							$month = floor($avg_num_month-($t-1));
						}
						$sequence = $key;
						$nodaycount=true;
					}
				}
				if ($nodaycount==false)
					$day++;
			}
			$u++;
		}
	}
    
	//$numyears = abs($numyears);
	
    $timer = substr($roun, strpos($roun,'.')+1,strlen($roun)-strpos($roun,'.')-1);
    $roun_out= $numyears.'/'.$month.'/'.$daynum.' '.$day.'.'. floor(intval(substr($timer,0,2))/100*$timeset['hours']).':'. floor(intval(substr($timer,2,2))/100*$timeset['minutes']).':'. floor(intval(substr($timer,4,2))/100*$timeset['seconds']).'.'.substr($timer,6,strlen($timer)-6);
 
    $roun_obj = array('stardate'=>"$numyears.$day", 'year'=>$numyears,'month'=>$month, 'mname' => $monthnames[$sequence][$month-1],'day'=>$daynum, 'jtl'=>$jtl, 'day_count'=>$day,'hours'=>floor(intval(substr($timer,0,2))/100*$timeset['hours']),'minute'=> floor(intval(substr($timer,2,2))/100*$timeset['minutes']),'seconds'=> floor(intval(substr($timer,4,2))/100*$timeset['seconds']),'microtime'=>substr($timer,6,strlen($timer)-6),'strout'=>$roun_out);

    return $roun_obj;
}




/**
 * VedicCalendar()
 * Vedic Calendar using Unix Timestamp with Ounion Movement Sequence
 *
 * @param integer $unix_time			Unix Date/Time Stamp to Convert
 * @param double $gmt					GMT/UTC/DST of $unix_time stamp
 * @param string $poffset				Core Break Point of Calendar Cycle to Unix Timestamp and $pweight
 * @param float $pweight				Weighting Days Balancing Variable
 * @param float $defiency				Defeiency Monthly Cycle to Use
 * @param array $timeset				Maximum Hours, Minutes, Seconds in a Time Cycle
 * @return array
 */
function VedicCalendar($unix_time, $gmt, $poffset = '1970-01-12 12:00 PM', $pweight = '-49183.75', $defiency='deficient', $timeset= array("hours" => 24, "minutes" => 60, "seconds" => 60))
    {
    // Code Segment 1 â€“ Calculate Floating Point
    $tme = $unix_time;

    if ($gmt>0){
        $gmt=-$gmt;
    } else {
        $gmt=abs($gmt);
    }
    
    $ptime = strtotime($poffset)+(60*60*$gmt);
    $weight = $pweight+(1*$gmt);

    $roun_xa = ($tme)/(24*60*60);
    $roun_ya = $ptime/(24*60*60);
    $roun = (($roun_xa -$roun_ya) - $weight)+(microtime(true)/999999);
    
    // Code Segment 2 â€“ Set month day arrays
	$deficient = array(	"seq1" => array(27,27,27,27,27,27,27,27,27,27,27,27),
					   	"seq2" => array(27,27,27,27,27,27,27,27,27,27,27,27),
					   	"seq3" => array(27,27,27,27,27,27,27,27,27,27,27,27),
					   	"seq4" => array(27,27,27,27,27,27,27,27,27,27,27,27),
						"seq5" => array(27,27,27,27,27,27,27,27,27,27,27,27),
						"seq6" => array(27,27,27,27,27,27,27,27,27,27,27,27),
						"seq7" => array(27,27,27,27,27,27,27,27,27,27,27,27),
						"seq8" => array(27,27,27,27,27,27,27,27,27,27,27,27),
						"seq9" => array(27,27,27,27,27,27,27,27,27,27,27,27),
						"seq10" => array(27,27,27,27,27,27,27,27,27,27,27,27),
						"seq11" => array(27,27,27,27,27,27,27,27,27,27,27,27),
					   	"seq12" => array(27,27,27,27,27,27,27,27,27,27,27,27),
					   	"seq13" => array(27,27,27,27,27,27,27,27,27,27,27,27),
					   	"seq14" => array(27,27,27,27,27,27,27,27,27,27,27,27),
						"seq15" => array(27,27,27,27,27,27,27,27,27,27,27,27),
						"seq16" => array(27,27,27,27,27,27,27,27,27,27,27,27),
						"seq17" => array(27,27,27,27,27,27,27,27,27,27,27,27),
						"seq18" => array(27,27,27,27,27,27,27,27,27,27,27,27),
						"seq19" => array(27,27,27,27,27,27,27,27,27,27,27,27),
						"seq20" => array(27,27,27,27,27,27,27,27,27,27,27,27),
						"seq21" => array(27,27,27,27,27,27,27,27,27,27,27,27),
					   	"seq22" => array(27,27,27,27,27,27,27,27,27,27,27,27),
					   	"seq23" => array(27,27,27,27,27,27,27,27,27,27,27,27),
					   	"seq24" => array(27,27,27,27,27,27,27,27,27,27,27,27),
						"seq25" => array(27,27,27,27,27,27,27,27,27,27,27,27),
						"seq26" => array(27,27,27,27,27,27,27,27,27,27,27,27),
						"seq27" => array(27,27,27,27,27,27,27,27,27,27,27,27),
						"seq28" => array(27,27,27,27,27,27,27,27,27,27,27,27),
						"seq29" => array(27,27,27,27,27,27,27,27,27,27,27,27),
						"seq30" => array(27,27,27,27,27,27,27,27,27,27,27,27),
						"seq31" => array(27,27,27,27,27,27,27,27,27,27,27,27),
					   	"seq32" => array(27,27,27,27,27,27,27,27,27,27,27,27),
					   	"seq33" => array(27,27,27,27,27,27,27,27,27,27,27,27),
					   	"seq34" => array(27,27,27,27,27,27,27,27,27,27,27,27),
						"seq35" => array(27,27,27,27,27,27,27,27,27,27,27,27),
						"seq36" => array(27,27,27,27,27,27,27,27,27,27,27,27),
						"seq37" => array(27,27,27,27,27,27,27,27,27,27,27,27),
						"seq38" => array(27,27,27,27,27,27,27,27,27,27,27,27),
						"seq39" => array(27,27,27,27,27,27,27,27,27,27,27,27),
						"seq40" => array(27,27,27,27,27,27,27,27,27,27,27,27),
						"seq41" => array(27,27,27,27,27,27,27,27,27,27,27,27),
					   	"seq42" => array(27,27,27,27,27,27,27,27,27,27,27,27),
					   	"seq43" => array(27,27,27,27,27,27,27,27,27,27,27,27),
					   	"seq44" => array(27,27,27,27,27,27,27,27,27,27,27,27),
						"seq45" => array(27,27,27,27,27,27,27,27,27,27,27,27),
						"seq46" => array(27,27,27,27,27,27,27,27,27,27,27,27),
						"seq47" => array(27,27,27,27,27,27,27,27,27,27,27,27),
						"seq48" => array(27,27,27,27,27,27,27,27,27,27,27,27),
						"seq49" => array(27,27,27,27,27,27,27,27,27,27,27,27),
						"seq50" => array(27,27,27,27,27,27,27,27,27,27,27,27),
						"seq51" => array(27,27,27,27,27,27,27,27,27,27,27,27),
					   	"seq52" => array(27,27,27,27,27,27,27,27,27,27,27,27),
					   	"seq53" => array(27,27,27,27,27,27,27,27,27,27,27,27),
					   	"seq54" => array(27,27,27,27,27,27,27,27,27,27,27,27),
						"seq55" => array(27,27,27,27,27,27,27,27,27,27,27,27),
						"seq56" => array(27,27,27,27,27,27,27,27,27,27,27,27),
						"seq57" => array(27,27,27,27,27,27,27,27,27,27,27,27),
						"seq58" => array(27,27,27,27,27,27,27,27,27,27,27,27),
						"seq59" => array(27,27,27,27,27,27,27,27,27,27,27,27),
						"seq60" => array(27,27,27,27,27,27,27,27,27,27,27,27));

	$daynames = array();
	foreach( array(	'Viṣkambha','Prīti','Āyuśmān','Saubhāgya','Śobhana',
						'Atigaṇḍa','Sukarma','Dhṛti','Śūla','Gaṇḍa','Vṛddhi',
						'Dhruva', 'Vyāghatā', 'Harṣaṇa', 'Vajra', 'Siddhi', 'Vyatipāta',
						'Variyas', 'Parigha', 'Śiva', 'Siddha', 'Sādhya', 'Śubha',
						'Śukla', 'Brahma', 'Māhendra', 'Vaidhṛti') as $day) {
		$daynames[] = htmlspecialchars($day);
	}
	
	$monthnames = array();
	foreach( array('Chaitra', 'Vaiśākha', 'Jyeṣṭha', 'Āṣāḍha', 'Śrāvaṇa',
						'Bhādrapada', 'Āśvina', 'Kārtika', 'Agrahāyaṇa', 'Pauṣa', 'Māgha', 'Phālguna') as $month)  {
		$monthnames[] = htmlspecialchars($month);
	}
	
										
    $monthusage = isset($defiency) ? ${$defiency} : $deficient;
    
    // Code Segment 3 â€“ Calculate month number, day number, day count etc
	$i = 0;
	$ii = 0;
	$ttl_num = 0;
	$ttl_num_months = 0;
	$nodaycount = 0;
	$month=0;
    foreach($monthusage as $key => $item){
        $i++;
        foreach($item as $numdays){
            $ttl_num=$ttl_num+$numdays;
            $ttl_num_months++;
        }
    }
    
   // You need to replace this section in Function EgyptianCalendar
	// As well as Function MayanTihkalCalendar
	$revolutionsperyear = $ttl_num / $i;
	$numyears = floor((ceil($roun) / $revolutionsperyear));
	$avg_num_month = $ttl_num_months/$i;
	$jtl = abs(abs($roun) - ceil($revolutionsperyear*($numyears+1)));
	while($month==0){
		$day=0;
		$u=0;
		foreach($monthusage as $key => $item){
			$t=0;   
			foreach($item as $numdays){
				$t++;
				$tt=0;
				for($sh=1;$sh<=$numdays;$sh++){
					$ii=$ii+1;
					$tt++;
					if ($ii==floor($jtl)){
						if ($roun<0){
							$daynum = floor($tt);
							$month = floor($t);
						} else {
							$daynum = floor($numdays-($tt-1));
							$month = floor($avg_num_month-($t-1));
						}
						$sequence = $key;
						$nodaycount=true;
					}
				}
				if ($nodaycount==false)
					$day++;
			}
			$u++;
		}
	}
    
	//$numyears = abs($numyears);
	
    $timer = substr($roun, strpos($roun,'.')+1,strlen($roun)-strpos($roun,'.')-1);
    $roun_out= $numyears.'/'.$month.'/'.$daynum.' '.$day.'.'. floor(intval(substr($timer,0,2))/100*$timeset['hours']).':'. floor(intval(substr($timer,2,2))/100*$timeset['minutes']).':'. floor(intval(substr($timer,4,2))/100*$timeset['seconds']).'.'.substr($timer,6,strlen($timer)-6);
 
    $verdic_obj = array('stardate'=>"$numyears.$day", 'year'=>$numyears,'month'=>$month, 'mname' => $monthnames[$month-1], 'dname' => $daynames[$daynum-1],'day'=>$daynum, 'jtl'=>$jtl, 'day_count'=>$day,'hours'=>floor(intval(substr($timer,0,2))/100*$timeset['hours']),'minute'=> floor(intval(substr($timer,2,2))/100*$timeset['minutes']),'seconds'=> floor(intval(substr($timer,4,2))/100*$timeset['seconds']),'microtime'=>substr($timer,6,strlen($timer)-6),'strout'=>$roun_out);

    return $verdic_obj;
}




/**
 * GregorianCalendar()
 * Gregorian Calendar using Unix Timestamp with Ounion Movement Sequence
 *
 * @param integer $unix_time			Unix Date/Time Stamp to Convert
 * @param double $gmt					GMT/UTC/DST of $unix_time stamp
 * @param string $poffset				Core Break Point of Calendar Cycle to Unix Timestamp and $pweight
 * @param float $pweight				Weighting Days Balancing Variable
 * @return array
 */
function GregorianCalendar($unix_time, $gmt, $poffset = 'NOW', $pweight = 0)
    {
    // Code Segment 1 â€“ Calculate Floating Point
    $tme = $unix_time;

    if ($gmt>0){
        $gmt=-$gmt;
    } else {
        $gmt=abs($gmt);
    }
    
	if ($poffset == 'NOW') {
		$diff = 0+(60*60*$gmt);
	} else {
		$diff = abs(time()-strtotime($poffset))+(60*60*$gmt);
	}
	
	$roun = (($unix_time - $diff) - $pweight);	
    $timer = substr($roun, strpos($roun,'.')+1,strlen($roun)-strpos($roun,'.')-1);
    $roun_out= date('Y/m/d j', $roun) . ' '. date('G', $roun).':'.date('i', $roun).':'.date('s', $roun).'.'.substr($timer,6,strlen($timer)-6).' ('.date('F l', $roun).')';
    $roun_obj = array('stardate'=>date('Y.j', $roun), 'year'=>date('Y', $roun),'month'=>date('m', $roun), 'mname' => date('F', $roun),'day'=>date('d', $roun), 'jtl'=>$roun, 'day_count'=>date('j', $roun),'hours'=>date('H', $roun),'minute'=> date('M', $roun),'seconds'=> date('s', $roun),'microtime'=>$timer,'strout'=>$roun_out);

    return $roun_obj;
}




/**
 * EgyptianCalendar()
 * Egyptian Calendar using Unix Timestamp with Ounion Movement Sequence
 *
 * @param integer $unix_time			Unix Date/Time Stamp to Convert
 * @param double $gmt					GMT/UTC/DST of $unix_time stamp
 * @param string $poffset				Core Break Point of Calendar Cycle to Unix Timestamp and $pweight
 * @param float $pweight				Weighting Days Balancing Variable
 * @param float $defiency				Defeiency Monthly Cycle to Use
 * @param array $timeset				Maximum Hours, Minutes, Seconds in a Time Cycle
 * @return array
 */
function EgyptianCalendar($unix_time, $gmt, $poffset = '1970-02-26 7:45 PM', $pweight = '-9777600.22222222223', $defiency='nonedeficient', $timeset= array("hours" => 24, "minutes" => 60, "seconds" => 60))
    {
    // Code Segment 1 â€“ Calculate Floating Point
    $tme = $unix_time;

    if ($gmt>0){
        $gmt=-$gmt;
    } else {
        $gmt=abs($gmt);
    }
    
    $ptime = strtotime($poffset)+(60*60*$gmt);
    $weight = $pweight+(1*$gmt);

    $roun_xa = ($tme)/(24*60*60);
    $roun_ya = $ptime/(24*60*60);
    $roun = (($roun_xa -$roun_ya) - $weight)+(microtime(true)/999999);
    
    // Code Segment 2 â€“ Set month day arrays
    $nonedeficient = array("seq1" => array(30,30,30,30,30,30,30,30,30,30,30,30,5));

	$monthnames = array("seq1" => array('Thoth','Phaophi','Athyr','Choiak','Tybi',
										'Mecheir','Phamenoth','Pharmuthi','Pachon',
										'Payni','Epiphi','Mesore','epagomenai'));
										
    $monthusage = isset($defiency) ? ${$defiency} : $deficient;
    
    // Code Segment 3 â€“ Calculate month number, day number, day count etc
	$i = 0;
	$ii = 0;
	$ttl_num = 0;
	$ttl_num_months = 0;
	$nodaycount = 0;
	$month=0;
    foreach($monthusage as $key => $item){
        $i++;
        foreach($item as $numdays){
            $ttl_num=$ttl_num+$numdays;
            $ttl_num_months++;
        }
    }
    
   // You need to replace this section in Function EgyptianCalendar
	// As well as Function MayanTihkalCalendar
	$revolutionsperyear = $ttl_num / $i;
	$numyears = floor((ceil($roun) / $revolutionsperyear));
	$avg_num_month = $ttl_num_months/$i;
	$jtl = abs(abs($roun) - ceil($revolutionsperyear*($numyears+1)));
	while($month==0){
		$day=0;
		$u=0;
		foreach($monthusage as $key => $item){
			$t=0;   
			foreach($item as $numdays){
				$t++;
				$tt=0;
				for($sh=1;$sh<=$numdays;$sh++){
					$ii=$ii+1;
					$tt++;
					if ($ii==floor($jtl)){
						if ($roun<0){
							$daynum = $tt;
							$month = $t;
						} else {
							$daynum = $numdays-($tt-1);
							$month = $avg_num_month-($t-1);
						}
						$sequence = $key;
						$nodaycount=true;
					}
				}
				if ($nodaycount==false)
					$day++;
			}
			$u++;
		}
	}
    
	//$numyears = abs($numyears);
	
    $timer = substr($roun, strpos($roun,'.')+1,strlen($roun)-strpos($roun,'.')-1);
    $roun_out= $numyears.'/'.$month.'/'.$daynum.' '.$day.'.'. floor(intval(substr($timer,0,2))/100*$timeset['hours']).':'. floor(intval(substr($timer,2,2))/100*$timeset['minutes']).':'. floor(intval(substr($timer,4,2))/100*$timeset['seconds']).'.'.substr($timer,6,strlen($timer)-6);
 
    $roun_obj = array('stardate'=>"$numyears.$day", 'year'=>$numyears,'month'=>$month, 'mname' => $monthnames[$sequence][$month-1],'day'=>$daynum, 'jtl'=>$jtl, 'day_count'=>$day,'hours'=>floor(intval(substr($timer,0,2))/100*$timeset['hours']),'minute'=> floor(intval(substr($timer,2,2))/100*$timeset['minutes']),'seconds'=> floor(intval(substr($timer,4,2))/100*$timeset['seconds']),'microtime'=>substr($timer,6,strlen($timer)-6),'strout'=>$roun_out);

    return $roun_obj;
}



/**
 * MayanTihkalCalendar()
 * Mayan Tihkal Calendar using Unix Timestamp with Ounion Movement Sequence
 *
 * @param integer $unix_time			Unix Date/Time Stamp to Convert
 * @param double $gmt					GMT/UTC/DST of $unix_time stamp
 * @param string $poffset				Core Break Point of Calendar Cycle to Unix Timestamp and $pweight
 * @param array $pppo					$poffset array for Mayan Long Count of offset point
 * @param string $pweight				Weighting Days Balancing Variable
 * @param float $defiency				Defeiency Monthly Cycle to Use
 * @param array $timeset				Maximum Hours, Minutes, Seconds in a Time Cycle Array
 * @return array
 */
function MayanTihkalCalendar($unix_time, $gmt, $poffset = '2012-12-21 8:24 PM', $pppo = array(13,0,0,0,0), $pweight = '-1872000.00000001', $defiency='nonedeficient', $timeset= array("hours" => 24, "minutes" => 60, "seconds" => 60))
    {
    // Code Segment 1 â€“ Calculate Floating Point
    $tme = $unix_time;

    if ($gmt>0){
        $gmt=-$gmt;
    } else {
        $gmt=abs($gmt);
    }
    
    $ptime = strtotime($poffset)+(60*60*$gmt);

    $roun_xa = ($tme)/(24*60*60);
    $roun_ya = $ptime/(24*60*60);
    $roun = (($roun_xa -$roun_ya) - $pweight)+(microtime(true)/999999);
    
    // Code Segment 2 â€“ Set month day arrays
    $nonedeficient = array("seq1" => array(20,20,20,20,20,20,20,20,20,20,20,20,20,20,20,20,20,20,5));

	$monthnames = array("seq1" => array('Pop', 'Uo', 'Zip', 'Zot\'z', 'Tzec', 'Xul', 'Yaxkin', 'Mol', 
										'Ch\'en', 'Yax', 'Zac', 'Ceh', 'Mac', 'Kankin', 'Muan', 'Pax', 
										'Kayab', 'Cumku', 'Uayeb'));
	$daynames = array("seq1" => array('Imix', 'Ik', 'Akbal', 'Kan', 'Chicchan', 'Cimi','Manik', 'Lamat', 
									  'Muluc', 'Oc', 'Chuen', 'Eb', 'Ben', 'Ix', 'Men', 'Cib', 'Caban', 
									  'Etz\'nab', 'Cauac', 'Ahau'));
	
    $monthusage = isset($defiency) ? ${$defiency} : $deficient;
    
    // Code Segment 3 â€“ Calculate month number, day number, day count etc
	$i = 0;
	$ii = 0;
	$ttl_num = 0;
	$ttl_num_months = 0;
	$nodaycount = 0;
	$month = 0;
    foreach($monthusage as $key => $item){
        $i++;
        foreach($item as $numdays){
            $ttl_num=$ttl_num+$numdays;
            $ttl_num_months++;
        }
    }
    
	// As well as Function MayanTihkalCalendar
	$revolutionsperyear = $ttl_num / $i;
	$numyears = floor((ceil($roun) / $revolutionsperyear));
	$avg_num_month = $ttl_num_months/$i;
	$jtl = abs(abs($roun) - ceil($revolutionsperyear*($numyears+1)));
	while($month==0){
		$day=0;
		$u=0;
		foreach($monthusage as $key => $item){
			$t=0;   
			foreach($item as $numdays){
				$t++;
				$tt=0;
				for($sh=1;$sh<=$numdays;$sh++){
					$ii=$ii+1;
					$tt++;
					if ($ii==floor($jtl)){
						if ($roun<0){
							$daynum = $tt;
							$month = $t;
						} else {
							$daynum = $numdays-($tt-1);
							$month = $avg_num_month-($t-1);
						}
						$sequence = $key;
						$nodaycount=true;
					}
				}
				if ($nodaycount==false)
					$day++;
			}
			$u++;
		}
	}
    
    $timer = substr($roun, strpos($roun,'.')+1,strlen($roun)-strpos($roun,'.')-1);
    $roun_out= $numyears.'/'.$month.'/'.$daynum.' '.$day.'.'. floor(intval(substr($timer,0,2))/100*$timeset['hours']).':'. floor(intval(substr($timer,2,2))/100*$timeset['minutes']).':'. floor(intval(substr($timer,4,2))/100*$timeset['seconds']).'.'.substr($timer,6,strlen($timer)-6);
    $roun_obj = array('stardate'=>"$numyears.$day", 'year'=>abs($numyears),'month'=>$month, 'mname' => $monthnames[$sequence][$month-1],'day'=>$daynum, 'dayname'=>$daynames[$sequence][$daynum-1], 'day'=>$daynum, 'jtl'=>$jtl, 'day_count'=>$day,'hours'=>floor(intval(substr($timer,0,2))/100*$timeset['hours']),'minute'=> floor(intval(substr($timer,2,2))/100*$timeset['minutes']),'seconds'=> floor(intval(substr($timer,4,2))/100*$timeset['seconds']),'microtime'=>substr($timer,6,strlen($timer)-6),'strout'=>$roun_out);
	$roun_obj = array_merge($roun_obj, array('longcount'=>MayanLongCount($tme, $gmt, $roun_obj, $poffset, $pppo)));
    return $roun_obj;
}

/**
 * MayanLongCount()
 * Mayan Long Count Calendar using Unix Timestamp with Counter Movement Sequence
 *
 * @param integer $tme					Unix Date/Time Stamp to Convert
 * @param double $gmt					GMT/UTC/DST of $unix_time stamp
 * @param array $pmtc					Mayan Tihkal Calendar Array
 * @param string $poffset				Core Break Point of Calendar Cycle to Unix Timestamp and $pweight
 * @param array $pppo					Mayan Long Count Calendar Date
 * @return array
 */
function MayanLongCount($tme, $gmt, $pmtc, $poffset = '2012-12-21 8:24 PM', $pppo = array(13,0,0,0,0)){
	$epoch = strtotime($poffset);
	$diff=(($tme-$epoch)/(60*60*24));   
    	$ptime = $diff+((60*60*$gmt)/(60*60*24));
	$ppo = changemaya($pppo,ceil($ptime));
	return array('ppo' => $ppo[0].'.'.$ppo[1].'.'.$ppo[2].'.'.$ppo[3].'.'.$ppo[4], 'stardate' => $pmtc['year'] . '.' . $ppo[4]);
}

/**
 * changemaya()
 * Mayan Long Count Calendar Day Counter Movement Sequence
 *
 * @param array $ppo			Mayan Calendar Array
 * @param integer $diff			Difference of Change in Days
 * @return array
 */
function changemaya($ppo,$diff){
	if ($diff>0) { $amount=1; } else { $amount=-1; }
	for ($sh=1;$sh<abs($diff);$sh++){
		if ($ppo[4]+$amount>20){
			if ($ppo[3]+$amount>20){
				if ($ppo[2]+$amount>20){
					if ($ppo[1]+$amount>20){
						if ($ppo[0]+$amount>20){
							$ppo[0]=0;
							$ppo[1]=0;
							$ppo[2]=0;
							$ppo[3]=0;
							$ppo[4]=0;
						} else {
							$ppo[1]=0;
							$ppo[0]=$ppo[0]+$amount;
						}		
					} else {
						$ppo[2]=0;
						$ppo[1]=$ppo[1]+$amount;
					}		
				} else {
					$ppo[3]=0;
					$ppo[2]=$ppo[2]+$amount;
				}
			} else {
				$ppo[4]=0;
				$ppo[3]=$ppo[3]+$amount;
			}
		} elseif ($ppo[4]+$amount<0){
			if ($ppo[3]+$amount<0){
				if ($ppo[2]+$amount<0){
					if ($ppo[1]+$amount<0){
						if ($ppo[0]+$amount<0){
							$ppo[0]=20;
							$ppo[1]=0;
							$ppo[2]=0;
							$ppo[3]=0;
							$ppo[4]=0;
						} else {
							$ppo[1]=20;
							$ppo[0]=$ppo[0]+$amount;
						}		
					} else {
						$ppo[2]=20;
						$ppo[1]=$ppo[1]+$amount;
					}		
				} else {
					$ppo[3]=20;
					$ppo[2]=$ppo[2]+$amount;
				}
			} else {
				$ppo[4]=20;
				$ppo[3]=$ppo[3]+$amount;
			}
		} else {
			$ppo[4]=$ppo[4]+$amount;
		}
	}
	return $ppo;
	
}


/**
 * MayanLongCountSeconds()
 * Mayan Hour Long Count Calendar using Unix Timestamp with Counter Movement Sequence
 *
 * @param integer $tme					Unix Date/Time Stamp to Convert
 * @param double $gmt					GMT/UTC/DST of $unix_time stamp
 * @param array $ptch					Mayan Tihkal Calendar Array
 * @param string $poffset				Core Break Point of Calendar Cycle to Unix Timestamp and $pweight
 * @param array $pppo					Weighting Days Balancing Variable
 * @return array
 */
function MayanLongCountHours($tme, $gmt, $ptch, $poffset = '2012-12-21 8:24 PM', $pppo = array(0,0,0,0)){
	$epoch = strtotime($poffset);
	$tme = $tme-strtotime(date('Y/m/d',$tme));
	$day = strtotime(date('Y/m/d',strtotime($poffset))) - strtotime($poffset);
	$diff=$tme-$day;   
        $ptime = $diff+(60*60*$gmt);
	$ppo = changemayahours($pppo,ceil($ptime));
	return array('ppo' => $ppo[0].'.'.$ppo[1].'.'.$ppo[2].'.'.$ppo[3], 'stardate'=>$ptch['year'].$ppo[3]);
}

/**
 * changemayaseconds()
 * Mayan Hour Count Calendar Day Counter Movement Sequence
 *
 * @param array $ppo			Mayan Hour count Calendar Array
 * @param integer $diff			Difference of Change in Seconds
 * @return array
 */
function changemayahours($ppo,$diff){
	if ($diff>0) { $amount=1; } else { $amount=-1; }
	for ($sh=1;$sh<abs($diff);$sh++){
		if ($ppo[3]+$amount>20){
			if ($ppo[2]+$amount>20){
				if ($ppo[1]+$amount>20){
					if ($ppo[0]+$amount>20){
						$ppo[0]=0;
						$ppo[1]=0;
						$ppo[2]=0;
						$ppo[3]=0;
					} else {
						$ppo[1]=0;
						$ppo[0]=$ppo[0]+$amount;
					}		
				} else {
					$ppo[2]=0;
					$ppo[1]=$ppo[1]+$amount;
				}
			} else {
				$ppo[3]=0;
				$ppo[2]=$ppo[2]+$amount;
			}
		} elseif ($ppo[3]+$amount<0){
			if ($ppo[2]+$amount<0){
				if ($ppo[1]+$amount<0){
					if ($ppo[0]+$amount<0){
						$ppo[0]=20;
						$ppo[1]=0;
						$ppo[2]=0;
						$ppo[3]=0;
					} else {
						$ppo[1]=20;
						$ppo[0]=$ppo[0]+$amount;
					}		
				} else {
					$ppo[2]=20;
					$ppo[1]=$ppo[1]+$amount;
				}		
			} else {
				$ppo[3]=20;
				$ppo[2]=$ppo[2]+$amount;
			}
		} else {
			$ppo[3]=$ppo[3]+$amount;
		}
	}
	return $ppo;
	
}


/**
 * MayanLongCountSeconds()
 * Mayan Hour Long Count Calendar using Unix Timestamp with Counter Movement Sequence
 *
 * @param integer $tme					Unix Date/Time Stamp to Convert
 * @param double $gmt					GMT/UTC/DST of $unix_time stamp
 * @param array $ptch					Mayan Tihkal Calendar Array
 * @param string $poffset				Core Break Point of Calendar Cycle to Unix Timestamp and $pweight
 * @param array $pppo					Weighting Days Balancing Variable
 * @return array
 */
function MayanLongCountMinutes($tme, $gmt, $ptch, $poffset = '2012-12-21 8:24 PM', $pppo = array(0,0,0)){
	$epoch = strtotime($poffset);
	$tme = $tme-strtotime(date('Y/m/d H:00',$tme));
	$minutes = strtotime(date('Y/m/d H:i',strtotime($poffset))) - strtotime(date('Y/m/d H:00',strtotime($poffset)));
	$diff=$tme-$minutes;   
    	$ptime = $diff+(60*$gmt);
	$ppo = changemayaminutes($pppo,ceil($ptime));
	return array('ppo' => $ppo[0].'.'.$ppo[1].'.'.$ppo[2], 'stardate'=>$ptch['year'].$ppo[2]);
}

/**
 * changemayaseconds()
 * Mayan Hour Count Calendar Day Counter Movement Sequence
 *
 * @param array $ppo			Mayan Hour count Calendar Array
 * @param integer $diff			Difference of Change in Seconds
 * @return array
 */
function changemayaminutes($ppo,$diff){
	if ($diff>0) { $amount=1; } else { $amount=-1; }
	for ($sh=1;$sh<abs($diff);$sh++){
		if ($ppo[2]+$amount>20){
			if ($ppo[1]+$amount>20){
				if ($ppo[0]+$amount>20){
					$ppo[0]=0;
					$ppo[1]=0;
					$ppo[2]=0;
				} else {
					$ppo[1]=0;
					$ppo[0]=$ppo[0]+$amount;
				}		
			} else {
				$ppo[2]=0;
				$ppo[1]=$ppo[1]+$amount;
			}
		} elseif ($ppo[2]+$amount<0){
			if ($ppo[1]+$amount<0){
				if ($ppo[0]+$amount<0){
					$ppo[0]=20;
					$ppo[1]=0;
					$ppo[2]=0;
				} else {
					$ppo[1]=20;
					$ppo[0]=$ppo[0]+$amount;
				}		
			} else {
				$ppo[2]=20;
				$ppo[1]=$ppo[1]+$amount;
			}		
		} else {
			$ppo[1]=20;
			$ppo[0]=$ppo[1]+$amount;
		}
		
	}
	return $ppo;
	
}


/**
 * MayanLongCountSeconds()
 * Mayan Hour Long Count Calendar using Unix Timestamp with Counter Movement Sequence
 *
 * @param integer $tme					Unix Date/Time Stamp to Convert
 * @param double $gmt					GMT/UTC/DST of $unix_time stamp
 * @param array $ptch					Mayan Tihkal Calendar Array
 * @param string $poffset				Core Break Point of Calendar Cycle to Unix Timestamp and $pweight
 * @param array $pppo					Weighting Days Balancing Variable
 * @return array
 */
function MayanLongCountSeconds($tme, $gmt, $ptch, $poffset = '2012-12-21 8:24 PM', $pppo = array(0,0)){
	$epoch = strtotime($poffset);
	$tme = $tme-strtotime(date('Y/m/d H:i:00',$tme));
	$seconds = strtotime(date('Y/m/d H:i:s',strtotime($poffset))) - strtotime(date('Y/m/d H:i', strtotime($poffset)));
	$diff=$tme-$seconds;   
    	$ptime = $diff + $gmt;
	$ppo = changemayaseconds($pppo,ceil($ptime));
	return array('ppo' => $ppo[0].'.'.$ppo[1], 'stardate'=>$ptch['year'].$ppo[1]);
}

/**
 * changemayaseconds()
 * Mayan Hour Count Calendar Day Counter Movement Sequence
 *
 * @param array $ppo			Mayan Hour count Calendar Array
 * @param integer $diff			Difference of Change in Seconds
 * @return array
 */
function changemayaseconds($ppo,$diff){
	if ($diff>0) { $amount=1; } else { $amount=-1; }
	for ($sh=1;$sh<abs($diff);$sh++){
		if ($ppo[1]+$amount>20){
			if ($ppo[0]+$amount>20){
				$ppo[0]=0;
				$ppo[1]=0;
			} else {
				$ppo[1]=0;
				$ppo[0]=$ppo[0]+$amount;
			}		
		} elseif ($ppo[1]+$amount<0){
			if ($ppo[0]+$amount<0){
				$ppo[0]=20;
				$ppo[1]=0;
			} else {
				$ppo[1]=20;
				$ppo[0]=$ppo[0]+$amount;
			}		
		} else {
			$ppo[1]=20;
			$ppo[0]=$ppo[0]+$amount;
		}	
	}	
	return $ppo;
	
}

/**
 * jde_date_create()
 * Create Julian Day Count from Gregorian Date Stamp
 *
 * @param integer $month		Gregorian Month Count 1 - 12
 * @param integer $day			Gregorian Day Count 1 - 31
 * @param integer $year			Gregorian Year Count
 * @return float
 */
function jde_date_create($month, $day, $year){

	if ($month ==0) {
		$month = date('m');
	}
	
	if ($day ==0) {
		$day = date('d');
	}
	
	if ($year ==0) {
		$year = date('Y');
	}
	
   /*
   *  NOTd: $month and $day CANNOT have leading zeroes, 
   *        $year must be'YYYY' format
   */
   $jde_year_prefix = substr($year, 0, 1) - 1;
   $jde_year_suffix = substr($year, -2);
   
   //note that valid years for mktime are 1902-2037
   $timestamp = mktime(0,0,0,$month, $day, $year);
   $baseline_timestamp = mktime(0,0,0,1,0,$year);
   
   $day_count = round(($timestamp - $baseline_timestamp)/86400);
   $day_count_padded = str_pad($day_count,3,"0",STR_PAD_LEFT);

   return ($jde_year_prefix . $jde_year_suffix . $day_count_padded);
   
}


if (!function_exists('cal_days_in_month')){
	/**
	 * cal_days_in_month()
	 * Calculate total days in month
	 *
	 * @param string $a_null		NULL
	 * @param integer $a_month		Gregorian Month Count 1 - 12
	 * @param integer $a_year		Gregorian Year Count
	 * @return integer
	 */
	function cal_days_in_month($a_null, $a_month, $a_year) {
    	return date('t', mktime(0, 0, 0, $a_month+1, 0, $a_year));
    }
}

if (!function_exists('cal_to_jd')){
	/**
	 * cal_to_jd()
	 * Calculate Julian Day Count from Gregorian Date Seed
	 *
	 * @param string $a_null		NULL
	 * @param integer $a_month		Gregorian Month Count 1 - 12
	 * @param integer $a_day		Gregorian Day Count 1 - 32
	 * @param integer $a_year		Gregorian Year Count
	 * @return integer
	 */
	function cal_to_jd($a_null, $a_month, $a_day, $a_year){
   		if ( $a_month <= 2 ){
   			$a_month = $a_month + 12 ;
            $a_year = $a_year - 1 ;
   		}
   		$A = intval($a_year/100);
   		$B = intval($A/4) ;
   		$C = 2-$A+$B ;
   		$E = intval(365.25*($a_year+4716)) ;
   		$F = intval(30.6001*($a_month+1));
   		return intval($C+$a_day+$E+$F-1524) ;
	}
}


if (!function_exists('get_jd_dmy')) {

	/**
	 * get_jd_dmy()
	 * Gets Julian Date Stamp in Month, Day, Year from Julian Date Decimal Seed
	 *
	 * @param double $a_jd			Julian Date Seed
	 * @return array
	 */
	function get_jd_dmy($a_jd){
		$W = intval(($a_jd - 1867216.25)/36524.25) ;
		$X = intval($W/4) ;
		$A = $a_jd+1+$W-$X ;
		$B = $A+1524 ;
		$C = intval(($B-122.1)/365.25) ;
		$D = intval(365.25*$C) ;
		$E = intval(($B-$D)/30.6001) ;
		$F = intval(30.6001*$E) ;
		$a_day = $B-$D-$F ;
		if ( $E > 13 ) { 
			$a_month=$E-13 ;
			$a_year = $C-4715 ; 
		} else {
			$a_month=$E-1 ;
			$a_year=$C-4716 ;
     	}
     	return array($a_month, $a_day, $a_year) ;
	}
}



if (!function_exists('jdmonthname')) {
	/**
	 * jdmonthname()
	 * Gets Julian Date Month Name Stamp from Julian Date Decimal Seed with mode
	 *
	 * @param double $a_jd			Julian Date Seed
	 * @param boolean $a_mode		Return Mode
	 * @return string
	 */
	function jdmonthname($a_jd,$a_mode){
    	$tmp = get_jd_dmy($a_jd) ;
    	$a_time = "$tmp[0]/$tmp[1]/$tmp[2]" ;
    	switch($a_mode) {
    	case 0:
    		return strftime("%b",strtotime("$a_time")) ;
    	case 1:
    		return strftime("%B",strtotime("$a_time")) ;
        }
    }
}



if (!function_exists('jddayofweek')) {
	/**
	 * jddayofweek()
	 * Gets Julian Date Day of Week from Julian Date Decimal Seed with mode
	 *
	 * @param double $a_jd			Julian Date Seed
	 * @param integer $a_mode		Return Mode
	 * @return string
	 */
	function jddayofweek($a_jd,$a_mode){
		$tmp = get_jd_dmy($a_jd) ;
		$a_time = "$tmp[0]/$tmp[1]/$tmp[2]" ;
		switch($a_mode) {
		case 1:
			return strftime("%A",strtotime("$a_time")) ;
		case 2:
			return strftime("%a",strtotime("$a_time")) ;
		default:
			return strftime("%w",strtotime("$a_time")) ;
		}
	}
}

$j_month_name 		= array(	"", "Farvardin", "Ordibehesht", "Khordad", "Tir",
								"Mordad", "Shahrivar", "Mehr", "Aban", "Azar",
								"Dey", "Bahman", "Esfand"							);


/**
 * div()
 * Divide A by B
 *
 * @param float $a		Divisable
 * @param float $b		Divider
 * @return string
 */
function div($a, $b)
{
   return (integer) ($a / $b);
} 

/**
 * gregorian_to_jalali()
 * Gregorian calendar to Jalali Calendar
 *
 * @param integer $g_y		Gregorian Year
 * @param integer $g_m		Gregorian Month 1 - 12
 * @param integer $g_d		Gregorian Day 1 - 31
 * @return array
 */
function gregorian_to_jalali($g_y, $g_m, $g_d)
{
	$g_days_in_month 	= array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
	$j_days_in_month 	= array(31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29);
	$gy = $g_y-1600;
	$gm = $g_m-1;
	$gd = $g_d-1;
	$g_day_no = 365*$gy+div($gy+3,4)-div($gy+99,100)+div($gy+399,400);
	for ($i=0; $i < $gm; ++$i)
		$g_day_no += $g_days_in_month[$i];
	if ($gm>1 && (($gy%4==0 && $gy%100!=0) || ($gy%400==0)))
		++$g_day_no;
	$g_day_no += $gd;
	$j_day_no = $g_day_no-79;
	$j_np = div($j_day_no, 12053);
	$j_day_no %= 12053;
	$jy = 979+33*$j_np+4*div($j_day_no,1461);
	$j_day_no %= 1461;
	if ($j_day_no >= 366) {
		$jy += div($j_day_no-1, 365);
		$j_day_no = ($j_day_no-1)%365;
	}
	for ($i = 0; $j_day_no >= $j_days_in_month[$i]; ++$i) {
		$j_day_no -= $j_days_in_month[$i];
	}
	$jm = $i+1;
	$jd = $j_day_no+1;
	return array($jy, $jm, $jd);
}

/**
 * farsinum()
 * farsinum jalali calendar to bit sequence
 *
 * @param string $str		Jalali Date String
 * @return string
 */
function farsinum($str)
{
	if (strlen($str) == 1)
		$str = "0".$str;
	$out = "";
	for ($i = 0; $i < strlen($str); ++$i) {
		$c = substr($str, $i, 1); 
		$out .= pack("C*", 0xDB, 0xB0 + $c);
	}
	return $out;
}

if (!function_exists(date_format)){
	/**
	 * date_format()
	 * Date format for jalali calendar
	 *
	 * @param integer $datestamp		Unix Timestamp
	 * @return string
	 */
	function date_format($datestamp)
	{
		$tzoffset = 0;
		list($date,$time) = explode(" ",$datestamp);
		list($year,$month,$day) = explode("-",$date);
		list($hour,$minute,$second) = explode(":",$time);
		$hour = $hour + $tzoffset;
		list($jyear, $jmonth, $jday) = gregorian_to_jalali($year,$month,$day);
		$sDate = ($jyear - 1300)."/".($jmonth)."/".($jday)
				   ." - ".($hour).":".($minute);
		 return $sDate;
	}
}

/**
 * get_ordinal_suffix()
 * Proper ordinal suffix for a number jalali calendar
 *
 * @param integer $number		Number to have ordinal calculated
 * @return integer
 */
function get_ordinal_suffix ($number) {
	$last_2_digits = substr (0, -2, $number);
    if (($number % 10) == 1 && $last_2_digits != 11)
        return 'st';
    if (($number % 10) == 2 && $last_2_digits != 12)
        return 'nd';
    if (($number % 10) == 3 && $last_2_digits != 13)
        return 'rd';
    return 'th'; //default suffix
}



/**
 * gregorian2FrenchDateArray()
 * Gregorian to French Date Calculator
 *
 * @param integer $m		Gregorian Month 1 - 12
 * @param integer $d		Gregorian Day 1 - 31
 * @param integer $y		Gregorian Year
 * @return array
 */
function gregorian2FrenchDateArray($m, $d, $y)
{
    $julian_date = gregoriantojd($m, $d, $y);
    $french = jdtofrench($julian_date);
    if($french == "0/0/0")
        return "" ;
    $arD = split("/", $french) ;
    $monthname = FrenchMonthNames($arD[0]) ;
    $stryear = decrom($arD[2]) ;
    return array($monthname, $arD[1], $stryear ) ;
}

/**
 * FrenchMonthNames()
 * French Month Names
 *
 * @param integer $mo		French Month Number
 * @return string
 */
function FrenchMonthNames($mo)
{
    $arMo = array("VendÃ©miaire", 
                      "Brumaire",
                      "Frimaire",
                      "NivÃ´se", 
                      "PluviÃ´se",
                      "VentÃ´se", 
                      "Germinal",
                      "FlorÃ©al", 
                      "Prairial",
                      "Messidor", 
                      "Thermidor", 
                      "Fructidor",
                      "Sansculottide") ;
    if($mo < count($arMo)+1) 
        return $arMo[$mo-1] ;
}


/**
 * decrom()
 * Decrom from Number Calculator
 *
 * @param integer $value		Number to turn into a decrom
 * @return string
 */
function decrom($value){
	$digits	=	array(
    				1 => "I",
    				4 => "IV",
    				5 => "V",
    				9 => "IX",
    				10 => "X",
    				40 => "XL",
    				50 => "L",
    				90 => "XC",
    				100 => "C",
    				400 => "CD",
    				500 => "D",
    				900 => "CM",
    				1000 => "M"
       			);
       krsort($digits);
       foreach($digits as $key => $symbol){
			while($value>intval($key)) {
				$value = $value - intval($key);
				$ret .= $symbol;
			}
       }
       return $ret;
} 


/**
 * RomanCalendar()
 * Roman Calendar using Unix Timestamp with Ounion Movement Sequence
 *
 * @param integer $unix_time			Unix Date/Time Stamp to Convert
 * @param double $gmt					GMT/UTC/DST of $unix_time stamp
 * @param string $poffset				Core Break Point of Calendar Cycle to Unix Timestamp and $pweight
 * @param float $pweight				Weighting Days Balancing Variable
 * @param float $defiency				Defeiency Monthly Cycle to Use
 * @param array $timeset				Maximum Hours, Minutes, Seconds in a Time Cycle
 * @return array
 */
function RomanCalendar($unix_time, $gmt, $poffset = '1970-09-02 12:00 PM', $pweight = '-708219.75000001', $defiency='deficient', $timeset= array("hours" => 24, "minutes" => 60, "seconds" => 60))
    {
    // Code Segment 1 â€“ Calculate Floating Point
    $tme = $unix_time;

    if ($gmt>0){
        $gmt=-$gmt;
    } else {
        $gmt=abs($gmt);
    }
    
	$ptime 				= strtotime($poffset)+(60*60*$gmt);
    $weight 			= $pweight+(1*$gmt);
    $roun_xa 			= ($tme)/(24*60*60);
    $roun_ya 			= $ptime/(24*60*60);
    $roun 				= (($roun_xa -$roun_ya) - $weight)+(microtime(true)/999999);
	
    // Code Segment 1 â€“ Set month day arrays
	$cycle 				= 16;
    $cyclemonths 		= array("romulus" 		=> array(31,30,31,30,31,30,30,31,30,30));			
	$cycledepreciation  = array(16 				=> array("romulus"	=>	array('cycle'		=>  6,
																			  'scale' 		=> array('gmt' 	=> 	 0,
																			  						 'UP' 	=>	-9,
																			  				   		 'DN' 	=>	 9)
																		)
													));
													
	$cycledays 			= array(16 				=> 61);
	$cyclenames 		= array(16 				=> "Nivis Off Month");
	$cyclemonthnames	= array("romulus" 		=> array(	"Martius", "Aprilis", "Mius", "Lunius", "Quintilis", "Sexitilis", 
															"Messidor", "Octobribiis", "Novembribiis", "Decembribiis"));
	$monthnames = array();
	
	// Code Segment 3 Calculate Calendar Cycle Depreciation
	if ($cycle > 0) {
		foreach ($cyclemonths as $key => $value) {
			if (isset($cycledepreciation[$cycle][$key])) {
				$month = 0;
				$monthnames[$key] = array();
				$monthnames_tmp = array(); 
				$appretite = array();
				foreach( $value as $index => $monthday ) {
					$month++;
					$appretite = array_merge($appretite, array($monthday));
					if ($month==$cycledepreciation[$cycle][$key]['cycle']) {
						$appretite = array_merge($appretite, array($cycledays[$cycle]));
						foreach($cyclemonthnames[$key] as $mindex => $mname) {
							$uu++;
							$monthnames_tmp = array_merge($monthnames_tmp, array($mname));
							if ($uu==$cycledepreciation[$cycle][$key]['cycle']) 
								$monthnames_tmp = array_merge($monthnames_tmp, array($cyclenames[$cycle]));
						}
					}
				}
				if (isset($monthnames_tmp))
					$monthnames[$key] = $monthnames_tmp;
				else 
					$monthnames[$key] = $cyclemonthnames[$key];
			}
			if (isset($appretite))
				$deficient[$key] = $appretite; 
			else
				$deficient[$key] = $value;
		}	
	} else {
		foreach ($cyclemonths as $key => $value) 
			$deficient[$key] = $value;
		foreach($cyclemonthnames as $key => $value) 
			$monthnames[$key] = $value;
	}	
	
    $monthusage 		= isset($defiency) ? ${$defiency} : $deficient;
	
    // Code Segment 2 â€“ Calculate month number, day number, day count etc
	$i = 0;
	$ii = 0;
	$ttl_num = 0;
	$ttl_num_months = 0;
	$nodaycount = 0;
    foreach($monthusage as $key => $item){
        $i++;
        foreach($item as $numdays){
            $ttl_num=$ttl_num+$numdays;
            $ttl_num_months++;
        }
    	if ($cycle > 0) {
			$ttl_num=$ttl_num+$cycledays[$cycle];
			$ttl_num_months++;
		}
	}
		
	$revolutionsperyear = $ttl_num / $i;
	$numyears = floor((ceil($roun) / $revolutionsperyear));
	$avg_num_month = $ttl_num_months/$i;
	$jtl = abs(abs($roun) - ceil($revolutionsperyear*($numyears+1)));

	$ii=0;
	$tt=0;
	$t=0;
	$month=0;
	while($month==0){
		$day=0;
		$u=0;
		foreach($monthusage as $key => $item){
			$t=0;   
			foreach($item as $numdays){
				$t++;
				$tt=0;
				for($sh=1;$sh<=$numdays;$sh++){
					$ii=$ii+1;
					$tt++;
					if ($ii==floor($jtl)){
						if ($roun<0){
							$daynum = $tt;
							$month = $t;
						} else {
							$daynum = $numdays-($tt-1);
							$month = $avg_num_month-($t-1);
						}
						$sequence = $key;
						$nodaycount=true;
					}
				}
				if ($nodaycount==false)
					$day++;
			}
			$u++;
		}
	}
    
	//$numyears = abs($numyears);
	
    $timer = substr($roun, strpos($roun,'.')+1,strlen($roun)-strpos($roun,'.')-1);
    $roun_out = decrom($numyears) . ' ' . decrom($month) . ' ' . decrom($daynum).' '.$day.'.'. floor(intval(substr($timer,0,2))/100*$timeset['hours']).':'. floor(intval(substr($timer,2,2))/100*$timeset['minutes']).':'. floor(intval(substr($timer,4,2))/100*$timeset['seconds']).'.'.substr($timer,6,strlen($timer)-6);
 
    $roun_obj = array('stardate'=>"$numyears.$day", 'year'=>decrom($numyears),'month'=>decrom($month), 'mname' => $monthnames[$sequence][$month-1],'day'=>decrom($daynum), 'jtl'=>$jtl, 'day_count'=>$day,'hours'=>floor(intval(substr($timer,0,2))/100*$timeset['hours']),'minute'=> floor(intval(substr($timer,2,2))/100*$timeset['minutes']),'seconds'=> floor(intval(substr($timer,4,2))/100*$timeset['seconds']),'microtime'=>substr($timer,6,strlen($timer)-6),'strout'=>$roun_out);

    return $roun_obj;
}

/**
 * LatinCalendar()
 * Latin Calendar using Unix Timestamp with Ounion Movement Sequence
 *
 * @param integer $unix_time			Unix Date/Time Stamp to Convert
 * @param double $gmt					GMT/UTC/DST of $unix_time stamp
 * @param string $poffset				Core Break Point of Calendar Cycle to Unix Timestamp and $pweight
 * @param float $pweight				Weighting Days Balancing Variable
 * @param float $defiency				Defeiency Monthly Cycle to Use
 * @param array $timeset				Maximum Hours, Minutes, Seconds in a Time Cycle
 * @return array
 */
function LatinCalendar($unix_time, $gmt, $poffset = '1970-06-29 12:00 PM', $pweight = '-719542.50000001', $defiency='deficient', $timeset= array("hours" => 24, "minutes" => 60, "seconds" => 60))
    {
    // Code Segment 1 â€“ Calculate Floating Point
    $tme = $unix_time;

    if ($gmt>0){
        $gmt=-$gmt;
    } else {
        $gmt=abs($gmt);
    }
    
    $ptime = strtotime($poffset)+(60*60*$gmt);
    $weight = $pweight+(1*$gmt);

    $roun_xa = ($tme)/(24*60*60);
    $roun_ya = $ptime/(24*60*60);
    $roun = (($roun_xa -$roun_ya) - $weight)+(microtime(true)/999999);
    
    // Code Segment 2 â€“ Set month day arrays
    $nonedeficient = array("dark" => array(31,30,31,30,31,30,30,31,30,30,31,30));
	$deficient =  array("julius" => array(31,30,31,30,31,30,30,31,30,30,31,30));
	$modern =  array("modern" => array(31,30,31,30,31,30,30,31,30,30,31,30));

	$monthnames = array("dark" => array("Ianuariis", "Februariis", "Martiis", "Aprilis", "Miis", "Iuniis",  "Iuliis", "Augustiis",
										"Octobribiis", "Messidor", "Novembribiis", "Decembribiis"),
						"julius" => array("Ianuarias", "Februarias", "Martias", "Aprilibias",  "Mais", "Iunias", "Iulias", "Augustias",
										"Septembribias", "Octobribias", "Novembribias", "Decembribias"),
						"modern" => array("Ianuaries", "Februaries", "Marties", "Apriles", "Maes", "Iunies", "Iulies",
					                     "Augusties", "Septembries","Octobries", "Novembries",  "Decembries")) ;

    $monthusage = isset($defiency) ? ${$defiency} : $deficient;
    
    // Code Segment 3 â€“ Calculate month number, day number, day count etc
	$i = 0;
	$ii = 0;
	$ttl_num = 0;
	$ttl_num_months = 0;
	$nodaycount = 0;
	$month = 0;
    foreach($monthusage as $key => $item){
        $i++;
        foreach($item as $numdays){
            $ttl_num=$ttl_num+$numdays;
            $ttl_num_months++;
        }
    }
    
   // You need to replace this section in Function EgyptianCalendar
	// As well as Function MayanTihkalCalendar
	$revolutionsperyear = $ttl_num / $i;
	$numyears = floor((ceil($roun) / $revolutionsperyear));
	$avg_num_month = $ttl_num_months/$i;
	$jtl = abs(abs($roun) - ceil($revolutionsperyear*($numyears+1)));
	while($month==0){
		$day=0;
		$u=0;
		foreach($monthusage as $key => $item){
			$t=0;   
			foreach($item as $numdays){
				$t++;
				$tt=0;
				for($sh=1;$sh<=$numdays;$sh++){
					$ii=$ii+1;
					$tt++;
					if ($ii==floor($jtl)){
						if ($roun<0){
							$daynum = $tt;
							$month = $t;
						} else {
							$daynum = $numdays-($tt-1);
							$month = $avg_num_month-($t-1);
						}
						$sequence = $key;
						$nodaycount=true;
					}
				}
				if ($nodaycount==false)
					$day++;
			}
			$u++;
		}
	}
    
	//$numyears = abs($numyears);
	
    $timer = substr($roun, strpos($roun,'.')+1,strlen($roun)-strpos($roun,'.')-1);
    $roun_out = decrom($numyears) . ' ' . decrom($month) . ' ' . decrom($daynum).' '.$day.'.'. floor(intval(substr($timer,0,2))/100*$timeset['hours']).':'. floor(intval(substr($timer,2,2))/100*$timeset['minutes']).':'. floor(intval(substr($timer,4,2))/100*$timeset['seconds']).'.'.substr($timer,6,strlen($timer)-6);
 
    $roun_obj = array('stardate'=>"$numyears.$day", 'year'=>decrom($numyears),'month'=>decrom($month), 'mname' => $monthnames[$sequence][$month-1],'day'=>decrom($daynum), 'jtl'=>$jtl, 'day_count'=>$day,'hours'=>floor(intval(substr($timer,0,2))/100*$timeset['hours']),'minute'=> floor(intval(substr($timer,2,2))/100*$timeset['minutes']),'seconds'=> floor(intval(substr($timer,4,2))/100*$timeset['seconds']),'microtime'=>substr($timer,6,strlen($timer)-6),'strout'=>$roun_out);

    return $roun_obj;
}

?>
