<?php
/*  --------------------------------------------------
    upd_2.1.0c-2.1.1.php
    $Id: upd_2.1.0c-2.1.1.php 1759 2003-08-18 19:15:45Z cvs_iezzi $
    
    Just use this file if you update Power Phlogger
    from version 2.1.0c to 2.1.1
    This is just updating your mySQL table-structure
    prior to run this script, please update all
    files in your /pphlogger directory!!
    --------------------------------------------------
*/


@set_time_limit(0);
define('PPHL_DB_LOCK', FALSE);
define('NO_SESS', 1);
define('PPHL_SCRIPT_PATH', '../');
include PPHL_SCRIPT_PATH."main_location.inc";
include LIB_LOADSQL;

$arr_engines = load_engines();

//Load the updated pphlogger_domains table
$sql = "DROP TABLE IF EXISTS ".PPHL_TBL_DOMAINS;
$res = mysqli_query($GLOBALS['mysql_link'], $sql);
echo mysqli_error()."<br />";
exec_sql_lines(SQL_DOMAINS, 'pph_domains', PPHL_TBL_DOMAINS);

//Load the new pphlogger_cache table
$sql = "SELECT COUNT(*) FROM ".PPHL_TBL_CACHE;
$res = mysqli_query($GLOBALS['mysql_link'], $sql);
if (!$res) exec_sql_lines(SQL_CACHE, 'pphl_cache', PPHL_TBL_CACHE);


$sql = "ALTER TABLE ".PPHL_TBL_USERS." "
     . "ADD conf enum('Y','N') DEFAULT 'N' NOT NULL, "
	 . "CHANGE date_start date_start DATETIME NOT NULL";
mysqli_qry($sql);
$sql = "UPDATE ".PPHL_TBL_USERS." SET conf='Y'";
mysqli_qry($sql);

$sql = "SELECT id FROM ".PPHL_TBL_USERS.";";
$res = mysqli_query($GLOBALS['mysql_link'], $sql);
while ($row = mysqli_fetch_array($res)) {
	$id = $row['id'];
	$sql = "ALTER TABLE ".PPHL_DB_PREFIX.$id.$tbl_mpdl." "
		 . "CHANGE type type enum('mp','dl','kw') DEFAULT 'mp' NOT NULL";
	mysqli_qry($sql);
	
	$sql = "ALTER TABLE ".PPHL_DB_PREFIX.$id.$tbl_ipcheck." "
	     . "ADD hostname varchar(150) NOT NULL AFTER ip;";
	mysqli_qry($sql);
	
	//load keywords
	$sql_del = "DELETE FROM ".PPHL_DB_PREFIX.$id.$tbl_mpdl." WHERE type = 'kw'";
	$res_del = mysqli_query($GLOBALS['mysql_link'], $sql_del);
	$sql_keyw = "SELECT referer FROM ".PPHL_DB_PREFIX.$id.$tbl_logs." WHERE referer LIKE '%?%'";
	$res_keyw = mysqli_query($GLOBALS['mysql_link'], $sql_keyw);
	while ($row_keyw = mysqli_fetch_array($res_keyw)) {
		$keywrd = show_keywords($row_keyw['referer'], $arr_engines);
		if ($keywrd[3]) {
			insert_mpdl($keywrd[3], 'kw', PPHL_DB_PREFIX.$id.$tbl_mpdl)."<br />";
		}
	}
}

echo $br.$br."<b>Your upgrade to v.2.1.1 was successful!</b>";
echo $br."Now, please run the next upgrade script: <a href=\"upd_2.1.1-2.1.1d.".CFG_PHPEXT."\">upd_2.1.1-2.1.1d.".CFG_PHPEXT."</a>";
?>