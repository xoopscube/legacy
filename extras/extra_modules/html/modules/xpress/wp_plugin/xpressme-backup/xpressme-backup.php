<?php
/*
Plugin Name: XPressME Database Backup
Plugin URI: http://ja.xpressme.info/
Description: On-demand backup of your WordPress database.  Navigate to <a href="edit.php?page=xpressme-backup">Tools &rarr; Backup</a> to get started.
Author: toemon 
Author URI: http://ja.xpressme.info
Version: 1.2

Originally modified from Austin Matzko's WordPress Database Backup(http://www.ilfilosofo.com/blog/wp-db-backup) plugin.

Copyright 2008  toemon 

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110, USA
*/

/**
 * Change WP_BACKUP_DIR if you want to
 * use a different backup location
 */

$rand = substr( md5( md5( DB_PASSWORD ) ), -5 );
global $wpdbb_content_dir, $wpdbb_content_url, $wpdbb_plugin_dir;
$wpdbb_content_dir = ( defined('WP_CONTENT_DIR') ) ? WP_CONTENT_DIR : ABSPATH . 'wp-content';
$wpdbb_content_url = ( defined('WP_CONTENT_URL') ) ? WP_CONTENT_URL : get_option('siteurl') . '/wp-content';
$wpdbb_plugin_dir = ( defined('WP_PLUGIN_DIR') ) ? WP_PLUGIN_DIR : $wpdbb_content_dir . '/plugins';

if ( ! defined('WP_BACKUP_DIR') ) {
	define('WP_BACKUP_DIR', $wpdbb_content_dir . '/backup-' . $rand . '/');
}

if ( ! defined('WP_BACKUP_URL') ) {
	define('WP_BACKUP_URL', $wpdbb_content_url . '/backup-' . $rand . '/');
}

if ( ! defined('ROWS_PER_SEGMENT') ) {
	define('ROWS_PER_SEGMENT', 100);
}

/** 
 * Set MOD_EVASIVE_OVERRIDE to true 
 * and increase MOD_EVASIVE_DELAY 
 * if the backup stops prematurely.
 */
// define('MOD_EVASIVE_OVERRIDE', false);
if ( ! defined('MOD_EVASIVE_DELAY') ) {
	define('MOD_EVASIVE_DELAY', '500');
}
load_plugin_textdomain('xpressme-backup', 'wp-content/plugins/xpressme-backup');

class wpdbBackup {

	var $backup_complete = false;
	var $backup_file = '';
	var $backup_filename;
	var $core_table_names = array();
	var $errors = array();
	var $basename;
	var $page_url;
	var $referer_check_key;
	var $version = '2.1.5-alpha';

	function gzip() {
		return function_exists('gzopen');
	}

	function module_check() {
		$mod_evasive = false;
		if ( true === MOD_EVASIVE_OVERRIDE ) return true;
		if ( false === MOD_EVASIVE_OVERRIDE ) return false;
		if ( function_exists('apache_get_modules') ) 
			foreach( (array) apache_get_modules() as $mod ) 
				if ( false !== strpos($mod,'mod_evasive') || false !== strpos($mod,'mod_dosevasive') )
					return true;
		return false;
	}

	function wpdbBackup() {
		global $table_prefix, $wpdb;
		add_action('wp_ajax_save_backup_time', array(&$this, 'save_backup_time'));
		add_action('init', array(&$this, 'init_textdomain'));
		add_action('load-update-core.php', array(&$this, 'update_notice_action'));
		add_action('wp_db_backup_cron', array(&$this, 'cron_backup'));
		add_action('xpressme_cron_daily', array(&$this, 'xpressme_cron_daily'));
		add_filter('cron_schedules', array(&$this, 'add_sched_options'));
		add_filter('wp_db_b_schedule_choices', array(&$this, 'schedule_choices'));
		
		$table_prefix = ( isset( $table_prefix ) ) ? $table_prefix : $wpdb->prefix;
		$datum = date("Ymd_B");
		$this->backup_filename = DB_NAME . "_$table_prefix$datum.sql";
		if ($this->gzip()) $this->backup_filename .= '.gz';

		$possible_names = array(
			'categories',
			'comments',
			'link2cat',
			'linkcategories',
			'links',
			'options',
			'post2cat',
			'postmeta',
			'posts',
			'terms',
			'term_taxonomy',
			'term_relationships',
			'users',
		);
		$xpress_possible_names = array(
			'usermeta',
			'd3forum_link',
			'group_role',
			'views',
			'notify_reserve',
		);

		foreach( $possible_names as $name ) {
			if ( isset( $wpdb->{$name} ) ) {
				$this->core_table_names[] = $wpdb->{$name};
			}
		}
		foreach( $xpress_possible_names as $name ) {
				$this->core_table_names[] = $table_prefix . $name;
		}
	
		$this->backup_dir = trailingslashit(apply_filters('wp_db_b_backup_dir', WP_BACKUP_DIR));
		$this->basename = 'xpressme-backup';
	
		$this->referer_check_key = $this->basename . '-download_' . DB_NAME;
		$query_args = array( 'page' => $this->basename );
		if ( function_exists('wp_create_nonce') )
			$query_args = array_merge( $query_args, array('_wpnonce' => wp_create_nonce($this->referer_check_key)) );
		$base = ( function_exists('site_url') ) ? site_url('', 'admin') : get_option('siteurl');
		$this->page_url = add_query_arg( $query_args, $base . '/wp-admin/edit.php');
		if (isset($_POST['do_backup'])) {
			$this->wp_secure('fatal');
			check_admin_referer($this->referer_check_key);
			$this->can_user_backup('main');
			// save exclude prefs

			$exc_revisions = (array) $_POST['exclude-revisions'];
			$exc_spam = (array) $_POST['exclude-spam'];
			update_option('xpressme_backup_excs', array('revisions' => $exc_revisions, 'spam' => $exc_spam));
			
			$do_euc_to_utf8 = $_POST['do_euc_to_utf8'];
			$do_rename_prefix = $_POST['do_rename_prefix'];
			$before_prefix = $_POST['before_prefix'];
			$after_prefix = $_POST['after_prefix'];
			$do_change_uri = $_POST['do_change_uri'];
			$before_uri = $_POST['before_uri'];
			$after_uri = $_POST['after_uri'];
			$del_active_plugin = $_POST['del_active_plugin'];
			$set_default_role = $_POST['set_default_role'];
			
			update_option('xpressme_backup_extras_option', 
				array('do_euc_to_utf8' => $do_euc_to_utf8, 
					'do_rename_prefix' => $do_rename_prefix, 
					'before_prefix' => $before_prefix, 
					'after_prefix' => $after_prefix, 
					'do_change_uri' => $do_change_uri, 
					'before_uri' => $before_uri, 
					'after_uri' => $after_uri,
					'del_active_plugin' => $del_active_plugin,
					'set_default_role' => $set_default_role
				)
			);
				
			switch($_POST['do_backup']) {
			case 'backup':
				add_action('init', array(&$this, 'perform_backup'));
				break;
			case 'fragments':
				add_action('admin_menu', array(&$this, 'fragment_menu'));
				break;				
			}
		} elseif (isset($_GET['fragment'] )) {
			$this->can_user_backup('frame');
			add_action('init', array(&$this, 'init'));
		} elseif (isset($_GET['backup'] )) {
			$this->can_user_backup();
			add_action('init', array(&$this, 'init'));
		} else {
			add_action('admin_menu', array(&$this, 'admin_menu'));
		}
	}
	
	function init() {
		$this->can_user_backup();
		if (isset($_GET['backup'])) {
			$via = isset($_GET['via']) ? $_GET['via'] : 'http';
			
			$this->backup_file = $_GET['backup'];
			$this->validate_file($this->backup_file);

			switch($via) {
			case 'smtp':
			case 'email':
				$success = $this->deliver_backup($this->backup_file, 'smtp', $_GET['recipient'], 'frame');
				$this->error_display( 'frame' );
				if ( $success ) {
					echo '
						<!-- ' . $via . ' -->
						<script type="text/javascript"><!--\\
					';
					echo '
						alert("' . __('Backup Complete!','xpressme-backup') . '");
						window.onbeforeunload = null; 
						</script>
					';
				}
				break;
			default:
				$this->deliver_backup($this->backup_file, $via);
				$this->error_display( 'frame' );
			}
			die();
		}
		if (isset($_GET['fragment'] )) {
			list($table, $segment, $filename) = explode(':', $_GET['fragment']);
			$this->validate_file($filename);
			$this->backup_fragment($table, $segment, $filename);
		}

		die();
	}

	function init_textdomain() {
		load_plugin_textdomain('xpressme-backup', str_replace(ABSPATH, '', dirname(__FILE__)), dirname(plugin_basename(__FILE__)));
	}

	/*
	 * Add a link to back up your database when doing a core upgrade 
	 */
	function update_notice_action() {
		if ( 'upgrade-core' == $_REQUEST['action'] ) :
			ob_start(array(&$this, 'update_notice'));
			add_action('admin_footer', create_function('', 'ob_end_flush();'));
		endif;
	}
		function update_notice($text = '') {
			$pattern = '#(<a href\="' . __('http://codex.wordpress.org/WordPress_Backups') . '">.*?</p>)#';
			$replace = '$1' . "\n<p>" . sprintf(__('Click <a href="%s" target="_blank">here</a> to back up your database using the WordPress Database Backup plugin. <strong>Note:</strong> WordPress Database Backup does <em>not</em> back up your files, just your database.', 'xpressme-backup'), 'tools.php?page=xpressme-backup') . "</p>\n"; 
			$text = preg_replace($pattern, $replace, $text);
			return $text;
		}

	function build_backup_script() {
		global $table_prefix, $wpdb;
	
		echo "<div class='wrap'>";
		echo 	'<fieldset class="options"><legend>' . __('Progress','xpressme-backup') . '</legend>
			<p><strong>' .
				__('DO NOT DO THE FOLLOWING AS IT WILL CAUSE YOUR BACKUP TO FAIL:','xpressme-backup').
			'</strong></p>
			<ol>
				<li>'.__('Close this browser','xpressme-backup').'</li>
				<li>'.__('Reload this page','xpressme-backup').'</li>
				<li>'.__('Click the Stop or Back buttons in your browser','xpressme-backup').'</li>
			</ol>
			<p><strong>' . __('Progress:','xpressme-backup') . '</strong></p>
			<div id="meterbox" style="height:11px;width:80%;padding:3px;border:1px solid #659fff;"><div id="meter" style="height:11px;background-color:#659fff;width:0%;text-align:center;font-size:6pt;">&nbsp;</div></div>
			<div id="progress_message"></div>
			<div id="errors"></div>
			</fieldset>
			<iframe id="backuploader" src="about:blank" style="visibility:hidden;border:none;height:1em;width:1px;"></iframe>
			<script type="text/javascript">
			//<![CDATA[
			window.onbeforeunload = function() {
				return "' . __('Navigating away from this page will cause your backup to fail.', 'xpressme-backup') . '";
			}
			function setMeter(pct) {
				var meter = document.getElementById("meter");
				meter.style.width = pct + "%";
				meter.innerHTML = Math.floor(pct) + "%";
			}
			function setProgress(str) {
				var progress = document.getElementById("progress_message");
				progress.innerHTML = str;
			}
			function addError(str) {
				var errors = document.getElementById("errors");
				errors.innerHTML = errors.innerHTML + str + "<br />";
			}

			function backup(table, segment) {
				var fram = document.getElementById("backuploader");
				fram.src = "' . $this->page_url . '&fragment=" + table + ":" + segment + ":' . $this->backup_filename . ':";
			}
			
			var curStep = 0;
			
			function nextStep() {
				backupStep(curStep);
				curStep++;
			}
			
			function finishBackup() {
				var fram = document.getElementById("backuploader");				
				setMeter(100);
		';

		$download_uri = add_query_arg('backup', $this->backup_filename, $this->page_url);
		switch($_POST['deliver']) {
		case 'http':
			echo '
				setProgress("' . sprintf(__("Backup complete, preparing <a href=\\\"%s\\\">backup</a> for download...",'xpressme-backup'), $download_uri) . '");
				window.onbeforeunload = null; 
				fram.src = "' . $download_uri . '";
			';
			break;
		case 'smtp':
			echo '
				setProgress("' . sprintf(__("Backup complete, sending <a href=\\\"%s\\\">backup</a> via email...",'xpressme-backup'), $download_uri) . '");
				window.onbeforeunload = null; 
				fram.src = "' . $download_uri . '&via=email&recipient=' . $_POST['backup_recipient'] . '";
			';
			break;
		default:
			echo '
				setProgress("' . sprintf(__("Backup complete, download <a href=\\\"%s\\\">here</a>.",'xpressme-backup'), $download_uri) . '");
				window.onbeforeunload = null; 
			';
		}
		
		echo '
			}
			
			function backupStep(step) {
				switch(step) {
				case 0: backup("", 0); break;
		';
		
		$also_backup = array();
		if (isset($_POST['other_tables'])) {
			$also_backup = $_POST['other_tables'];
		} else {
			$also_backup = array();
		}
		$core_tables = $_POST['core_tables'];
		$tables = array_merge($core_tables, $also_backup);
		$step_count = 1;
		foreach ($tables as $table) {
			$rec_count = $wpdb->get_var("SELECT count(*) FROM {$table}");
			$rec_segments = ceil($rec_count / ROWS_PER_SEGMENT);
			$table_count = 0;
			if ( $this->module_check() ) {
				$delay = "setTimeout('";
				$delay_time = "', " . (int) MOD_EVASIVE_DELAY . ")";
			}
			else { $delay = $delay_time = ''; }
			do {
				echo "case {$step_count}: {$delay}backup(\"{$table}\", {$table_count}){$delay_time}; break;\n";
				$step_count++;
				$table_count++;
			} while($table_count < $rec_segments);
			echo "case {$step_count}: {$delay}backup(\"{$table}\", -1){$delay_time}; break;\n";
			$step_count++;
		}
		echo "case {$step_count}: finishBackup(); break;";
		
		echo '
				}
				if(step != 0) setMeter(100 * step / ' . $step_count . ');
			}

			nextStep();
			// ]]>
			</script>
	</div>
		';
		$this->backup_menu();
	}

	function backup_fragment($table, $segment, $filename) {
		global $table_prefix, $wpdb;
			
		echo "$table:$segment:$filename";
		
		if($table == '') {
			$msg = __('Creating backup file...','xpressme-backup');
		} else {
			if($segment == -1) {
				$msg = sprintf(__('Finished backing up table \\"%s\\".','xpressme-backup'), $table);
			} else {
				$msg = sprintf(__('Backing up table \\"%s\\"...','xpressme-backup'), $table);
			}
		}
		
		if (is_writable($this->backup_dir)) {
			$this->fp = $this->open($this->backup_dir . $filename, 'a');
			if(!$this->fp) {
				$this->error(__('Could not open the backup file for writing!','xpressme-backup'));
				$this->error(array('loc' => 'frame', 'kind' => 'fatal', 'msg' =>  __('The backup file could not be saved.  Please check the permissions for writing to your backup directory and try again.','xpressme-backup')));
			}
			else {
				if($table == '') {		
					//Begin new backup of MySql
					$this->stow("# " . __('WordPress MySQL database backup','xpressme-backup') . "\n");
					$this->stow("#\n");
					$this->stow("# " . sprintf(__('Generated: %s','xpressme-backup'),date("l j. F Y H:i T")) . "\n");
					$this->stow("# " . sprintf(__('Hostname: %s','xpressme-backup'),DB_HOST) . "\n");
					$this->stow("# " . sprintf(__('Database: %s','xpressme-backup'),$this->backquote(DB_NAME)) . "\n");
					$this->stow("# --------------------------------------------------------\n");
				} else {
					if($segment == 0) {
						// Increase script execution time-limit to 15 min for every table.
						if ( !ini_get('safe_mode')) @set_time_limit(15*60);
						// Create the SQL statements
						$this->stow("# --------------------------------------------------------\n");
						$this->stow("# " . sprintf(__('Table: %s','xpressme-backup'),$this->backquote($table)) . "\n");
						$this->stow("# --------------------------------------------------------\n");
					}			
					$this->backup_table($table, $segment);
				}
			}
		} else {
			$this->error(array('kind' => 'fatal', 'loc' => 'frame', 'msg' => __('The backup directory is not writeable!  Please check the permissions for writing to your backup directory and try again.','xpressme-backup')));
		}

		if($this->fp) $this->close($this->fp);
		
		$this->error_display('frame');

		echo '<script type="text/javascript"><!--//
		var msg = "' . $msg . '";
		window.parent.setProgress(msg);
		window.parent.nextStep();
		//--></script>
		';
		die();
	}

	function perform_backup() {
		// are we backing up any other tables?
		$also_backup = array();
		if (isset($_POST['other_tables']))
			$also_backup = $_POST['other_tables'];
		$core_tables = $_POST['core_tables'];
		$this->backup_file = $this->db_backup($core_tables, $also_backup);
		if (false !== $this->backup_file) {
			if ('smtp' == $_POST['deliver']) {
				$this->deliver_backup($this->backup_file, $_POST['deliver'], $_POST['backup_recipient'], 'main');
				wp_redirect($this->page_url);
			} elseif ('http' == $_POST['deliver']) {
				$download_uri = add_query_arg('backup',$this->backup_file,$this->page_url);
				wp_redirect($download_uri); 
				exit;
			}
			// we do this to say we're done.
			$this->backup_complete = true;
		}
	}

	function admin_header() {
		?>
		<script type="text/javascript">
		//<![CDATA[
		if ( 'undefined' != typeof addLoadEvent ) {
			addLoadEvent(function() {
				var t = {'extra-tables-list':{name: 'other_tables[]'}, 'include-tables-list':{name: 'xpressme_cron_backup_tables[]'}};

				for ( var k in t ) {
					t[k].s = null;
					var d = document.getElementById(k);
					if ( ! d )
						continue;
					var ul = d.getElementsByTagName('ul').item(0);
					if ( ul ) {
						var lis = ul.getElementsByTagName('li');
						if ( 3 > lis.length )
							return;
						var text = document.createElement('p');
						text.className = 'instructions';
						text.innerHTML = '<?php _e('Click and hold down <code>[SHIFT]</code> to toggle multiple checkboxes', 'xpressme-backup'); ?>';
						ul.parentNode.insertBefore(text, ul);
					}
					t[k].p = d.getElementsByTagName("input");
					for(var i=0; i < t[k].p.length; i++)
						if(t[k].name == t[k].p[i].getAttribute('name')) {
							t[k].p[i].id = k + '-table-' + i;
							t[k].p[i].onkeyup = t[k].p[i].onclick = function(e) {
								e = e ? e : event;
								if ( 16  == e.keyCode ) 
									return;
								var match = /([\w-]*)-table-(\d*)/.exec(this.id);
								var listname = match[1];
								var that = match[2];
								if ( null === t[listname].s )
									t[listname].s = that;
								else if ( e.shiftKey ) {
									var start = Math.min(that, t[listname].s) + 1;
									var end = Math.max(that, t[listname].s);
									for( var j=start; j < end; j++)
										t[listname].p[j].checked = t[listname].p[j].checked ? false : true;
									t[listname].s = null;
								}
							}
						}
				}

				<?php if ( function_exists('wp_schedule_event') ) : // needs to be at least WP 2.1 for ajax ?>
				if ( 'undefined' == typeof XMLHttpRequest ) 
					var xml = new ActiveXObject( navigator.userAgent.indexOf('MSIE 5') >= 0 ? 'Microsoft.XMLHTTP' : 'Msxml2.XMLHTTP' );
				else
					var xml = new XMLHttpRequest();

				var initTimeChange = function() {
					var timeWrap = document.getElementById('backup-time-wrap');
					var backupTime = document.getElementById('next-backup-time');
					if ( !! timeWrap && !! backupTime ) {
						var span = document.createElement('span');
						span.className = 'submit';
						span.id = 'change-wrap';
						span.innerHTML = '<input type="submit" id="change-backup-time" name="change-backup-time" value="<?php _e('Change','xpressme-backup'); ?>" />';
						timeWrap.appendChild(span);
						backupTime.ondblclick = function(e) { span.parentNode.removeChild(span); clickTime(e, backupTime); };
						span.onclick = function(e) { span.parentNode.removeChild(span); clickTime(e, backupTime); };
					}
				}

				var clickTime = function(e, backupTime) {
					var tText = backupTime.innerHTML;
					backupTime.innerHTML = '<input type="text" value="' + tText + '" name="backup-time-text" id="backup-time-text" /> <span class="submit"><input type="submit" name="save-backup-time" id="save-backup-time" value="<?php _e('Save', 'xpressme-backup'); ?>" /></span>';
					backupTime.ondblclick = null;
					var mainText = document.getElementById('backup-time-text');
					mainText.focus();
					var saveTButton = document.getElementById('save-backup-time');
					if ( !! saveTButton )
						saveTButton.onclick = function(e) { saveTime(backupTime, mainText); return false; };
					if ( !! mainText )
						mainText.onkeydown = function(e) { 
							e = e || window.event;
							if ( 13 == e.keyCode ) {
								saveTime(backupTime, mainText);
								return false;
							}
						}
				}

				var saveTime = function(backupTime, mainText) {
					var tVal = mainText.value;

					xml.open('POST', 'admin-ajax.php', true);
					xml.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
					if ( xml.overrideMimeType )
						xml.setRequestHeader('Connection', 'close');
					xml.send('action=save_backup_time&_wpnonce=<?php echo wp_create_nonce($this->referer_check_key); ?>&backup-time='+tVal);
					xml.onreadystatechange = function() {
						if ( 4 == xml.readyState && '0' != xml.responseText ) {
							backupTime.innerHTML = xml.responseText;
							initTimeChange();
						}
					}
				}

				initTimeChange();
				<?php endif; // wp_schedule_event exists ?>
			});
		}
		//]]>
		</script>
		<style type="text/css">
			.xpressme-backup-updated {
				margin-top: 1em;
			}

			fieldset.options {
				border: 1px solid;
				margin-top: 1em;
				padding: 1em;
			}
				fieldset.options div.tables-list {
					float: left;
					padding: 1em;
				}

				fieldset.options input {
				}

				fieldset.options legend {
					font-size: larger;
					font-weight: bold;
					margin-bottom: .5em;
					padding: 1em;
				}
		
				fieldset.options .instructions {
					font-size: smaller;
				}

				fieldset.options ul {
					list-style-type: none;
				}
					fieldset.options li {
						text-align: left;
					}

				fieldset.options .submit {
					border-top: none;
				}
		</style>
		<?php 
	}

	function admin_load() {
		add_action('admin_head', array(&$this, 'admin_header'));
	}

	function admin_menu() {
		$_page_hook = add_management_page(__('XPressME Backup','xpressme-backup'), __('XPressME Backup','xpressme-backup'), 'import', $this->basename, array(&$this, 'backup_menu'));
		add_action('load-' . $_page_hook, array(&$this, 'admin_load'));
		if ( function_exists('add_contextual_help') ) {
			$text = $this->help_menu();
			add_contextual_help($_page_hook, $text);
		}
	}

	function fragment_menu() {
		$page_hook = add_management_page(__('XPressME Backup','xpressme-backup'), __('XPressME Backup','xpressme-backup'), 'import', $this->basename, array(&$this, 'build_backup_script'));
		add_action('load-' . $page_hook, array(&$this, 'admin_load'));
	}

	/** 
	 * Add WP-DB-Backup-specific help options to the 2.7 =< WP contextual help menu
	 * return string The text of the help menu.
	 */
	function help_menu() {
		$text = '';
//		$text = "\n<a href=\"http://wordpress.org/extend/plugins/xpressme-backup/faq/\" target=\"_blank\">" . __('FAQ', 'xpressme-backup') . '</a>';
//		$text .= "\n<br />\n<a href=\"http://www.ilfilosofo.com/forum/forum/2\" target=\"_blank\">" . __('WP-DB-Backup Support Forum', 'xpressme-backup') . '</a>';
		return $text;
	}

	function save_backup_time() {
		if ( $this->can_user_backup() ) {
			// try to get a time from the input string
			$time = strtotime(strval($_POST['backup-time']));
			if ( ! empty( $time ) && time() < $time ) {
				wp_clear_scheduled_hook( 'wp_db_backup_cron' ); // unschedule previous
				$scheds = (array) wp_get_schedules();
				$name = get_option('xpressme_cron_backup_schedule');
				if ( 0 != $time ) {
					wp_schedule_event($time, $name, 'wp_db_backup_cron');
					echo gmdate(get_option('date_format') . ' ' . get_option('time_format'), $time + (get_option('gmt_offset') * 3600));
					exit;
				}
			}
		} else {
			die(0);
		}
	}

	/**
	 * Better addslashes for SQL queries.
	 * Taken from phpMyAdmin.
	 */
	function sql_addslashes($a_string = '', $is_like = false) {
		if ($is_like) $a_string = str_replace('\\', '\\\\\\\\', $a_string);
		else $a_string = str_replace('\\', '\\\\', $a_string);
		return str_replace('\'', '\\\'', $a_string);
	} 

	/**
	 * Add backquotes to tables and db-names in
	 * SQL queries. Taken from phpMyAdmin.
	 */
	function backquote($a_name) {
		if (!empty($a_name) && $a_name != '*') {
			if (is_array($a_name)) {
				$result = array();
				reset($a_name);
				while(list($key, $val) = each($a_name)) 
					$result[$key] = '`' . $val . '`';
				return $result;
			} else {
				return '`' . $a_name . '`';
			}
		} else {
			return $a_name;
		}
	} 

	function open($filename = '', $mode = 'w') {
		if ('' == $filename) return false;
		if ($this->gzip()) 
			$fp = @gzopen($filename, $mode);
		else
			$fp = @fopen($filename, $mode);
		return $fp;
	}

	function close($fp) {
		if ($this->gzip()) gzclose($fp);
		else fclose($fp);
	}

	/**
	 * Write to the backup file
	 * @param string $query_line the line to write
	 * @return null
	 */
	function stow($query_line) {
		$query_line = $this->extras_filter($query_line);
		if ($this->gzip()) {
			if(! @gzwrite($this->fp, $query_line))
				$this->error(__('There was an error writing a line to the backup script:','xpressme-backup') . '  ' . $query_line . '  ' . $php_errormsg);
		} else {
			if(false === @fwrite($this->fp, $query_line))
				$this->error(__('There was an error writing a line to the backup script:','xpressme-backup') . '  ' . $query_line . '  ' . $php_errormsg);
		}
	}
	
	/**
	 * Logs any error messages
	 * @param array $args
	 * @return bool
	 */
	function error($args = array()) {
		if ( is_string( $args ) ) 
			$args = array('msg' => $args);
		$args = array_merge( array('loc' => 'main', 'kind' => 'warn', 'msg' => ''), $args);
		$this->errors[$args['kind']][] = $args['msg'];
		if ( 'fatal' == $args['kind'] || 'frame' == $args['loc'])
			$this->error_display($args['loc']);
		return true;
	}

	/**
	 * Displays error messages 
	 * @param array $errs
	 * @param string $loc
	 * @return string
	 */
	function error_display($loc = 'main', $echo = true) {
		$errs = $this->errors;
		unset( $this->errors );
		if ( ! count($errs) ) return;
		$msg = '';
		$err_list = array_slice(array_merge( (array) $errs['fatal'], (array) $errs['warn']), 0, 10);
		if ( 10 == count( $err_list ) )
			$err_list[9] = __('Subsequent errors have been omitted from this log.','xpressme-backup');
		$wrap = ( 'frame' == $loc ) ? "<script type=\"text/javascript\">\n var msgList = ''; \n %1\$s \n if ( msgList ) alert(msgList); \n </script>" : '%1$s';
		$line = ( 'frame' == $loc ) ? 
			"try{ window.parent.addError('%1\$s'); } catch(e) { msgList += ' %1\$s';}\n" :
			"%1\$s<br />\n";
		foreach( (array) $err_list as $err )
			$msg .= sprintf($line,str_replace(array("\n","\r"), '', addslashes($err)));
		$msg = sprintf($wrap,$msg);
		if ( count($errs['fatal'] ) ) {
			if ( function_exists('wp_die') && 'frame' != $loc ) wp_die(stripslashes($msg));
			else die($msg);
		}
		else {
			if ( $echo ) echo $msg;
			else return $msg;
		}
	}

	/**
	 * Taken partially from phpMyAdmin and partially from
	 * Alain Wolf, Zurich - Switzerland
	 * Website: http://restkultur.ch/personal/wolf/scripts/db_backup/
	
	 * Modified by Scott Merrill (http://www.skippy.net/) 
	 * to use the WordPress $wpdb object
	 * @param string $table
	 * @param string $segment
	 * @return void
	 */
	function backup_table($table, $segment = 'none') {
		global $wpdb;

		$table_structure = $wpdb->get_results("DESCRIBE $table");
		if (! $table_structure) {
			$this->error(__('Error getting table details','xpressme-backup') . ": $table");
			return false;
		}
	
		if(($segment == 'none') || ($segment == 0)) {
			// Add SQL statement to drop existing table
			$this->stow("\n\n");
			$this->stow("#\n");
			$this->stow("# " . sprintf(__('Delete any existing table %s','xpressme-backup'),$this->backquote($table)) . "\n");
			$this->stow("#\n");
			$this->stow("\n");
			$this->stow("DROP TABLE IF EXISTS " . $this->backquote($table) . ";\n");
			
			// Table structure
			// Comment in SQL-file
			$this->stow("\n\n");
			$this->stow("#\n");
			$this->stow("# " . sprintf(__('Table structure of table %s','xpressme-backup'),$this->backquote($table)) . "\n");
			$this->stow("#\n");
			$this->stow("\n");
			
			$create_table = $wpdb->get_results("SHOW CREATE TABLE $table", ARRAY_N);
			if (false === $create_table) {
				$err_msg = sprintf(__('Error with SHOW CREATE TABLE for %s.','xpressme-backup'), $table);
				$this->error($err_msg);
				$this->stow("#\n# $err_msg\n#\n");
			}
			$this->stow($create_table[0][1] . ' ;');
			
			if (false === $table_structure) {
				$err_msg = sprintf(__('Error getting table structure of %s','xpressme-backup'), $table);
				$this->error($err_msg);
				$this->stow("#\n# $err_msg\n#\n");
			}
		
			// Comment in SQL-file
			$this->stow("\n\n");
			$this->stow("#\n");
			$this->stow('# ' . sprintf(__('Data contents of table %s','xpressme-backup'),$this->backquote($table)) . "\n");
			$this->stow("#\n");
		}
		
		if(($segment == 'none') || ($segment >= 0)) {
			$defs = array();
			$ints = array();
			foreach ($table_structure as $struct) {
				if ( (0 === strpos($struct->Type, 'tinyint')) ||
					(0 === strpos(strtolower($struct->Type), 'smallint')) ||
					(0 === strpos(strtolower($struct->Type), 'mediumint')) ||
					(0 === strpos(strtolower($struct->Type), 'int')) ||
					(0 === strpos(strtolower($struct->Type), 'bigint')) ) {
						$defs[strtolower($struct->Field)] = ( null === $struct->Default ) ? 'NULL' : $struct->Default;
						$ints[strtolower($struct->Field)] = "1";
				}
			}
			
			
			// Batch by $row_inc
			
			if($segment == 'none') {
				$row_start = 0;
				$row_inc = ROWS_PER_SEGMENT;
			} else {
				$row_start = $segment * ROWS_PER_SEGMENT;
				$row_inc = ROWS_PER_SEGMENT;
			}
			
			do {	
				// don't include extra stuff, if so requested
				$excs = (array) get_option('xpressme_backup_excs');

				$where = '';
				if ( is_array($excs['spam'] ) && in_array($table, $excs['spam']) ) {
					$where = ' WHERE comment_approved != "spam"';
				} elseif ( is_array($excs['revisions'] ) && in_array($table, $excs['revisions']) ) {
					$where = ' WHERE post_type != "revision"';
				}
				
				if ( !ini_get('safe_mode')) @set_time_limit(15*60);
				$table_data = $wpdb->get_results("SELECT * FROM $table $where LIMIT {$row_start}, {$row_inc}", ARRAY_A);

				$entries = 'INSERT INTO ' . $this->backquote($table) . ' VALUES (';	
				//    \x08\\x09, not required
				$search = array("\x00", "\x0a", "\x0d", "\x1a");
				$replace = array('\0', '\n', '\r', '\Z');
				if($table_data) {
					foreach ($table_data as $row) {
						$values = array();
						foreach ($row as $key => $value) {
							if ($ints[strtolower($key)]) {
								// make sure there are no blank spots in the insert syntax,
								// yet try to avoid quotation marks around integers
								$value = ( null === $value || '' === $value) ? $defs[strtolower($key)] : $value;
								$values[] = ( '' === $value ) ? "''" : $value;
							} else {
								$values[] = "'" . str_replace($search, $replace, $this->sql_addslashes($value)) . "'";
							}
						}
						$this->stow(" \n" . $entries . implode(', ', $values) . ');');
					}
					$row_start += $row_inc;
				}
			} while((count($table_data) > 0) and ($segment=='none'));
		}
		
		if(($segment == 'none') || ($segment < 0)) {
			// Create footer/closing comment in SQL-file
			$this->stow("\n");
			$this->stow("#\n");
			$this->stow("# " . sprintf(__('End of data contents of table %s','xpressme-backup'),$this->backquote($table)) . "\n");
			$this->stow("# --------------------------------------------------------\n");
			$this->stow("\n");
		}
	} // end backup_table()
	
	function db_backup($core_tables, $other_tables) {
		global $table_prefix, $wpdb;
		
		if (is_writable($this->backup_dir)) {
			$this->fp = $this->open($this->backup_dir . $this->backup_filename);
			if(!$this->fp) {
				$this->error(__('Could not open the backup file for writing!','xpressme-backup'));
				return false;
			}
		} else {
			$this->error(__('The backup directory is not writeable!','xpressme-backup'));
			return false;
		}
		
		//Begin new backup of MySql
		$this->stow("# " . __('WordPress MySQL database backup','xpressme-backup') . "\n");
		$this->stow("#\n");
		$this->stow("# " . sprintf(__('Generated: %s','xpressme-backup'),date("l j. F Y H:i T")) . "\n");
		$this->stow("# " . sprintf(__('Hostname: %s','xpressme-backup'),DB_HOST) . "\n");
		$this->stow("# " . sprintf(__('Database: %s','xpressme-backup'),$this->backquote(DB_NAME)) . "\n");
		$this->stow("# --------------------------------------------------------\n");
		
			if ( (is_array($other_tables)) && (count($other_tables) > 0) )
			$tables = array_merge($core_tables, $other_tables);
		else
			$tables = $core_tables;
		
		foreach ($tables as $table) {
			// Increase script execution time-limit to 15 min for every table.
			if ( !ini_get('safe_mode')) @set_time_limit(15*60);
			// Create the SQL statements
			$this->stow("# --------------------------------------------------------\n");
			$this->stow("# " . sprintf(__('Table: %s','xpressme-backup'),$this->backquote($table)) . "\n");
			$this->stow("# --------------------------------------------------------\n");
			$this->backup_table($table);
		}
				
		$this->close($this->fp);
		
		if (count($this->errors)) {
			return false;
		} else {
			return $this->backup_filename;
		}
		
	} //wp_db_backup

	/**
	 * Sends the backed-up file via email
	 * @param string $to
	 * @param string $subject
	 * @param string $message
	 * @return bool
	 */
	function send_mail( $to, $subject, $message, $diskfile) {
		global $phpmailer;

		$filename = basename($diskfile);

		extract( apply_filters( 'wp_mail', compact( 'to', 'subject', 'message' ) ) );

		if ( !is_object( $phpmailer ) || ( strtolower(get_class( $phpmailer )) != 'phpmailer' ) ) {
			if ( file_exists( ABSPATH . WPINC . '/class-phpmailer.php' ) )
				require_once ABSPATH . WPINC . '/class-phpmailer.php';
			if ( file_exists( ABSPATH . WPINC . '/class-smtp.php' ) )
				require_once ABSPATH . WPINC . '/class-smtp.php';
			if ( class_exists( 'PHPMailer') )
				$phpmailer = new PHPMailer();
		}

		// try to use phpmailer directly (WP 2.2+)
		if ( is_object( $phpmailer ) && ( strtolower(get_class( $phpmailer )) == 'phpmailer' ) ) {
			
			// Get the site domain and get rid of www.
			$sitename = strtolower( $_SERVER['SERVER_NAME'] );
			if ( substr( $sitename, 0, 4 ) == 'www.' ) {
				$sitename = substr( $sitename, 4 );
			}
			$from_email = 'wordpress@' . $sitename;
			$from_name = 'WordPress';

			// Empty out the values that may be set
			$phpmailer->ClearAddresses();
			$phpmailer->ClearAllRecipients();
			$phpmailer->ClearAttachments();
			$phpmailer->ClearBCCs();
			$phpmailer->ClearCCs();
			$phpmailer->ClearCustomHeaders();
			$phpmailer->ClearReplyTos();

			$phpmailer->AddAddress( $to );
			$phpmailer->AddAttachment($diskfile, $filename);
			$phpmailer->Body = $message;
			$phpmailer->CharSet = apply_filters( 'wp_mail_charset', get_bloginfo('charset') );
			$phpmailer->From = apply_filters( 'wp_mail_from', $from_email );
			$phpmailer->FromName = apply_filters( 'wp_mail_from_name', $from_name );
			$phpmailer->IsMail();
			$phpmailer->Subject = $subject;

			do_action_ref_array( 'phpmailer_init', array( &$phpmailer ) );
			
			$result = @$phpmailer->Send();

		// old-style: build the headers directly
		} else {
			$randomish = md5(time());
			$boundary = "==WPBACKUP-$randomish";
			$fp = fopen($diskfile,"rb");
			$file = fread($fp,filesize($diskfile)); 
			$this->close($fp);
			
			$data = chunk_split(base64_encode($file));
			
			$headers .= "MIME-Version: 1.0\n";
			$headers = 'From: wordpress@' . preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME'])) . "\n";
			$headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\n";
		
			// Add a multipart boundary above the plain message
			$message = "This is a multi-part message in MIME format.\n\n" .
		        	"--{$boundary}\n" .
				"Content-Type: text/plain; charset=\"" . get_bloginfo('charset') . "\"\n" .
				"Content-Transfer-Encoding: 7bit\n\n" .
				$message . "\n\n";

			// Add file attachment to the message
			$message .= "--{$boundary}\n" .
				"Content-Type: application/octet-stream;\n" .
				" name=\"{$filename}\"\n" .
				"Content-Disposition: attachment;\n" .
				" filename=\"{$filename}\"\n" .
				"Content-Transfer-Encoding: base64\n\n" .
				$data . "\n\n" .
				"--{$boundary}--\n";
			
			$result = @wp_mail($to, $subject, $message, $headers);
		}
		return $result;

	}

	function deliver_backup($filename = '', $delivery = 'http', $recipient = '', $location = 'main') {
		if ('' == $filename) { return false; }
		
		$diskfile = $this->backup_dir . $filename;
		if ('http' == $delivery) {
			if (! file_exists($diskfile)) 
				$this->error(array('kind' => 'fatal', 'msg' => sprintf(__('File not found:%s','xpressme-backup'), "&nbsp;<strong>$filename</strong><br />") . '<br /><a href="' . $this->page_url . '">' . __('Return to Backup','xpressme-backup') . '</a>'));
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Length: ' . filesize($diskfile));
			header("Content-Disposition: attachment; filename=$filename");
			$success = readfile($diskfile);
			unlink($diskfile);
		} elseif ('smtp' == $delivery) {
			if (! file_exists($diskfile)) {
				$msg = sprintf(__('File %s does not exist!','xpressme-backup'), $diskfile);
				$this->error($msg);
				return false;
			}
			if (! is_email($recipient)) {
				$recipient = get_option('admin_email');
			}
			$message = sprintf(__("Attached to this email is\n   %1s\n   Size:%2s kilobytes\n",'xpressme-backup'), $filename, round(filesize($diskfile)/1024));
			$success = $this->send_mail($recipient, get_bloginfo('name') . ' ' . __('Database Backup','xpressme-backup'), $message, $diskfile);

			if ( false === $success ) {
				$msg = __('The following errors were reported:','xpressme-backup') . "\n ";
				if ( function_exists('error_get_last') ) {
					$err = error_get_last();
					$msg .= $err['message'];
				} else {
					$msg .= __('ERROR: The mail application has failed to deliver the backup.','xpressme-backup'); 
				}
				$this->error(array('kind' => 'fatal', 'loc' => $location, 'msg' => $msg));
			} else {
				unlink($diskfile);
			}
		}
		return $success;
	}
	
	function backup_menu() {
		global $table_prefix, $wpdb,$wp_version;
		$feedback = '';
		$whoops = false;
		
		// did we just do a backup?  If so, let's report the status
		if ( $this->backup_complete ) {
			$feedback = '<div class="updated xpressme-backup-updated"><p>' . __('Backup Successful','xpressme-backup') . '!';
			$file = $this->backup_file;
			switch($_POST['deliver']) {
			case 'http':
				$feedback .= '<br />' . sprintf(__('Your backup file: <a href="%1s">%2s</a> should begin downloading shortly.','xpressme-backup'), WP_BACKUP_URL . "{$this->backup_file}", $this->backup_file);
				break;
			case 'smtp':
				if (! is_email($_POST['backup_recipient'])) {
					$feedback .= get_option('admin_email');
				} else {
					$feedback .= $_POST['backup_recipient'];
				}
				$feedback = '<br />' . sprintf(__('Your backup has been emailed to %s','xpressme-backup'), $feedback);
				break;
			case 'none':
				$feedback .= '<br />' . __('Your backup file has been saved on the server. If you would like to download it now, right click and select "Save As"','xpressme-backup');
				$feedback .= ':<br /> <a href="' . WP_BACKUP_URL . "$file\">$file</a> : " . sprintf(__('%s bytes','xpressme-backup'), filesize($this->backup_dir . $file));
			}
			$feedback .= '</p></div>';
		}
	
		// security check
		$this->wp_secure();  

		if (count($this->errors)) {
			$feedback .= '<div class="updated xpressme-backup-updated error"><p><strong>' . __('The following errors were reported:','xpressme-backup') . '</strong></p>';
			$feedback .= '<p>' . $this->error_display( 'main', false ) . '</p>';
			$feedback .= "</p></div>";
		}

		// did we just save options for wp-cron?
		if ( (function_exists('wp_schedule_event') || function_exists('xpressme_cron_init')) 
			&& isset($_POST['xpressme_cron_backup_options']) ) :
			do_action('wp_db_b_update_cron_options');
			if ( function_exists('wp_schedule_event') ) {
				wp_clear_scheduled_hook( 'wp_db_backup_cron' ); // unschedule previous
				$scheds = (array) wp_get_schedules();
				$name = strval($_POST['xpressme_cron_schedule']);
				$interval = ( isset($scheds[$name]['interval']) ) ? 
					(int) $scheds[$name]['interval'] : 0;
				update_option('xpressme_cron_backup_schedule', $name, false);
				if ( 0 !== $interval ) {
					wp_schedule_event(time() + $interval, $name, 'wp_db_backup_cron');
				}
			}
			else {
				update_option('xpressme_cron_backup_schedule', intval($_POST['cron_schedule']), false);
			}
			update_option('xpressme_cron_backup_tables', $_POST['xpressme_cron_backup_tables']);
			if (is_email($_POST['cron_backup_recipient'])) {
				update_option('xpressme_cron_backup_recipient', $_POST['cron_backup_recipient'], false);
			}
			$feedback .= '<div class="updated xpressme-backup-updated"><p>' . __('Scheduled Backup Options Saved!','xpressme-backup') . '</p></div>';
		endif;
		
		$other_tables = array();
		$also_backup = array();
	
		// Get complete db table list	
		$all_tables = $wpdb->get_results("SHOW TABLES", ARRAY_N);
		$all_tables = array_map(create_function('$a', 'return $a[0];'), $all_tables);
		// Get list of WP tables that actually exist in this DB (for 1.6 compat!)
		$wp_backup_default_tables = array_intersect($all_tables, $this->core_table_names);
		// Get list of non-WP tables
		$other_tables = array_diff($all_tables, $wp_backup_default_tables);
		
		if ('' != $feedback)
			echo $feedback;

		if ( ! $this->wp_secure() ) 	
			return;

		// Give the new dirs the same perms as wp-content.
//		$stat = stat( ABSPATH . 'wp-content' );
//		$dir_perms = $stat['mode'] & 0000777; // Get the permission bits.
		$dir_perms = '0777';

		// the file doesn't exist and can't create it
		if ( ! file_exists($this->backup_dir) && ! @mkdir($this->backup_dir) ) {
			?><div class="updated xpressme-backup-updated error"><p><?php _e('WARNING: Your backup directory does <strong>NOT</strong> exist, and we cannot create it.','xpressme-backup'); ?></p>
			<p><?php printf(__('Using your FTP client, try to create the backup directory yourself: %s', 'xpressme-backup'), '<code>' . $this->backup_dir . '</code>'); ?></p></div><?php
			$whoops = true;
		// not writable due to write permissions
		} elseif ( !is_writable($this->backup_dir) && ! @chmod($this->backup_dir, $dir_perms) ) {
			?><div class="updated xpressme-backup-updated error"><p><?php _e('WARNING: Your backup directory is <strong>NOT</strong> writable! We cannot create the backup files.','xpressme-backup'); ?></p>
			<p><?php printf(__('Using your FTP client, try to set the backup directory&rsquo;s write permission to %1$s or %2$s: %3$s', 'xpressme-backup'), '<code>777</code>', '<code>a+w</code>', '<code>' . $this->backup_dir . '</code>'); ?>
			</p></div><?php 
			$whoops = true;
		} else {
			$this->fp = $this->open($this->backup_dir . 'test' );
			if( $this->fp ) { 
				$this->close($this->fp);
				@unlink($this->backup_dir . 'test' );
			// the directory is not writable probably due to safe mode
			} else {
				?><div class="updated xpressme-backup-updated error"><p><?php _e('WARNING: Your backup directory is <strong>NOT</strong> writable! We cannot create the backup files.','xpressme-backup'); ?></p><?php 
				if( ini_get('safe_mode') ){
					?><p><?php _e('This problem seems to be caused by your server&rsquo;s <code>safe_mode</code> file ownership restrictions, which limit what files web applications like WordPress can create.', 'xpressme-backup'); ?></p><?php 
				}
				?><?php printf(__('You can try to correct this problem by using your FTP client to delete and then re-create the backup directory: %s', 'xpressme-backup'), '<code>' . $this->backup_dir . '</code>');
				?></div><?php 
				$whoops = true;
			}
		}

		

		if ( !file_exists($this->backup_dir . 'index.php') )
			@ touch($this->backup_dir . 'index.php');
		?><div class='wrap'>
		<h2><?php _e('Backup','xpressme-backup') ?></h2>
		<form method="post" action="">
		<?php if ( function_exists('wp_nonce_field') ) wp_nonce_field($this->referer_check_key); ?>
		<fieldset class="options"><legend><?php _e('Tables','xpressme-backup') ?></legend>
		<table align="center" cellspacing="5" cellpadding="5">
		<tr><td width="50%" align="left" class="alternate" valign="top">
		<div class="tables-list core-tables alternate">
		<h4><?php _e('These core WordPress tables will always be backed up:','xpressme-backup') ?></h4><ul><?php
		$excs = (array) get_option('xpressme_backup_excs');

		foreach ($wp_backup_default_tables as $table) {
			if ( $table == $wpdb->comments ) {
				$checked = ( isset($excs['spam']) && is_array($excs['spam'] ) && in_array($table, $excs['spam']) ) ? ' checked=\'checked\'' : '';
				echo "<li><input type='hidden' name='core_tables[]' value='$table' /><code>$table</code> <span class='instructions'> <input type='checkbox' name='exclude-spam[]' value='$table' $checked /> " . __('Exclude spam comments', 'xpressme-backup') . '</span></li>';
			} elseif ( function_exists('wp_get_post_revisions') && $table == $wpdb->posts ) {
					$checked = ( isset($excs['revisions']) && is_array($excs['revisions'] ) && in_array($table, $excs['revisions']) ) ? ' checked=\'checked\'' : '';
				echo "<li><input type='hidden' name='core_tables[]' value='$table' /><code>$table</code> <span class='instructions'> <input type='checkbox' name='exclude-revisions[]' value='$table' $checked /> " . __('Exclude post revisions', 'xpressme-backup') . '</span></li>';
			} else {
				echo "<li><input type='hidden' name='core_tables[]' value='$table' /><code>$table</code></li>";
			}
		}
		?></ul>
		</div>
		</td><td width="50%" align="left" valign="top">
		<div class="tables-list extra-tables" id="extra-tables-list">
		<?php 
		if (count($other_tables) > 0) { 
			$select_all = __('Select all','xpressme-backup');
			$select_none = __('Select none','xpressme-backup');
			?>
			<h4><?php _e('You may choose to include any of the following tables:','xpressme-backup'); ?></h4>
			<ul>
			<script type="text/javascript">
			//<![CDATA[
				var wpdbBackup = function() {};
				(function(b){
					var n = function(c) {
						var p = document.getElementsByTagName("input");
						for(var i=0;i<p.length;i++)
							if('other_tables[]' == p[i].getAttribute('name'))
								p[i].checked = c;
					}
					b.a = function() { n(true) }
					b.n = function() { n(false) }

					document.write('<p><a href="javascript:void(0)" onclick="wpdbBackup.a()"><?php echo $select_all ?></a> / <a href="javascript:void(0)" onclick="wpdbBackup.n()"><?php echo $select_none ?></a></p>');
				})(wpdbBackup)
			//]]>
			</script>
	
			<?php
			foreach ($other_tables as $table) {
				?>
				<li><label><input type="checkbox" name="other_tables[]" value="<?php echo $table; ?>" /> <code><?php echo $table; ?></code></label>
				<?php 
			}
			?></ul><?php 
		}
		?></div>
		</td></tr></table>
		</fieldset>
		
		<fieldset class="options">
			<legend><?php _e('Backup Options','xpressme-backup'); ?></legend>
			<p><?php  _e('What to do with the backup file:','xpressme-backup'); ?></p>
			<ul>
			<li><label for="do_save">
				<input type="radio" id="do_save" name="deliver" value="none" style="border:none;" />
				<?php _e('Save to server','xpressme-backup'); 
				echo " (<code>" . $this->backup_dir . "</code>)"; ?>
			</label></li>
			<li><label for="do_download">
				<input type="radio" checked="checked" id="do_download" name="deliver" value="http" style="border:none;" />
				<?php _e('Download to your computer','xpressme-backup'); ?>
			</label></li>
			<li><label for="do_email">
				<input type="radio" name="deliver" id="do_email" value="smtp" style="border:none;" />
				<?php _e('Email backup to:','xpressme-backup'); ?>
				<input type="text" name="backup_recipient" size="20" value="<?php echo get_option('admin_email'); ?>" />
			</label></li>
			</ul>
			<p><?php  _e('Data conversion option.','xpressme-backup'); ?></p>
			<ul>
			<?php
			$extras_option = (array) get_option('xpressme_backup_extras_option');
			if(WPLANG == 'ja_EUC'){
				echo '<li><label for="do_euc_to_utf8">';
				if($this->is_mbstring()){
					if ($extras_option['do_euc_to_utf8']) $checked = 'checked'; else $checked = '';
					echo	'<input type="checkbox" name="do_euc_to_utf8" id="do_euc_to_utf8" value="1" ' .$checked .' />';
					echo __('Converte EUC-JP to UTF-8','xpressme-backup');
				} else {
					echo	'<input type="checkbox" name="do_euc_to_utf8" id="do_euc_to_utf8" value="0" disabled="1"/>';
					echo __('Converte EUC-JP to UTF-8','xpressme-backup');
					echo ' (<span style="color:#ff0000">' . __('The server used does not support the mb_convert_encoding() function.','xpressme-backup') . '</span>)';
				}
				echo '</label></li>';
			} else {
				echo '<input type="hidden" name="do_euc_to_utf8" id="do_euc_to_utf8" value="0" />';
			}
			if($wp_version =='2.0.11-ME'){
				echo '<li><label for="set_default_role">';
				if ($extras_option['set_default_role']) $checked = 'checked'; else $checked = '';
				echo '<input type="checkbox" name="set_default_role" id="set_default_role" value="1" '.$checked . ' />';
				echo __('Changes from the user roles name of WordPressME to the user roles name of Wordpress.','xpressme-backup');
				echo '</label></li>';
			} else {
				echo '<input type="hidden" name="set_default_role" id="set_default_role" value="0" />';
			}
			
			echo '<li><label for="rename_prefix">';
				if ($extras_option['do_rename_prefix']) $checked = 'checked'; else $checked = '';
				echo '<input type="checkbox" name="do_rename_prefix" id="do_rename_prefix" value="1" '. $checked . ' />';
				echo __('Rename DB Prefix','xpressme-backup');
				echo '<input type="text" name="before_prefix" size="20" value="'.$table_prefix . '" readonly />';
				echo __('to','xpressme-backup');
				if (empty($extras_option['after_prefix'])){
					echo '<input type="text" name="after_prefix" size="20" value="'. $table_prefix . '" />';
				} else {
					echo '<input type="text" name="after_prefix" size="20" value="'. $extras_option['after_prefix'] . '" />';
				}
			echo '</label></li>';
			echo '<li><label for="change_uri">';
				if ($extras_option['do_change_uri']) $checked = 'checked'; else $checked = '';
				echo '<input type="checkbox" name="do_change_uri" id="do_change_uri" value="1" '. $checked . ' />';
				$site_uri = get_option('siteurl');
				echo __('Change URL','xpressme-backup');
				echo '<div style="padding-left: 20px;">';
				echo '<input type="text" name="before_uri" size="50" value="' . $site_uri . '" readonly /><br />';
				echo __('to','xpressme-backup') . '<br />';
				echo '<input type="text" name="after_uri" size="50" value="'. $extras_option['after_uri'] . '" />';
				echo '</div>';
			echo '</label></li>';
			echo '<li><label for="del_active_plugin">';
				if ($extras_option['del_active_plugin']) $checked = 'checked'; else $checked = '';

				echo '<input type="checkbox" name="del_active_plugin" id="del_active_plugin" value="1" '. $checked . ' />';
				echo __('In the backup data,all plug-ins are Deactivate state.','xpressme-backup');
			echo '</label></li>';
			?>
			</ul>

			<?php if ( ! $whoops ) : ?>
			<input type="hidden" name="do_backup" id="do_backup" value="backup" /> 
			<p class="submit">
				<input type="submit" name="submit" onclick="document.getElementById('do_backup').value='fragments';" value="<?php _e('Backup now!','xpressme-backup'); ?>" />
			</p>
			<?php else : ?>
				<div class="updated xpressme-backup-updated error"><p><?php _e('WARNING: Your backup directory is <strong>NOT</strong> writable!','xpressme-backup'); ?></p></div>
			<?php endif; // ! whoops ?>
		</fieldset>
		<?php do_action('wp_db_b_backup_opts'); ?>
		</form>
		
		<?php
		// this stuff only displays if some sort of wp-cron is available 
		$cron = ( function_exists('wp_schedule_event') ) ? true : false; // wp-cron in WP 2.1+
		$cron_old = ( function_exists('xpressme_cron_init') && ! $cron ) ? true : false; // wp-cron plugin by Skippy
		if ( $cron_old || $cron ) :
			echo '<fieldset class="options"><legend>' . __('Scheduled Backup','xpressme-backup') . '</legend>';
			$datetime = get_option('date_format') . ' ' . get_option('time_format');
			if ( $cron ) :
				$next_cron = wp_next_scheduled('wp_db_backup_cron');
				if ( ! empty( $next_cron ) ) :
					?>
					<p id="backup-time-wrap">
					<?php printf(__('Next Backup: %s','xpressme-backup'), '<span id="next-backup-time">' . gmdate($datetime, $next_cron + (get_option('gmt_offset') * 3600)) . '</span>'); ?>
					</p>
					<?php 
				endif;
			elseif ( $cron_old ) :
				?><p><?php printf(__('Last WP-Cron Daily Execution: %s','xpressme-backup'), gmdate($datetime, get_option('xpressme_cron_daily_lastrun') + (get_option('gmt_offset') * 3600))); ?><br /><?php 
				printf(__('Next WP-Cron Daily Execution: %s','xpressme-backup'), gmdate($datetime, (get_option('xpressme_cron_daily_lastrun') + (get_option('gmt_offset') * 3600) + 86400))); ?></p><?php 
			endif;
			?><form method="post" action="">
			<?php if ( function_exists('wp_nonce_field') ) wp_nonce_field($this->referer_check_key); ?>
			<div class="tables-list">
			<h4><?php _e('Schedule: ','xpressme-backup'); ?></h4>
			<?php 
			if ( $cron_old ) :
				$xpressme_cron_backup_schedule = get_option('xpressme_cron_backup_schedule');
				$schedule = array(0 => __('None','xpressme-backup'), 1 => __('Daily','xpressme-backup'));
				foreach ($schedule as $value => $name) {
					echo ' <input type="radio" style="border:none;" name="cron_schedule"';
					if ($xpressme_cron_backup_schedule == $value) {
						echo ' checked="checked" ';
					}
					echo 'value="' . $value . '" /> ' . $name;
				}
			elseif ( $cron ) :
				echo apply_filters('wp_db_b_schedule_choices', wp_get_schedules() );
			endif;
			$cron_recipient = get_option('xpressme_cron_backup_recipient');
			if (! is_email($cron_recipient)) {
				$cron_recipient = get_option('admin_email');
			}
			$cron_recipient_input = '<p><label for="cron_backup_recipient">' . __('Email backup to:','xpressme-backup') . ' <input type="text" name="cron_backup_recipient" id="cron_backup_recipient" size="20" value="' . $cron_recipient . '" /></label></p>';
			echo apply_filters('wp_db_b_cron_recipient_input', $cron_recipient_input);
			echo '<p class="submit"><input type="submit" name="submit" value="' . __('Schedule backup','xpressme-backup') . '" /></p>';
			echo '</div>';
			$cron_tables = get_option('xpressme_cron_backup_tables');
			if (! is_array($cron_tables)) {
				$cron_tables = array();
			}
			if (count($other_tables) > 0) {
				echo '<div class="tables-list alternate" id="include-tables-list">';
				echo '<h4>' . __('Tables to include in the scheduled backup:','xpressme-backup') . '</h4><ul>';
				foreach ($other_tables as $table) {
					echo '<li><input type="checkbox" ';
					if (in_array($table, $cron_tables)) {
						echo 'checked="checked" ';
					}
					echo "name='xpressme_cron_backup_tables[]' value='{$table}' /> <code>{$table}</code></li>";
				}
				echo '</ul></div>';
			}
			echo '<input type="hidden" name="xpressme_cron_backup_options" value="SET" /></form>';
			echo '</fieldset>';
		endif; // end of wp_cron (legacy) section
		
		echo '</div><!-- .wrap -->';
		
	} // end wp_backup_menu()

	function get_sched() {
		$options = array_keys( (array) wp_get_schedules() );
		$freq = get_option('xpressme_cron_backup_schedule'); 
		$freq = ( in_array( $freq , $options ) ) ? $freq : 'never';
		return $freq;
	}

	function schedule_choices($schedule) { // create the cron menu based on the schedule
		$xpressme_cron_backup_schedule = $this->get_sched();
		$next_cron = wp_next_scheduled('wp_db_backup_cron');
		$xpressme_cron_backup_schedule = ( empty( $next_cron ) ) ? 'never' : $xpressme_cron_backup_schedule;
		$sort = array();
		foreach ( (array) $schedule as $key => $value ) $sort[$key] = $value['interval'];
		asort( $sort );
		$schedule_sorted = array();
		foreach ( (array) $sort as $key => $value ) $schedule_sorted[$key] = $schedule[$key];
		$menu = '<ul>';
		$schedule = array_merge( array( 'never' => array( 'interval' => 0, 'display' => __('Never','xpressme-backup') ) ),
			(array) $schedule_sorted );
		foreach ( $schedule as $name => $settings) {
			$interval = (int) $settings['interval'];
			if ( 0 == $interval && ! 'never' == $name ) continue;
			$display = ( ! '' == $settings['display'] ) ? $settings['display'] : sprintf(__('%s seconds','xpressme-backup'),$interval);
			$menu .= "<li><input type='radio' name='xpressme_cron_schedule' style='border:none;' ";
			if ($xpressme_cron_backup_schedule == $name) {
				$menu .= " checked='checked' ";
			}
			$menu .= "value='$name' /> $display</li>";
		}
		$menu .= '</ul>';
		return $menu;
	} // end schedule_choices()
	
	function wp_cron_daily() { // for legacy cron plugin
		$schedule = intval(get_option('xpressme_cron_backup_schedule'));
		// If scheduled backup is disabled
		if (0 == $schedule)
		        return;
		else return $this->cron_backup();
	} 

	function cron_backup() {
		global $table_prefix, $wpdb;
		$all_tables = $wpdb->get_results("SHOW TABLES", ARRAY_N);
		$all_tables = array_map(create_function('$a', 'return $a[0];'), $all_tables);
		$core_tables = array_intersect($all_tables, $this->core_table_names);
		$other_tables = get_option('xpressme_cron_backup_tables');
		$recipient = get_option('xpressme_cron_backup_recipient');
		$backup_file = $this->db_backup($core_tables, $other_tables);
		if (false !== $backup_file) 
			return $this->deliver_backup($backup_file, 'smtp', $recipient, 'main');
		else return false;
	}

	function add_sched_options($sched) {
		$sched['weekly'] = array('interval' => 604800, 'display' => __('Once Weekly','xpressme-backup'));
		return $sched;
	}

	/**
	 * Checks that WordPress has sufficient security measures 
	 * @param string $kind
	 * @return bool
	 */
	function wp_secure($kind = 'warn', $loc = 'main') {
		global $wp_version;
		
		if ( function_exists('wp_verify_nonce') ) return true;
		else {
			$this->error(array('kind' => $kind, 'loc' => $loc, 'msg' => sprintf(__('Your WordPress version, %1s, lacks important security features without which it is unsafe to use the WP-DB-Backup plugin.  Hence, this plugin is automatically disabled.  Please consider <a href="%2s">upgrading WordPress</a> to a more recent version.','xpressme-backup'),$wp_version,'http://wordpress.org/download/')));
			return false;
		}
		
	}

	/**
	 * Checks that the user has sufficient permission to backup
	 * @param string $loc
	 * @return bool
	 */
	function can_user_backup($loc = 'main') {
		$can = false;
		// make sure WPMU users are site admins, not ordinary admins
		if ( function_exists('is_site_admin') && ! is_site_admin() )
			return false;
		if ( ( $this->wp_secure('fatal', $loc) ) && current_user_can('import') )
			$can = $this->verify_nonce($_REQUEST['_wpnonce'], $this->referer_check_key, $loc);
		if ( false == $can ) 
			$this->error(array('loc' => $loc, 'kind' => 'fatal', 'msg' => __('You are not allowed to perform backups.','xpressme-backup')));
		return $can;
	}

	/**
	 * Verify that the nonce is legitimate
	 * @param string $rec 	the nonce received
	 * @param string $nonce	what the nonce should be
	 * @param string $loc 	the location of the check
	 * @return bool
	 */
	function verify_nonce($rec = '', $nonce = 'X', $loc = 'main') {
		if ( wp_verify_nonce($rec, $nonce) )
			return true;
		else 
			$this->error(array('loc' => $loc, 'kind' => 'fatal', 'msg' => sprintf(__('There appears to be an unauthorized attempt from this site to access your database located at %1s.  The attempt has been halted.','xpressme-backup'),get_option('home'))));
	}

	/**
	 * Check whether a file to be downloaded is  
	 * surreptitiously trying to download a non-backup file
	 * @param string $file
	 * @return null
	 */ 
	function validate_file($file) {
		if ( (false !== strpos($file, '..')) || (false !== strpos($file, './')) || (':' == substr($file, 1, 1)) )
			$this->error(array('kind' => 'fatal', 'loc' => 'frame', 'msg' => __("Cheatin' uh ?",'xpressme-backup')));
	}
	
	function extras_filter($query_line){
		$extras_option = get_option('xpressme_backup_extras_option');
		
		// Change EUC to UTF-8
		if ($extras_option['do_euc_to_utf8'] && $this->is_mbstring()){
			$query_line = mb_convert_encoding(mb_convert_encoding($query_line,"sjis-win","EUC-JP"),"UTF-8","sjis-win");
			$tmp = preg_replace('/DEFAULT\s*CHARSET\s*=\s*ujis/','DEFAULT CHARSET=utf8',$query_line);
			if (empty($buf)) $query_line = $tmp;
			if (preg_match_all('/s:([0-9]+):"(.*?)";/',$query_line,$matchs)){
				$i_count = count($matchs[0]);
				for($i=0; $i < $i_count ;$i++){
					$org = $matchs[0][$i];
					$num = $matchs[1][$i];
					$str = $matchs[2][$i];
					$str =  str_replace('\r\n','\n',$str);

					$volm = strlen(bin2hex($str)) / 2;
					
					if ($num != $volm){
						$org =  str_replace('\\','\\\\',$org);
						$org =  str_replace('/','\\/',$org);
						$org =  str_replace('(','\\(',$org);
						$org =  str_replace(')','\\)',$org);
						$org =  str_replace('?','\\?',$org);
						$org =  str_replace('+','\\+',$org);
						$org =  str_replace('*','\\*',$org);
						$org =  str_replace('[','\\[',$org);
						$org =  str_replace(']','\\]',$org);					
						$org =  str_replace('$','\\$',$org);
						$org =  str_replace('{','\\{',$org);					
						$org =  str_replace('}','\\}',$org);
						$org =  str_replace('^','\\^',$org);
						$org =  str_replace('.','\\.',$org);
				
						$src = '/' . $org . '/';
						$dist = 's:'. $volm . ':"' . $str . '";';
						if(preg_match($src,$query_line)){
							$query_line = preg_replace($src,$dist,$query_line);
						}
					}
				}
			}
			$seach = "/'blog_charset'.*'EUC-JP'/";
			$src = "/'EUC-JP'/";
			$dist = "'UTF-8'";
			if(preg_match($seach,$query_line)){
				$query_line = preg_replace($src,$dist,$query_line);
			}
		}
		
		// Change Data Prefix
		if ($extras_option['do_rename_prefix']){
			if (!empty($extras_option['before_prefix']) && !empty($extras_option['after_prefix'])){
				$src = '/' . $extras_option['before_prefix'] . '/';
				$dist = $extras_option['after_prefix'];
				if(preg_match($src,$query_line)){
					$query_line = preg_replace($src,$dist,$query_line);
				}
			}
		}
		
		// Change User_Role_Name(WordPressME to Wordpress)
		if ($extras_option['set_default_role']){
			$src = '/' . "(INSERT INTO `.+_options` VALUES \([0-9]+, [0-9]+, '.+_user_roles', 'Y', [0-9]+, ')(a:.+}}})('.+\);)" . '/';
			if(preg_match($src,$query_line)){
				$patterns = array();
				$patterns[0] = '/s:[0-9]+:"[^"]+ - \(Administrator\)"/';
				$patterns[1] = '/s:[0-9]+:"[^"]+ - \(Editor\)"/';
				$patterns[2] = '/s:[0-9]+:"[^"]+ - \(Author\)"/';
				$patterns[3] = '/s:[0-9]+:"[^"]+ - \(Contributor\)"/';
				$patterns[4] = '/s:[0-9]+:"[^"]+ - \(Subscriber\)"/';
				$replacements = array();
				$replacements[0] = 's:13:"Administrator"';
				$replacements[1] = 's:6:"Editor"';
				$replacements[2] = 's:6:"Author"';
				$replacements[3] = 's:11:"Contributor"';
				$replacements[4] = 's:10:"Subscriber"';
				$ans = preg_replace($patterns,$replacements,$query_line);
				if (!empty($ans)) $query_line = $ans;
			}
		}
		
		// Change URI Clear
		if ($extras_option['do_change_uri']){
			if (!empty($extras_option['before_uri']) && !empty($extras_option['after_uri'])){
				$org =  $extras_option['before_uri'];
				$org =  str_replace('/','\\/',$org);
				$src = '/' . $org . '/';
				$dist = $extras_option['after_uri'];
				if(preg_match($src,$query_line)){
					$query_line = preg_replace($src,$dist,$query_line);
					
				}
			}
		}
		
		// Active Plugin Clear
		if ($extras_option['del_active_plugin']){
				$src = '/' . "(INSERT INTO `.+_options` VALUES \([0-9]+, [0-9]+, 'active_plugins', 'Y', [0-9]+, ')(a:.+})('.+\);)" . '/';
				if(preg_match($src,$query_line,$matches)){
					if(preg_match('/xpressme\/xpressme.php/',$matches[2])){
						$query_line = "\n" . $matches[1] . 'a:1:{i:0;s:21:"xpressme/xpressme.php";}' . $matches[3];
					} else {
						$query_line = "\n" . $matches[1] . 'a:0:{}' . $matches[3];
					}
				}			
		}
		
		// XPressME Buckup Option Data Clear
		$src = '/' . "INSERT INTO `.+_options` VALUES \([0-9]+, [0-9]+, 'xpressme_backup_excs'" . '/';
		if(preg_match($src,$query_line)){
			$query_line = "\n# Skip XPressME Buckup 'xpressme_backup_excs' Option Data\n";
		}
		$src = '/' . "INSERT INTO `.+_options` VALUES \([0-9]+, [0-9]+, 'xpressme_backup_extras_option'" . '/';
		if(preg_match($src,$query_line)){
			$query_line = "\n# Skip XPressME Buckup 'xpressme_backup_extras_option' Option Data\n";
		}

		return $query_line;
		
	}
	function is_mbstring(){
		return function_exists('mb_convert_encoding');
	}
}

function wpdbBackup_init() {
	global $mywpdbbackup;
	$mywpdbbackup = new wpdbBackup(); 	
}

add_action('plugins_loaded', 'wpdbBackup_init');
?>
