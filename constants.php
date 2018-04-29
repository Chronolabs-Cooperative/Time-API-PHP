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

set_time_limit(7200);

// URL Association for SSL and Protocol Compatibility
$http = 'http://';
if (!empty($_SERVER['HTTPS'])) {
	$http = ($_SERVER['HTTPS']=='on') ? 'https://' : 'http://';
}


define('FEED_PROT', $http);
define('FEED_ROOT_HOSTNAME', 'xortify.com/time');
define('FEED_DATEZONE_FILE', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'datezones.diz');

?>
