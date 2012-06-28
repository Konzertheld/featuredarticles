<?php
if(is_numeric($_GET['id']))
{
	$id = $_GET['id'];
	mysql_connect("localhost", "web450", "DidsPwfmMSQLDb");
	mysql_select_db("usr_web450_1");
	$rs = mysql_query("SELECT value FROM habari__postinfo WHERE post_id=$id AND name='featured'");
	if(mysql_num_rows($rs) > 0)
	{
		$row = mysql_fetch_array($rs);
		$newvalue = ($row[0] == 1)?0:1;
		if(mysql_query("UPDATE habari__postinfo SET value=$newvalue WHERE post_id=$id AND name='featured'"))
			echo "ok";
	}
	else
	{
		if(mysql_query("INSERT INTO habari__postinfo (post_id, name, value) VALUES ($id, 'featured', true)"))
			echo "ok";
	}
}
?>