<?php
	/*
		Plugin Name: WP Center Test
		Plugin URI: http://postpix.org
		Description: This a test plugin for WPCenter that creates a custom endpoint and pulls data from the JSONPlaceholder API.
		Version: 1.0
		Author: Dogu Pekgoz
		Author URI: http://postpix.org
		License: GPL2
	*/
	
	if (!defined('WPINC')) {
		die;
	}
	
	register_activation_hook(__FILE__, 'wpcenter_test_activate');
	register_deactivation_hook(__FILE__, 'wpcenter_test_deactivate');
	
	function wpcenter_test_activate() {
		wpcenter_test_create_endpoint();
		flush_rewrite_rules();
	}
	
	function wpcenter_test_deactivate() {
		flush_rewrite_rules();
	}
	
	add_action('init', 'wpcenter_test_create_endpoint');
	add_action('wp_enqueue_scripts', 'wpcenter_test_enqueue_scripts');
	add_action('template_redirect', 'wpcenter_test_template_redirect');
	add_action('wp_ajax_get_user_details', 'wpcenter_test_ajax_get_user_details');
	add_action('wp_ajax_nopriv_get_user_details', 'wpcenter_test_ajax_get_user_details');
	add_action('wp_ajax_clear_user_cache', 'wpcenter_test_clear_user_cache');
	
	function wpcenter_test_create_endpoint() {
		add_rewrite_rule('^wpcenter-test/?', 'index.php?wpcenter-test=1', 'top');
		add_rewrite_tag('%wpcenter-test%', '([^&]+)');
	}
	
	function wpcenter_test_display_users() {
		// transientte kullanıcı varsa
		$users = get_transient('wpcenter_test_users');
		
		// yoksa apiden çek
		if (false === $users) {
			$response = wp_remote_get('https://jsonplaceholder.typicode.com/users');
			if (is_wp_error($response)) {
				return;
			}
			
			$users = json_decode(wp_remote_retrieve_body($response), true);
			
			// kullanıcıları sakla
			set_transient('wpcenter_test_users', $users, 1 * HOUR_IN_SECONDS);
		}
		
		if (empty($users)) {
			return;
		}
		
		echo '<table>';
		echo '<thead>';
		echo '<tr>';
		echo '<th>ID</th>';
		echo '<th>İsim</th>';
		echo '<th>Kullanıcı Adı</th>';
		echo '</tr>';
		echo '</thead>';
		echo '<tbody>';
		foreach ($users as $user) {
			echo '<tr class="user-row" data-user-id="' . esc_attr($user['id']) . '">';
			echo '<td>' . esc_html($user['id']) . '</td>';
			echo '<td>' . esc_html($user['name']) . '</td>';
			echo '<td>' . esc_html($user['username']) . '</td>';
			echo '</tr>';
		}
		echo '</tbody>';
		echo '</table>';
		
		echo '<div style="text-align: center; margin-top: 20px;">';
		echo '<button id="clearCacheButton">Önbelleği Temizle</button>';
		echo '</div>';
	}
	
	function wpcenter_test_ajax_get_user_details() {
		check_ajax_referer('wpcenter_test_nonce', 'nonce');
		$user_id = isset($_POST['user_id']) ? intval(sanitize_text_field($_POST['user_id'])) : 0;
		
		// apiden ayrıntıları al
		$response = wp_remote_get('https://jsonplaceholder.typicode.com/users/' . $user_id);
		
		if (is_wp_error($response)) {
			wp_send_json_error('Kullanıcı bulunamadı.');
			} else {
			$user_details = json_decode(wp_remote_retrieve_body($response), true);
			wp_send_json_success($user_details);
		}
		
		wp_die();
	}
	
	function wpcenter_test_clear_user_cache() {
		check_ajax_referer('wpcenter_test_nonce', 'nonce');
		delete_transient('wpcenter_test_users');
		wp_send_json_success('Önbellek temizlendi.');
	}
	
	function wpcenter_test_template_redirect() {
		global $wp_query;
		if (isset($wp_query->query_vars['wpcenter-test'])) {
			$custom_title = get_bloginfo('name') . ' - WPCenter Test';  
    		add_filter('pre_get_document_title', function() use ($custom_title) {
      		return $custom_title;
			});
			get_header();
			
			wpcenter_test_display_users();
			
			get_footer();
			
			remove_filter('pre_get_document_title', 'set_custom_title');
  	
			exit;
		}
	}
	
	function wpcenter_test_enqueue_scripts() {
		global $wp_query;
		if (isset($wp_query->query_vars['wpcenter-test'])) {
			wp_enqueue_script('wpcenter-test-js', plugin_dir_url(__FILE__) . 'wpcenter-test.js', array('jquery'), filemtime(plugin_dir_path(__FILE__) . 'wpcenter-test.js'), true);
			
			wp_localize_script('wpcenter-test-js', 'ajax_object', array('ajaxurl' => admin_url('admin-ajax.php'),'nonce' => wp_create_nonce('wpcenter_test_nonce'),'gif_url' => plugin_dir_url(__FILE__) . 'img/loading.gif'));
			
			wp_enqueue_style('wpcenter-test-css', plugin_dir_url(__FILE__) . 'wpcenter-test.css', array(), filemtime(plugin_dir_path(__FILE__) . 'wpcenter-test.css'));
		}
	}	