<?php

class Legacy_AIAssistantBlock
{
    private $api_token;
    private $api_url = 'https://api-inference.huggingface.co/models/';
    private $supported_languages = [
        'en' => 'English',
        'fr' => 'French',
        'ja' => 'Japanese',
        'pt' => 'Portuguese',
        'ru' => 'Russian'
    ];

    public function __construct($options)
    {
        // Debug configuration with better visibility
        error_log("AI Block Constructor Options: " . json_encode($options));
        
        // Fix token extraction
        if (is_array($options)) {
            $this->api_token = $options['api_token'] ?? '';
        } else if (is_string($options)) {
            $values = explode('|', $options);
            $this->api_token = trim($values[0] ?? '');
        }
        
        // Validate token format
        if (!empty($this->api_token) && !preg_match('/^hf_/', $this->api_token)) {
            error_log("Invalid token format. Should start with 'hf_'");
        }
        
        error_log("Token Status: " . (empty($this->api_token) ? 'Empty' : 'Present'));
    }

    public function processRequest($content, $type, $sourceLang = '', $targetLang = '')
    {
        if (empty($content)) {
            throw new Exception(_MB_LEGACY_BLOCK_AI_NO_CONTENT);
        }

        try {
            switch($type) {
                case 'translate':
                    // Use Facebook's mBART model for translations
                    $model = "facebook/mbart-large-50-many-to-many-mmt";
                    
                    // Map language codes to mBART format if needed
                    $mbartSourceLang = $this->getMbartLangCode($sourceLang);
                    $mbartTargetLang = $this->getMbartLangCode($targetLang);
                    
                    $data = [
                        'inputs' => $content,
                        'parameters' => [
                            'src_lang' => $mbartSourceLang,
                            'tgt_lang' => $mbartTargetLang
                        ]
                    ];
                    
                    error_log("Using mBART model with src_lang: $mbartSourceLang, tgt_lang: $mbartTargetLang");
                    break;
                case 'summarize':
                    $model = "facebook/bart-large-cnn";
                    $data = [
                        'inputs' => $content,
                        'parameters' => [
                            'max_length' => 130,
                            'min_length' => 30,
                        ]
                    ];
                    break;
                default: // enhance
                    $model = "facebook/bart-large-cnn";
                    $data = [
                        'inputs' => $content,
                        'parameters' => ['max_length' => 100]
                    ];
            }

            $result = $this->callHuggingFace($model, $data);
            
            // Debug response
            error_log("API Result: " . print_r($result, true));
            
            // Handle different response formats
            if (is_array($result)) {
                // Direct array response
                if (isset($result['generated_text'])) {
                    return $result['generated_text'];
                }
                // Array of results
                if (isset($result[0])) {
                    foreach(['summary_text', 'translation_text', 'generated_text'] as $key) {
                        if (isset($result[0][$key])) {
                            return $result[0][$key];
                        }
                    }
                }
                // Raw array response
                return json_encode($result);
            }
            
            // String response
            return (string)$result;
            
        } catch (Exception $e) {
            error_log("AI Processing Error: " . $e->getMessage());
            throw new Exception(_MB_LEGACY_BLOCK_AI_ERROR . ': ' . $e->getMessage());
        }
    }
    
    // Helper method to map our language codes to mBART format
    private function getMbartLangCode($langCode) {
        $mbartMap = [
            'en' => 'en_XX',
            'fr' => 'fr_XX',
            'ja' => 'ja_XX',
            'pt' => 'pt_XX',
            'ru' => 'ru_RU'  // Updated to ru_RU instead of ru_XX
        ];
        
        return $mbartMap[$langCode] ?? 'en_XX'; // Default to English if not found
    }

    private function callHuggingFace($model, $data) 
    {
        if (empty($this->api_token)) {
            error_log("Missing API token in callHuggingFace");
            throw new Exception(_MB_LEGACY_BLOCK_AI_NO_TOKEN);
        }

        $url = $this->api_url . $model;
        
        // Debug request
/*         error_log("Making API request:");
        error_log("- URL: " . $url);
        error_log("- Token Length: " . strlen($this->api_token));
        error_log("- Data: " . json_encode($data)); */

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $this->api_token,
                'Content-Type: application/json',
                'Accept: application/json',
                'Origin: ' . (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST']
            ],
            // SSL settings
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_TIMEOUT => 30,
            // Debug info
            CURLOPT_VERBOSE => true
        ]);

        // Capture CURL debug output
        $verbose = fopen('php://temp', 'w+');
        curl_setopt($ch, CURLOPT_STDERR, $verbose);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        // Log verbose debug info
        rewind($verbose);
        $verboseLog = stream_get_contents($verbose);
        error_log("CURL Debug: " . $verboseLog);

        curl_close($ch);

        // Log response details
        error_log("HTTP Code: " . $httpCode);
        
        if ($error) {
            throw new Exception("cURL Error: " . $error);
        }

        // Improved error handling with specific messages for common errors
        if ($httpCode !== 200) {
            // Handle specific HTTP error codes
            switch ($httpCode) {
                case 403:
                    error_log("API Authorization Error: " . substr($response, 0, 200) . "...");
                    throw new Exception(_MB_LEGACY_BLOCK_AI_ERROR);
                case 404:
                    error_log("Model Not Found: " . $model);
                    throw new Exception(_MB_LEGACY_BLOCK_AI_ERROR);
                case 429:
                    error_log("Rate Limit Exceeded: " . substr($response, 0, 200) . "...");
                    throw new Exception(_MB_LEGACY_BLOCK_AI_ERROR);
                default:
                    error_log("API Error (HTTP $httpCode): " . substr($response, 0, 200) . "...");
                    throw new Exception(_MB_LEGACY_BLOCK_AI_ERROR);
            }
        }

        $result = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            // Try to return raw response if JSON decode fails
            return $response;
        }

        return $result;
    }
}

// Block functions should be outside the class
function b_legacy_ai_assistant_show($options) 
{
    $action = ''; //enhance, translate

    // Parse options from pipe-separated string if needed
    if (is_string($options)) {
        $values = explode('|', $options);
    } else {
        $values = $options;
    }

    // TODO block bid if further duplication, used in block JavaScript 
    if (isset($block['bid'])) {
        // access the bid key
        $block_id = $block['bid'];
    } else {
        // handle the case where the bid key is not defined
        $block_id = '888'; // or some other default value
    }

    // Create block array with named options
    $block = array(
        'title' => _MB_LEGACY_BLOCK_AI_NAME,
        'options' => array(
            'bid' => $block_id,
            'api_token' => $values[0] ?? '',
            'max_tokens' => intval($values[1] ?? 1000),
            'temperature' => floatval($values[2] ?? 0.7),
            'model' => $values[3] ?? 'gpt-3.5-turbo',
            'side' => intval($values[4] ?? 0)
        )
    );

    error_log("Block Options: " . print_r($block['options'], true));

    // Process AI request if needed
    if (isset($_POST['ai_action']) && $_POST['ai_action'] === 'query') {
        try {
            $aiBlock = new Legacy_AIAssistantBlock($block['options']);
            
            // Get action type from POST
            $action = $_POST['type'] ?? 'enhance';
            $content = $_POST['ai_content'] ?? '';
            
            if (empty($content)) {
                throw new Exception(_MB_LEGACY_BLOCK_AI_NO_CONTENT);
            }

            // Handle translation specific parameters
            if ($action === 'translate') {
                $sourceLang = $_POST['source_lang'] ?? '';
                $targetLang = $_POST['target_lang'] ?? '';
                
                if (empty($sourceLang) || empty($targetLang)) {
                    throw new Exception(_MB_LEGACY_BLOCK_AI_LANG_ERROR);
                }
                
                if ($sourceLang === $targetLang) {
                    throw new Exception(_MB_LEGACY_BLOCK_AI_LANG_SAME_ERROR);
                }
                
                $result = $aiBlock->processRequest($content, $action, $sourceLang, $targetLang);
            } else {
                // For non-translation actions
                $result = $aiBlock->processRequest($content, $action);
            }
            
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'result' => $result]);
            
        } catch (Exception $e) {
            error_log("AI Block Error: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false, 
                'error' => $e->getMessage()
            ]);
        }
        exit;
    }

    return $block;
}

function b_legacy_ai_assistant_edit($options) 
{
    // Parse options
    if (is_string($options)) {
        $values = explode('|', $options);
        $options = [
            'api_token' => $values[0] ?? '',
            'max_tokens' => $values[1] ?? 1000,
            'temperature' => $values[2] ?? 0.7,
            'model' => $values[3] ?? 'facebook/mbart-large-50-many-to-many-mmt',
            'side' => $values[4] ?? 0
        ];
    }

    $form = '<table class="outer">';
    
    // API Token field
    $form .= '<tr>';
    $form .= '<td class="head">' . _MB_LEGACY_BLOCK_AI_TOKEN . '</td>';
    $form .= '<td class="even">';
    $form .= '<input type="text" name="options[0]" value="' . 
            htmlspecialchars($options['api_token']) . 
            '" size="60" placeholder="hf_..." required>';
    $form .= '</td></tr>';
    
    // Max tokens
    $form .= '<tr>';
    $form .= '<td class="head">' . _MB_LEGACY_BLOCK_AI_TOKENS . '</td>';
    $form .= '<td class="even">';
    $form .= '<input type="number" name="options[1]" value="' . 
            intval($options['max_tokens']) . '" min="1" max="4000">';
    $form .= '</td></tr>';
    
    // Temperature
    $form .= '<tr>';
    $form .= '<td class="head">' . _MB_LEGACY_BLOCK_AI_TEMP . '</td>';
    $form .= '<td class="even">';
    $form .= '<input type="number" name="options[2]" value="' . 
            floatval($options['temperature']) . '" min="0" max="1" step="0.1">';
    $form .= '</td></tr>';
    
    // Model selection - Updated to use Hugging Face models
    $form .= '<tr>';
    $form .= '<td class="head">' . _MB_LEGACY_BLOCK_AI_MODEL . '</td>';
    $form .= '<td class="even"><select name="options[3]">';
    $form .= '<option value="facebook/mbart-large-50-many-to-many-mmt"' . 
            ($options['model'] === 'facebook/mbart-large-50-many-to-many-mmt' ? ' selected' : '') . 
            '>mBART-50 (Translation)</option>';
    $form .= '<option value="facebook/bart-large-cnn"' . 
            ($options['model'] === 'facebook/bart-large-cnn' ? ' selected' : '') . 
            '>BART-CNN (Summarization)</option>';
    $form .= '</select></td></tr>';

    // Side (hidden)
    $form .= '<input type="hidden" name="options[4]" value="' . 
            intval($options['side']) . '">';
    
    $form .= '</table>';

    return $form;
}