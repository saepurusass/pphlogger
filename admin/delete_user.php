<?php
// $Id: delete_user.php 1759 2003-08-18 19:15:45Z cvs_iezzi $

if(!defined('NO_SESS')) define('NO_SESS', 1);
define('PPHL_SCRIPT_PATH', '../');
include PPHL_SCRIPT_PATH."main_location.inc";
if (isset($usr)) $id = $usr;
include INC_GETUSERDATA;

if ($pw_check == $admin_pw) {
	$sql = "DELETE FROM ".PPHL_TBL_USERS." WHERE id = ".$id;
	$res = mysqli_query($GLOBALS['mysql_link'], $sql);
	$sql = "DROP TABLE IF EXISTS ".$tbl_logs.", ".$tbl_mpdl;
	$res = mysqli_query($GLOBALS['mysql_link'], $sql);
	$sql = "DELETE FROM ".PPHL_TBL_CACHE." WHERE id = ".$id;
	$res = mysqli_query($GLOBALS['mysql_link'], $sql);
	Header("Location: $adm_view[1]?deleted=1");
	exit;
} else {
	Header("Location: $adm_view[1]?wrongpw=1");
	exit;
}

?>