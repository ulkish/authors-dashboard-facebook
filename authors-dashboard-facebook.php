<?php
/**
Plugin Name:       Authors Dashboard - Facebook
Plugin URI:        https://tipit.net/
Description:       Display Twitter data.
Version:           1.0
Requires at least: 5.2
Requires PHP:      7.2
Author:            Hugo Moran
Author URI:        https://tipit.net
License:           GPL v2 or later
License URI:       https://www.gnu.org/licenses/gpl-2.0.html

Authors Dashboard is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

Authors Dashboard is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Authors Dashboard. If not, see https://www.gnu.org/licenses/gpl-2.0.html

@package Authors Dashboard Facebook
 */

// Loading Facebook PHP SDK for access to the Graph API.
require_once __DIR__ . '/vendor/autoload.php';
// Loading app credentials.
require_once 'app-credentials.php';

// TODO LIST:
// - Add the Facebook API. [DONE]
// - Get data. [DONE]
// - Format data for storing in postmeta.

/**
 * Performs a request to the GraphAPI to check a given URL engagement stats.
 *
 * @param string $url URL on which to perform the search with.
 * @param array  $app_credentials Array of necessary.
 * @return mixed $graph_node GraphNode Object.
 */
function get_facebook_data( $url, $app_credentials ) {

	$fb = new \Facebook\Facebook(
		[
			'app_id'                => $app_credentials['app_id'],
			'app_secret'            => $app_credentials['app_secret'],
			'default_graph_version' => 'v2.10',
		]
	);

	try {
		// Get the GraphNode Object for the specified URL along its engagement stats.
		$response = $fb->get(
			'?id=' . $url . '&fields=engagement',
			$app_credentials['access_token']
		);
	} catch ( \Facebook\Exceptions\FacebookResponseException $e ) {
		// When Graph returns an error.
		echo 'Graph returned an error: ' . esc_textarea( $e->getMessage() );
		exit;
	} catch ( \Facebook\Exceptions\FacebookSDKException $e ) {
		// When validation fails or other local issues.
		echo 'Facebook SDK returned an error: ' . esc_textarea( $e->getMessage() );
		exit;
	}
	$graph_node = $response->getGraphNode();
	return $graph_node;
}
