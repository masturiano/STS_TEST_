<?
#Description: Redirection Page
#Author: Jhae Torres
#Date Created: February 02, 2009


require_once "../../includes/config.php";
session_start();
if( !isset($_SESSION['username']) and !isset($_SESSION['password']) ){
	header("Location: http://".$config['sys_host']."/".$config['sys_dir']."/index.php");
	session_destroy();
}
?>