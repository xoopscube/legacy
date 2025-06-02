<?php
/**
 * Smarty {json} plugin
 *
 * Type:       function
 * Name:       json
 * Date:       2025 XCL update by @gigamaster
 * Purpose:    Read JSON from file, decode and assign data to Smarty template variable
 * Syntax:     {json file="filename.json"}: 'file' is a required parameter (URL)
 *             Predefined additional parameters:
 *             - assign="data": assign all JSON data to template variable $data
 *             - obj2obj [ Boolean | default:false ]:
 *               decodes JSON objects as either PHP associative arrays or PHP objects
 *             - debug [ Boolean | default:false ]: print decoded data in template
 *             - local [ Boolean | default:false ]: disable local ssl verification
 *             Variable parameters:
 *             {json file="filename.json" home="homepage" lang="languages" local="true"}:
 *               assign (JSONdata)["homepage"] to template variable $home
 *               and (JSONdata)["languages"] to $lang,
 *               compare to: {config_load file="filename.conf" section="homepage"}
 * Install:    Drop into the plugin directory
 * @author     Nuno Luciano aka gigamaster 
 * @version    2.5.0 XCL 2025-05-25   
 * @author     Sander Aarts <smarty at jlix dot net>
 * @copyright  2009 Sander Aarts
 * @license    LGPL License
 * @link       http://github.com/xoopscube/legacy/
 * @param $params
 * @param $smarty
 * @return array|bool|float|int|mixed|stdClass|string|void|null
 */

function smarty_function_json($params, &$smarty) {
    
    $params['file'] ??= null;
    $params['obj2obj'] ??= true;
    $params['debug'] ??= false;
    $params['local'] ??= false;

    if (!is_callable('json_decode')) {
        $smarty->_trigger_fatal_error("{json} requires json_decode() function");
        return;
    }
    if (empty($params['file'])) {
        $smarty->_trigger_fatal_error("{json} parameter 'file' must not be empty");
        return;
    }
    if (isset($params['assign'], $params[$params['assign']])) {
        $smarty->_trigger_fatal_error("{json} parameter 'assign' conflicts with a variable assign parameter (both refer to the same variable)");
        return;
    }

    $file_to_fetch = $params['file'];
    $is_https = (strtolower(parse_url($file_to_fetch, PHP_URL_SCHEME)) === 'https');
    $use_local_ssl_options = ($params['local'] == true);
    $json_string_content = false;

    if ($is_https) {
        if ($use_local_ssl_options) {
            // HTTPS URL and local="true"
            // Disable SSL peer verification for local/dev environments
            $ssl_context_opts = ["ssl" => ["verify_peer" => false, "verify_peer_name" => false]];
            $stream_context = stream_context_create($ssl_context_opts);
            $json_string_content = @file_get_contents($file_to_fetch, false, $stream_context);
        } else {
            // HTTPS URL and local="false" 
            // Try with default PHP SSL verification
            $json_string_content = @file_get_contents($file_to_fetch);
            
            if ($json_string_content === false) {
                // Display error
                $error_message_html = '<style>body{background:#21212a;}'
                                    . '.error{color:#f44;border:2px solid darkred;padding:1rem;margin:20% auto;background-color:#ff000025;font-family:sans-serif;max-width:800px;}'
                                    . 'code{background:#500;color:#eee;}</style>'
                                    . '<div class="error">'
                                    . '<h3>JSON File Load Error (Smarty Plugin)</h3>'
                                    . '<p><b>Error:</b> SSL certificate issue trying to load JSON configuration. The file could not be fetched securely.</p>'
                                    . '<p><b>URL:</b> ' . htmlspecialchars($file_to_fetch) . '</p>'
                                    . '<p><b>Suggestion:</b> For a local development environment or trust source, add <code>local="true"</code> to <code>{json}</code> tag in your Theme or Template.</p>'
                                    . '<p><b>Example:</b> <code>{json file="' . htmlspecialchars($file_to_fetch) . '" theme_options="theme_options" local="true"}</code>.</p>'
                                    . '<p>Verify that your HTTP server is configured correctly for SSL certificate verification or use a local file.</p>'
                                    . '<p><em>Script execution has been stopped to prevent a redirect loop.</em></p>'
                                    . '</div>';

                if (headers_sent()) {
                    echo $error_message_html;
                } else {
                    echo $error_message_html;
                }
                exit();

            }
        }
    } else {
        // Not HTTPS URL, HTTP or local file path
        $json_string_content = @file_get_contents($file_to_fetch);
    }

    if ($json_string_content === false) {
        // catch failures - HTTPS with local=true) or non-HTTPS
        $smarty->_trigger_fatal_error("{json} failed to fetch file '{$file_to_fetch}'.");
        return;
    }
    
    $json = trim($json_string_content);
    $assoc = ($params['obj2obj'] == true) ? false : true;

    try {
        $data = json_decode($json, $assoc, 512, JSON_THROW_ON_ERROR);
    } catch (JsonException $e) {
        $error_decode_message = "{json} could not decode content from '{$file_to_fetch}'. Error: {$e->getMessage()}";
        if (!empty($params['assign'])) {
            $smarty->assign($params['assign'], $error_decode_message);
        } else {
            // error trigger
            $smarty->_trigger_fatal_error($error_decode_message);
        }
        return;
    }


    if ($params['debug'] == true) {
        echo "<pre>";
        print_r($data);
        echo "</pre>";
    }

    unset($params['file'], $params['obj2obj'], $params['debug'], $params['local']);

    $assign = [];
    foreach ($params as $key => $value) {
        if ($key === 'assign') {
            $assign[$value] = $data;
        } else {
            // Assign keys from JSON to Smarty variables
            if (isset($data)) {
                 $assign[$key] = $assoc ? ($data[$value] ?? null) : ($data->$value ?? null);
            } else {
                 $assign[$key] = null; // Assign null if $data is not available
            }
        }
    }

    if (count($assign) > 0) {
        $smarty->assign($assign);
    } elseif (isset($data)) { // Only return $data if successfully decoded
        return $data;
    }
    // If no assignment and $data this function will return null.
}
