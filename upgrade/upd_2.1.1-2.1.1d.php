<?php
/*  --------------------------------------------------
    upd_2.1.1-2.1.1d.php
    $Id: upd_2.1.1-2.1.1d.php 1759 2003-08-18 19:15:45Z cvs_iezzi $
    
    Just use this file if you update Power Phlogger
    from version 2.1.1 to 2.1.1d
    This is just updating your mySQL table-structure.
    Run this script AFTER having updated all
    files in your /pphlogger directory!!
    --------------------------------------------------
*/


@set_time_limit(0);
define('PPHL_DB_LOCK', FALSE);
define('NO_SESS', 1);
define('PPHL_SCRIPT_PATH', '../');
include PPHL_SCRIPT_PATH."main_location.inc";
include LIB_LOADSQL;

// pageimpressions by hit:
$sql = "SELECT id FROM ".PPHL_TBL_USERS;
$res = mysqli_query($GLOBALS['mysql_link'], $sql);
while ($row = mysqli_fetch_array($res)) {
	$id = $row['id'];
	$sql = "ALTER TABLE ".PPHL_DB_PREFIX.$id.$tbl_logs." "
		 . "ADD mp int(10) unsigned DEFAULT '1' NOT NULL, "
		 . "ADD ok enum('Y','N') DEFAULT 'N' NOT NULL";
	mysqli_qry($sql);
	
	$sql = "UPDATE ".PPHL_DB_PREFIX.$id.$tbl_logs." "
	     . "SET ok = 'Y', time=time WHERE 1=1";
	mysqli_qry($sql);
	
	$sql = "ALTER TABLE ".PPHL_DB_PREFIX.$id.$tbl_ipcheck." "
	     . "ADD mp int(10) unsigned DEFAULT '1' NOT NULL";
	mysqli_qry($sql);
}

// user-defined dellog limit value:
$sql = "ALTER TABLE ".PPHL_TBL_USERS." "
     . "ADD limh int(6) unsigned DEFAULT '0' NOT NULL, "
     . "ADD limd int(3) unsigned DEFAULT '0' NOT NULL";
mysqli_qry($sql);

// load the new pphlogger_cache table
$sql = "DROP TABLE IF EXISTS ".PPHL_TBL_CACHE;
mysqli_qry($sql);
exec_sql_lines(SQL_CACHE, 'pphl_cache', PPHL_TBL_CACHE);

// bugfix for online-times in pphlogger_userlog
$sql = "UPDATE ".PPHL_TBL_USERLOG." SET online = UNIX_TIMESTAMP(t_reload)-UNIX_TIMESTAMP(t_since),t_reload=t_reload";
mysqli_qry($sql);


echo $br.$br."<b>Your upgrade to v.2.1.1d was successful!</b>";
echo $br."Now, please run the next upgrade script: <a href=\"upd_2.1.1d-2.1.2.".CFG_PHPEXT."\">upd_2.1.1d-2.1.2.".CFG_PHPEXT."</a>";
?>