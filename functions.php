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
 * @subpackage	    time
 * @description	    Multiple Calendars on RSS Feed for the Here and Now
 */

/**
 * xoStripeKey()
 * Stripes a checksum for guid
 *
 * @param string $time_key			Checksum to be striped
 * @return string
 */
function xoStripeKey($time_key)
{
    $uu = 0;
    $num = 6;
    $length = strlen($time_key);
    $strip = floor(strlen($time_key) / 6);
    for ($i = 0; $i < strlen($time_key); $i++) {
        if ($i < $length) {
            $uu++;
            if ($uu == $strip) {
                $ret .= substr($time_key, $i, 1) . '-';
                $uu = 0;
            } else {
                if (substr($time_key, $i, 1) != '-') {
                    $ret .= substr($time_key, $i, 1);
                } else {
                    $uu--;
                }
            }
        }
    }
    $ret = str_replace('--', '-', $ret);
    if (substr($ret, 0, 1) == '-') {
        $ret = substr($ret, 2, strlen($ret));
    }
    if (substr($ret, strlen($ret), 1) == '-') {
        $ret = substr($ret, 0, strlen($ret) - 1);
    }
    return $ret;
}



/**
 * xoFindDateZone()
 * Finds from Data a PHP Datezone
 *
 * @param array $terms			Terms to base find on
 * @param array $zones			Date Zone Array from xoGetDateZones()
 * @param string $node			Contextual Array Node
 * @param string $basis			Contextual Array Node
 * @return string
 */
function xoFindDateZone($terms = array(), $zones = array(), $node = "", $basis = 'regions')
{
	$result = "";
	foreach($terms as $id => $term)
	{
		if (!empty($term))
		{
			if (in_array(strtolower($term), array_keys($zones['places'])))
			{
				if (isset($zones['zones'][$zones['places'][$term]]['code']) && !empty($zones['zones'][$zones['places'][$term]]['code']))
					return $zones['zones'][$zones['places'][$term]]['code'];
			}
			switch($basis)
			{
				case "regions":
				default:
					if (in_array(strtolower($term), array_keys($zones['regions'])))
					{
						$tterm = $terms;
						unset($tterm[$id]);
						if (strlen($result = xoFindDateZone($tterm, $zones, $term, 'areas'))>0)
							return $result;
					}
					break;
				case "areas":
					if (in_array(strtolower($term), array_keys($zones['areas'][$node])))
					{
						if (isset($zones['zones'][$zones['areas'][$node][$term]]['code']) && !empty($zones['zones'][$zones['areas'][$node][$term]]['code']))
							return $zones['zones'][$zones['areas'][$node][$term]]['code'];
					}
					break;
			}
			foreach($zones['zones'] as $key => $values)
			{
				if (strpos($key, strtolower($term))>0)
				{
					return $values['code'];
				}
			}
		}
	}
	return "";
}


/**
 * xoGetDateZones()
 * Imports Datezones
 *
 * @param string $filename			File to be imported
 * @return array
 */
function xoGetDateZones($filename = '')
{
    if (!file_exists($filename))
	return array();
    static $ret = array();
    if (!isset($ret[md5($filename)]))
    {
        foreach(file($filename) as $line)
        {
             $parts = explode("/", $line = trim($line));
             if (isset($parts[1]) && !empty($parts[1]))
	     {
                 if ($parts[1]!=$parts[0]) {
                     $ret[md5($filename)]['areas'][strtolower($parts[0])][strtolower($parts[1])] = strtolower($line);
		     $ret[md5($filename)]['places'][strtolower($parts[1])] = strtolower($line);
                     $ret[md5($filename)]['zones'][strtolower($line)] = array('region'=>$parts[0], 'area' => $parts[1], 'code' => ucfirst(strtolower($parts[0])) . '/' . ucfirst(strtolower($parts[1])));
                 } else {
                     $ret[md5($filename)]['zones'][strtolower($line)] = array('region'=>$parts[0], 'area' => $parts[0], 'code' => ucfirst(strtolower($parts[0])));
		 }
                 $ret[md5($filename)]['regions'][strtolower($parts[0])] = $parts[0];
             } elseif (isset($parts[0]) && !empty($parts[0])) {
                 $ret[md5($filename)]['zones'][strtolower($line)] = array('region'=>$parts[0], 'area' => $parts[0], 'code' => ucfirst(strtolower($parts[0])));
                 $ret[md5($filename)]['regions'][strtolower($parts[0])] = $parts[0];
             }
        }
    }
    return $ret[md5($filename)];
}

?>
