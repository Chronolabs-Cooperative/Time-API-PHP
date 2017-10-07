<?php
/**
 * Chronolabs REST Geospatial API Services API
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       Chronolabs Cooperative http://snails.email
 * @license         GNU GPL 3 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 * @package         api
 * @since           2.0.1
 * @author          Simon Roberts <wishcraft@users.sourceforge.net>
 * @subpackage		places
 * @description		Geospatial API Services API
 * @see			    http://internetfounder.wordpress.com
 * @see			    http://sourceoforge.net/projects/chronolabsapis
 * @see			    https://github.com/Chronolabs-Cooperative/API-API-PHP
 */


if (!is_file(__DIR__ . DIRECTORY_SEPARATOR . 'mainfile.php') || !is_file(__DIR__ . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'license.php'))
{
    header('Location: ' . "./install");
    exit(0);
}

require_once __DIR__ . DIRECTORY_SEPARATOR . 'mainfile.php';

/**
 * Include libraries
 */
require_once __DIR__ . DIRECTORY_SEPARATOR . 'functions.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'class' . DIRECTORY_SEPARATOR . 'file' . DIRECTORY_SEPARATOR . 'apifile.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'class' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'apicache.php';

/**
 * Opens Access Origin Via networking Route NPN
 */
header('Access-Control-Allow-Origin: *');
header('Origin: *');

/**
 * Turns of GZ Lib Compression for Document Incompatibility
 */
ini_set("zlib.output_compression", 'Off');
ini_set("zlib.output_compression_level", -1);

