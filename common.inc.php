<?php

/**
 */
function currentPageURL() {

	if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')
	{
		$u = 'https';
	}
	else
	{
		$u = 'http';
	}
	$u .= '://';
	
	if ($_SERVER['SERVER_PORT'] != '80')
	{
		$u .= $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['REQUEST_URI'];
	}
	else
	{
		$u .= $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
	}
	return $u;
}
