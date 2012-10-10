<?php

/**
 */
require_once( __DIR__ . '/common.inc.php');
require_once( __DIR__ . '/lib/yapafo/lib/OSM/Api.php');

session_start();

global $osmApi, $oauth;

// osm api handler is instantiated if necessary
if (!isset($_SESSION['api']))
{
	$_SESSION['api'] = new OSM_Api(array('appName' => Conf::APP_NAME, 'url' => OSM_Api::URL_PROD_UK));
}
$osmApi = $_SESSION['api'];
$oauth = $osmApi->getCredentials();

if (!$oauth)
{
	$oauth = new OSM_Auth_OAuth(
			Conf::OAUTH_CONSUMER_KEY,
			Conf::OAUTH_CONSUMER_SECRET,
			array('callback_url' => currentPageURL())
	);
	$osmApi->setCredentials($oauth);
}

if (isset($_REQUEST["oauth_token"]))
{
	// Check that the callback is for us.
	$creds = $oauth->getRequestToken();
	if ($creds['token'] == $_REQUEST["oauth_token"])
	{
		$oauth->requestAccessToken(
			isset($_REQUEST['oauth_verifier']) ? $_REQUEST['oauth_verifier'] : null
		);
	}
	else
	{
		echo '<p>ERROR, oauth token does not match !</p>' . "\n";
	}
}
