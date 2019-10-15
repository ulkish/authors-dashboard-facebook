<?php
/**
 * Array of credentials needed for creating a Graph API request
 *
 * @package Authors Dashboard Facebook
 */

// Here's where an array containing our app ID and secret lies. This file should always
// be hidden from user view.

$app_id       = '535448793933963';
$app_secret   = '3d1bdfd0e2ea3f58e80662295f6613c7';
$access_token = $app_id . '|' . $app_secret;

$facebook_credentials = array(
	'app_id'       => $app_id,
	'app_secret'   => $app_secret,
	'access_token' => $access_token,
);
