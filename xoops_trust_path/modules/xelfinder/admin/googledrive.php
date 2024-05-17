<?php
/**
 * X-elFinder module for XCL
 * @package    XelFinder
 * @version    XCL 2.4.0
 * @author     Other authors Nuno Luciano (aka Gigamaster) 2020 XCL/PHP7
 * @author     Naoki Sawada (aka Nao-pon) <https://github.com/nao-pon>
 * @copyright  (c) 2005-2024 Authors
 * @license    https://github.com/xoopscube/xcl/blob/master/GPL_V2.txt
 */

$php54up = false;
$vendor  = false;

if ( $php54up = version_compare( PHP_VERSION, '5.4.0', '>=' ) ) {

	// Check if vendor file exists and print a message
	$filename = $mytrustdirpath . '/plugins/vendor/autoload.php';
	if ( ! file_exists( $filename ) ) {
        $googledrivefail = sprintf( '<div class="error"><p>'.xelfinderAdminLang ( 'COMPOSER_UPDATE_ERROR' ).'</p></div><div class="message-warning"><p>'.xelfinderAdminLang ( 'COMPOSER_UPDATE_FAIL' ).'</p></div>', $filename );
	} else {
        $googledrivepass = sprintf('<div class="success">'.xelfinderAdminLang ( 'COMPOSER_UPDATE_SUCCESS' ).'</div>', $filename);

        // AUTOLOAD
		if ( include_once $mytrustdirpath . '/plugins/vendor/autoload.php' ) {
			$vendor        = true;
			$selfURL       = XOOPS_MODULE_URL . '/' . $mydirname . '/admin/index.php?page=googledrive';
			$sessTokenKey  = $mydirname . 'AdminGoogledriveToken';
			$sessClientKey = $mydirname . 'AdminGoogledriveClientKey';
			$client        = null;
			$clientId      = $clientSecret = '';
			$config        = $xoopsModuleConfig;

			if ( ! empty( $_POST['json'] ) ) {
				$json = @json_decode( $_POST['json'], true, 512, JSON_THROW_ON_ERROR );
				if ( $json && isset( $json['web'] ) ) {
					$clientId     = @$json['web']['client_id'];
					$clientSecret = @$json['web']['client_secret'];
				}
			}
			if ( ! empty( $_POST['ClientId'] ) && ! empty( $_POST['ClientSecret'] ) ) {
				$clientId     = trim( $_POST['ClientId'] );
				$clientSecret = trim( $_POST['ClientSecret'] );
			} else {
				if ( isset( $config['googleapi_id'] ) ) {
					$clientId = $config['googleapi_id'];
				}
				if ( isset( $config['googleapi_secret'] ) ) {
					$clientSecret = $config['googleapi_secret'];
				}
			}

			if ( $clientId && $clientSecret ) {
				$_SESSION[ $sessClientKey ] = [
					'ClientId'     => $clientId,
					'ClientSecret' => $clientSecret
				];
			} elseif ( isset( $_SESSION[ $sessClientKey ] ) ) {
				$clientId     = $_SESSION[ $sessClientKey ]['ClientId'];
				$clientSecret = $_SESSION[ $sessClientKey ]['ClientSecret'];
			}

			if ( ! empty( $_SESSION[ $sessClientKey ] ) && ! isset( $_GET ['start'] ) ) {

				$client = new \Google_Client();
				$client->setClientId( $_SESSION[ $sessClientKey ]['ClientId'] );
				$client->setClientSecret( $_SESSION[ $sessClientKey ]['ClientSecret'] );
				$client->setRedirectUri( $selfURL );

				$service = new \Google_Service_Drive( $client );
				if ( isset( $_GET['code'] ) ) {
					$client->authenticate( $_GET['code'] );
					$_SESSION[ $sessTokenKey ] = $client->getAccessToken();
				}

				if ( isset( $_SESSION[ $sessTokenKey ] ) && isset( $_SESSION[ $sessTokenKey ]['access_token'] ) ) {
					$client->setAccessToken( $_SESSION[ $sessTokenKey ] );
				}
			}
		}
	}
}

xoops_cp_header();
include __DIR__ . '/mymenu.php';

echo '<h2>'. xelfinderAdminLang( 'GOOGLEDRIVE_GET_TOKEN' ) .'</h2>';


if ( $php54up && $vendor ) {
	$form = true;
	if ( $client ) {
		if ( empty( $_POST ) && $client->getAccessToken() ) {
			try {
				$aToken = $client->getAccessToken();
				$token  = [
					'client_id'     => $client->getClientId(),
					'client_secret' => $client->getClientSecret(),
					'access_token'  => $aToken['access_token']
				];
				if ( isset( $aToken['refresh_token'] ) ) {
					unset( $token['access_token'] );
					$token['refresh_token'] = $aToken['refresh_token'];
				}
				$ext_token = json_encode( $token, JSON_THROW_ON_ERROR );

                echo $googledrivepass;

				echo '<h3>Google Drive API Token</h3>';
				echo '<div><textarea class="allselect" style="width:70%;height:5em;" spellcheck="false">' . $ext_token . '</textarea></div>';

				echo '<h3>Example to Volume Driver Setting</h3>'
                    .'<div class="confirm">The default "root" folder is <a href="https://drive.google.com/drive/my-drive" target="_blank">"My Drive" of your Google Drive</a><br>'
                    .'You can set an ID of a specific folder.</div>'
                    .'<div class="ui-card-full">'
				    .'<p>Folder ID <input type=text id="xelfinder_googledrive_folder" value="root"></p>';
				echo '<p>You can find the folder ID to the URL(folders/[Folder ID]) of the site of <a href="https://drive.google.com/drive/" target="_blank">GoogleDrive</a>.</p>'
                    .'</div>';
                echo '<div class="confirm">Copy and paste to a newline in <a href="index.php?action=PreferenceEdit&confmod_id=11" target="_blank">Preferences > Volume Drivers</a></div>';
				echo '<div class="ui-card-full">'
                    .'<textarea class="allselect" style="width:70%;height:7em;" id="xelfinder_googledrive_volconf" spellcheck="false">xelfinder:GoogleDrive:root:GoogleDrive:gid=1|id=gd|ext_token=' . $ext_token . '</textarea>'
                    .'</div>';
				echo "<script>(function($){
					$('#xelfinder_googledrive_folder').on('change keyup mouseup paste', function(e) {
						var self = $(this);
						setTimeout(function(){
							var conf = $('#xelfinder_googledrive_volconf');
								data = conf.val();
							conf.val(data.replace(/GoogleDrive:[^:]*:/, 'GoogleDrive:' + self.val() + ':'));
						}, e.type === 'paste'? 100 : 0);
					});
					$('textarea.allselect').on('focus', function() { $(this).select(); });
				})(jQuery);</script>";


				echo '<h3>Authentication and authorization reauthorization</h3>';

                echo'<p><a class="button" href="' . $selfURL . '&start">Reauthorization</a></p>';
				$form = false;

			} catch ( Google_Exception $e ) {
				echo $e->getMessage();
			}
		} elseif ( ! empty( $_POST['scopes'] ) ) {
			if ( ! empty( $_POST['revoke'] ) ) {
                // TODO BUGFIX! @gigamaster Argument 1 passed to Google_AccessToken_Revoke::revokeToken() must be of the type array, null given
				//$client->revokeToken();
                // TODO BUGFIX! @gigamaster
                $client->revokeToken(['refresh_token' => $token]);
			}
			$scopes = [];
			foreach ( $_POST['scopes'] as $scope ) {
				switch ( $scope ) {
					case 'DRIVE' :
					case 'DRIVE_READONLY' :
					case 'DRIVE_FILE' :
					case 'DRIVE_PHOTOS_READONLY' :
					case 'DRIVE_APPS_READONLY' :
						$scopes[] = constant( 'Google_Service_Drive::' . $scope );
				}
			}
			$client->setScopes( $scopes );
			if ( ! empty( $_POST['offline'] ) ) {
				$client->setApprovalPrompt( 'force' );
				$client->setAccessType( 'offline' );
			}
			$authUrl = $client->createAuthUrl();
            echo '<p>All requests to the API must be authorized by an authenticated user.</p>
        <p>Both Sign In With Google and One Tap authentication include a consent screen which tells users the application 
            requesting access to their data, what kind of data they are asked for and the terms that apply.</p>';
			echo '<a class="button" href="' . $authUrl . '" target="_blank">Please allow the application access.</a>';
			$form = false;
		}
	}
	if ( $form ) {
		?>
        <h3>Authentication options</h3>
        <div class="ui-card-full">
            <h4>Step by step</h4>
            <ol>
                <li>Create a "New Project" in Google Developers Console :
                    <a href="https://console.developers.google.com/apis/dashboard" target="_brank">console.developers.google.com/apis/dashboard</a>)
                </li>
                <li>Enable Drive API<br>
                    To enable the Drive API, complete these steps:<br>
                    <ol>
                        <li>Go to the <a href="https://console.developers.google.com/" target="_brank">Google API Console</a>.</li>
                        <li>Select a project.</li>
                        <li>In the sidebar on the left, expand <strong>APIs &amp; auth</strong> and select <strong>APIs</strong>.</li>
                        <li>In the displayed list of available APIs, click the Drive API link and
                            click <strong>Enable API</strong>.</li>
                    </ol>
                </li>
                <li>Make Authentication information (Type of Web Server & User data)</li>
                <li>Make OAuth 2.0 Client<br>
                    Redirect URI: <pre><?php echo $selfURL; ?></pre>
                </li>
                <li>Get JSON and Paste it next TextArea</li>
                <li>And Click "Get authentication link"</li>
                <li>Then Approve this app in your account</li>
            </ol>
        </div>
        <form action="index.php?page=googledrive" method="post">

            <hr>

            <h3>Web client ID and Secret key</h3>

            <div class="confirm">Copy and past here the content of json file "client_secrect_xxxx.json"</div>

            <div class="ui-card-full">
                <p>JSON client secret :</p>

                <textarea name="json" rows="4" style="width: 40em"></textarea>

                <p><strong>OR</strong></p>
                <p>ClientId:<br>
                    <input type="text" name="ClientId" style="width: 40em" value="<?php echo htmlspecialchars( $clientId ); ?>"><br>
                </p>
                <p>ClientSecret:<br>
                    <input type="text" name="ClientSecret" style="width: 40em" value="<?php echo htmlspecialchars( $clientSecret ); ?>">
                </p>
            </div>

            <h3>Required scopes</h3>
            <div class="ui-card-full">
            <p>
                <input type="checkbox" name="scopes[]" value="DRIVE" checked="checked"> Google Drive (Read & Write)
                <br>
                <input type="checkbox" name="scopes[]" value="DRIVE_READONLY"> Google Drive (Read only)
                <br>
                <input type="checkbox" name="scopes[]" value="DRIVE_FILE"> Google Drive (Read & Write, Only opened or created with this app)
                <br>
                <input type="checkbox" name="scopes[]" value="DRIVE_PHOTOS_READONLY"> Google Photos (Read only)
                <br>
                <input type="checkbox" name="scopes[]" value="DRIVE_APPS_READONLY"> Google Apps (Read only)
            </p>
            </div>

            <h3>Continuous connectivity</h3>
            <div class="confirm">
                A short-lived access token helps improve the security of our applications, but it comes with a cost: when it expires, the user needs to log in again to get a new one. Frequent re-authentication can diminish the perceived user experience of your application</div>
            <div class="ui-card-full">
                <p>Keeping Refresh Tokens Secure</p>
                <p><input type="radio" name="offline" value="1" checked="checked"> Yes</p>
                <p><input type="radio" name="offline" value="0"> No (Expiration time: 1 hour)</p>
            </div>

            <h3>Revoke previous authentication</h3>
            <div class="ui-card-full">
                <p><input type="checkbox" name="revoke" value="1"> Revoke</p>
                <p><input type="submit" value="Get authentication link"></p>
                <input type="hidden" name="auth" value="1">
            </div>
        </form>
    <?php

	}

} else if ( $php54up && $googledrivefail ) {

        echo $googledrivefail;
        echo '<hr>'
            .'<h2>'. xelfinderAdminLang( 'COMPOSER_UPDATE' ) .'</h2>'
            .'<div class="tips">'. xelfinderAdminLang( 'COMPOSER_UPDATE_HELP' ) .'</div>'
            .'<p><a class="button" href="./index.php?page=vendorup">'. xelfinderAdminLang( 'COMPOSER_RUN_UPDATE' ) .'</a></p>';

} else {
    echo '<div class="error"><p>Update Vendor requires PHP >= 7.4<br> Your PHP version is '. PHP_VERSION .'</p></div>';
}
xoops_cp_footer();
