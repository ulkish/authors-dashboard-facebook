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
// - Format data for storing in postmeta. [DONE]
// - Check rate limit for Facebook data requests.

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
		// Get the GraphNode Object for the specified URL containing its engagement stats.
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

// $data = get_facebook_data( 'https://www.sapiens.org/column/field-trips/neanderthal-sex-lives-bones/', $app_credentials );
// print_r( $data );

/**
 * Performs a search for every post/page and returns an array containing all
 *  engagement reports.
 *
 * @param array $app_credentials Array of necessary credentials.
 * @return array $all_facebook_data All share_counts found.
 */
function get_all_facebook_data( $app_credentials ) {
	$all_facebook_data = array();
	$args              = array(
		'posts_per_page' => -1,
		'post_type'      => 'any',
	);
	$all_posts_query   = new WP_Query( $args );
	// Query all the posts.
	while ( $all_posts_query->have_posts() ) {
		$all_posts_query->the_post();
		$post_id       = $all_posts_query->post->ID;
		$post_url      = get_permalink( $post_id );
		$facebook_data = get_facebook_data( $post_url, $app_credentials );
		array_push(
			$all_facebook_data,
			array(
				'post_id'     => $post_id,
				'share_count' => $facebook_data,
			)
		);
	}

	return $all_facebook_data;
}
// add_action( 'init', 'get_all_facebook_data', 10, 1 );

/**
 * Stores all the Facebook data gathered in every postmeta.
 *
 * @param array $all_facebook_data All share_count data.
 * @return void
 */
function store_facebook_data( $all_facebook_data ) {
	foreach ( $all_facebook_data as $facebook_data ) {
		update_post_meta(
			$facebook_data['post_id'],
			'facebook_data',
			$facebook_data['show_count']
		);
	}
}
