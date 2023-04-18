<?php

class elFinderEditorOnlineConvert extends elFinderEditor
{
    protected $allowed = ['init', 'api'];

    public function enabled()
    {
        return defined('ELFINDER_ONLINE_CONVERT_APIKEY') && ELFINDER_ONLINE_CONVERT_APIKEY && (!defined('ELFINDER_DISABLE_ONLINE_CONVERT') || !ELFINDER_DISABLE_ONLINE_CONVERT);
    }

    public function init()
    {
        return ['api' => defined('ELFINDER_ONLINE_CONVERT_APIKEY') && ELFINDER_ONLINE_CONVERT_APIKEY && function_exists('curl_init')];
    }

    public function api()
    {
        // return array('apires' => array('message' => 'Currently disabled for developping...'));
        $endpoint = 'https://api2.online-convert.com/jobs';
        $category = $this->argValue('category');
        $convert = $this->argValue('convert');
        $options = $this->argValue('options');
        $source = $this->argValue('source');
        $filename = $this->argValue('filename');
        $mime = $this->argValue('mime');
        $jobid = $this->argValue('jobid');
        $string_method = '';
        $options = [];
        // Currently these converts are make error with API call. I don't know why.
        $nonApi = ['android', 'blackberry', 'dpg', 'ipad', 'iphone', 'ipod', 'nintendo-3ds', 'nintendo-ds', 'ps3', 'psp', 'wii', 'xbox'];
        if (in_array($convert, $nonApi)) {
            return ['apires' => []];
        }
        $ch = null;
        if ($convert && $source) {
            $request = ['input' => [['type' => 'remote', 'source' => $source]], 'conversion' => [['target' => $convert]]];

            if ($filename !== '') {
                $request['input'][0]['filename'] = $filename;
            }

            if ($mime !== '') {
                $request['input'][0]['content_type'] = $mime;
            }

            if ($category) {
                $request['conversion'][0]['category'] = $category;
            }

            if ($options && $options !== 'null') {
                $options = json_decode($options, true, 512, JSON_THROW_ON_ERROR);
            }
            if (!is_array($options)) {
                $options = [];
            }
            if ($options) {
                $request['conversion'][0]['options'] = $options;
            }

            $ch = curl_init($endpoint);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request));
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-Oc-Api-Key: ' . ELFINDER_ONLINE_CONVERT_APIKEY, 'Content-Type: application/json', 'cache-control: no-cache']);
        } else if ($jobid) {
            $ch = curl_init($endpoint . '/' . $jobid);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-Oc-Api-Key: ' . ELFINDER_ONLINE_CONVERT_APIKEY, 'cache-control: no-cache']);
        }

        if ($ch) {
            $response = curl_exec($ch);
            $info = curl_getinfo($ch);
            $error = curl_error($ch);
            curl_close($ch);

            if (!empty($error)) {
                $res = ['error' => $error];
            } else {
                $data = json_decode($response, true, 512, JSON_THROW_ON_ERROR);
                if (isset($data['status']) && isset($data['status']['code']) && $data['status']['code'] === 'completed') {
                    /** @var elFinderSession $session */
                    $session = $this->elfinder->getSession();
                    $urlContentSaveIds = $session->get('urlContentSaveIds', []);
                    $urlContentSaveIds['OnlineConvert-' . $data['id']] = true;
                    $session->set('urlContentSaveIds', $urlContentSaveIds);
                }
                $res = ['apires' => $data];
            }

            return $res;
        } else {
            return ['error' => ['errCmdParams', 'editor.OnlineConvert.api']];
        }
    }
}
