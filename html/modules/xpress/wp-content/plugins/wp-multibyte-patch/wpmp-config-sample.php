<?php
/* WP Multibyte Patch global config file */

// WordPress Settings
$wpmp_conf['excerpt_length'] = 55;           // Maximum word count for ascii posts.
$wpmp_conf['excerpt_mblength'] = 110;        // Maximum character count for multibyte posts.
$wpmp_conf['excerpt_more'] = ' [...]';       // More string at the end of the excerpt.
$wpmp_conf['comment_excerpt_length'] = 20;   // Maximum word count for ascii comments.
$wpmp_conf['comment_excerpt_mblength'] = 40; // Maximum character count for multibyte comments.

// BuddyPress Settings
$wpmp_conf['bp_excerpt_mblength'] = 110;     // Maximum character count for the multibyte text filtered by bp_create_excerpt hook.
$wpmp_conf['bp_excerpt_more'] = ' [...]';    // More string for the multibyte excerpt filtered by bp_create_excerpt hook.

// Each function can be turned off by using the value false.
$wpmp_conf['patch_wp_mail'] = true;
$wpmp_conf['patch_incoming_trackback'] = true;
$wpmp_conf['patch_incoming_pingback'] = true;
$wpmp_conf['patch_wp_trim_excerpt'] = true;
$wpmp_conf['patch_get_comment_excerpt'] = true;
$wpmp_conf['patch_process_search_terms'] = true;
$wpmp_conf['patch_admin_custom_css'] = true;
$wpmp_conf['patch_wplink_js'] = true;
$wpmp_conf['patch_word_count_js'] = true;
$wpmp_conf['patch_sanitize_file_name'] = true;
$wpmp_conf['patch_bp_create_excerpt'] = false;

/**
 * Set the encoding for wp_mail().
 * Available options are "JIS", "UTF-8" and "auto".
 * "auto" picks  "JIS" or "UTF-8" automatically.
 * This option is specific to Japanese.
 */
$wpmp_conf['mail_mode'] = 'JIS';
