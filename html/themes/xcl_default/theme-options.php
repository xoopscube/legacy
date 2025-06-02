<?php
/**
 * XCL Theme Default - Options
 * @package    XCL
 * @author     Nuno Luciano (aka Gigamaster)
 * @copyright  2005-2025 The XOOPSCube Project
 * @license    GPL v2.0
 * @version    v2.5.0 Release XCL
 * @link       https://github.com/xoopscube/xcl
 */

require_once dirname(__DIR__, 2) . '/mainfile.php';

global $xoopsSecurity, $xoopsUser, $xoopsConfig, $xoopsModule, $xoopsTpl, $root;

if (!is_object($xoopsUser) || !$xoopsUser->isAdmin()) {
    redirect_header(XOOPS_URL . '/', 3, _NOPERM);
    exit();
}

// TODO Language (optional)
$root->mLanguageManager->loadPageTypeMessageCatalog('global');

// Constants
define('OPTIONS_FILE', __DIR__ . '/theme-options.json');
define('HTML_BASE_PATH', XOOPS_ROOT_PATH);

$google_fonts = [
    'Roboto', 'Open Sans', 'Lato', 'Montserrat', 'Oswald', 'Source Sans Pro',
    'Raleway', 'Poppins', 'Nunito', 'Merriweather', 'Noto Sans', 'Ubuntu', 'Pacifico',
    'Playfair Display', 'PT Sans', 'Arimo', 'Dosis'
];
sort($google_fonts);

function get_pico_css_styles(): array {
    $styles = [];
    $base_css_path = HTML_BASE_PATH . '/common/picocss/';
    $sub_folders = ['classless', 'fluid-classless'];
    foreach ($sub_folders as $folder) {
        $dir_path = $base_css_path . $folder;
        if (is_dir($dir_path)) {
            $files = scandir($dir_path);
            foreach ($files as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) === 'css') {
                    $key = $folder . '/' . $file;
                    $display_name_folder = ucwords(str_replace('-', ' ', $folder));
                    $display_name_file = ucwords(str_replace(['.min', '.css', '-'], ['', '', ' '], $file));
                    $display_name = $display_name_folder . ' - ' . trim($display_name_file);
                    $styles[$key] = $display_name;
                }
            }
        }
    }
    asort($styles);
    return $styles;
}
$available_pico_styles = get_pico_css_styles();

$default_options = [
    'typography' => 'Nunito',
    'pico_style' => 'classless/pico.min.css',
    'site_title' => $xoopsConfig['sitename'] ?? 'XCL Theming Options',
    'site_slogan' => $xoopsConfig['slogan'] ?? 'Just another great website!',
    'text_color' => '',
    'primary_color' => '',
    'secondary_color' => '',
    'background_color' => '',
];

if (!empty($available_pico_styles) && !array_key_exists($default_options['pico_style'], $available_pico_styles)) {
    reset($available_pico_styles);
    $default_options['pico_style'] = key($available_pico_styles) ?: '';
} elseif (empty($available_pico_styles)) {
    $default_options['pico_style'] = '';
}

function load_theme_options(array $defaults): array {
    if (file_exists(OPTIONS_FILE)) {
        $json_data = file_get_contents(OPTIONS_FILE);
        $data_from_file = json_decode($json_data, true);

        // Check 'theme_options' key exists and is an array
        if (is_array($data_from_file) &&
            isset($data_from_file['theme_options']) &&
            is_array($data_from_file['theme_options']) &&
            !empty($data_from_file['theme_options']) &&
            isset($data_from_file['theme_options'][0]) &&
            is_array($data_from_file['theme_options'][0])) {

            // Merge defaults with the settings object
            return array_merge($defaults, $data_from_file['theme_options'][0]);
        }
    }
    return $defaults;
}



// Flat object into an array with all settings
// Use in theme.html: <{json file="$theme_name" theme="theme_options" local="true"}>
// <{foreach item=theme key=key from=$theme}>
// inside the loop: <{assign var='typography' value=$theme->typography}>
function save_theme_options(array $options_from_form): bool {
    global $default_options, $google_fonts, $available_pico_styles;
    $sanitized_options = [
        'typography'  => filter_var($options_from_form['typography'] ?? $default_options['typography'], FILTER_SANITIZE_SPECIAL_CHARS),
        'pico_style'  => filter_var($options_from_form['pico_style'] ?? $default_options['pico_style'], FILTER_SANITIZE_SPECIAL_CHARS),
        'site_title'  => filter_var($options_from_form['site_title'] ?? $default_options['site_title'], FILTER_SANITIZE_SPECIAL_CHARS),
        'site_slogan' => filter_var($options_from_form['site_slogan'] ?? $default_options['site_slogan'], FILTER_SANITIZE_SPECIAL_CHARS),
    ];

    foreach (['text_color', 'primary_color', 'secondary_color', 'background_color'] as $key_base) {
        $checkbox_form_key = $key_base . '_custom_toggle';
        $value_input_form_key = $key_base . '_custom_value';

        $is_custom_color_requested_by_user = isset($options_from_form[$checkbox_form_key]);

        if ($is_custom_color_requested_by_user) {
            $custom_value = filter_var($options_from_form[$value_input_form_key] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
            if (!preg_match('/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/', $custom_value)) {
                $custom_value = '';
            }
            $sanitized_options[$key_base] = $custom_value;
        } else {
            $sanitized_options[$key_base] = '';
        }
    }

    if (!in_array($sanitized_options['typography'], $google_fonts)) {
        $sanitized_options['typography'] = $default_options['typography'];
    }
    if (!empty($available_pico_styles) && !array_key_exists($sanitized_options['pico_style'], $available_pico_styles)) {
        $sanitized_options['pico_style'] = $default_options['pico_style'];
    } elseif (empty($available_pico_styles) && !empty($sanitized_options['pico_style'])) {
        $sanitized_options['pico_style'] = '';
    }

    // Wrap $sanitized_options in an array
    $data_to_save_to_json = ['theme_options' => [$sanitized_options]];
    $json_data = json_encode($data_to_save_to_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

    if ($json_data === false) { return false; }
    return file_put_contents(OPTIONS_FILE, $json_data) !== false;
}

$status_message = '';
$options = load_theme_options($default_options);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$xoopsSecurity->check()) {
        $status_message = '<div class="status-red">Security token error. Please refresh the page and try again.</div>';
    } else {
        $submitted_options = $_POST;
        if (save_theme_options($submitted_options)) {
            $status_message = '<div class="status-green">Theme options saved successfully!</div>';
            $options = load_theme_options($default_options);
        } else {
            $status_message = '<div class="status-red">Error saving theme options.</div>';
        }
    }
}

// Base URL for PicoCSS files
$pico_css_base_url = XOOPS_URL . '/common/picocss/';

// initial theme
$initial_pico_style_path = $options['pico_style'] ?? $default_options['pico_style'];
$initial_is_dark = false;
if (strpos($initial_pico_style_path, 'dark') !== false ||
    strpos($initial_pico_style_path, 'amber') !== false ||
    strpos($initial_pico_style_path, 'slate') !== false) {
    $initial_is_dark = true;
}

// HTML output is not processed by Smarty!
?>
<!DOCTYPE html>
<html lang="en" data-theme="<?php echo $initial_is_dark ? 'dark' : 'light'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>XCL Theming Options</title>
    <meta name="color-scheme" content="light dark">
    <link id="pico-style-link" rel="stylesheet" href="<?php echo htmlspecialchars($pico_css_base_url . ($options['pico_style'] ?? $default_options['pico_style'])); ?>">
    <link id="google-font-link" rel="stylesheet" href="">
    <style>
        :root {
            --head-font-family: '<?php echo htmlspecialchars($options['typography'], ENT_QUOTES); ?>', sans-serif;
        }
        html, body {
            font-family: var(--pico-font-family);
        }
        body > main {
            display: grid;
            grid-template-columns: 18rem 1fr;
            grid-template-areas: "menu main";
            column-gap: 2rem;
        }
        body > main > aside { grid-area: menu; padding: var(--pico-spacing); border-right: 1px solid var(--pico-muted-border-color); }
        body > main > section.preview-area { grid-area: main; padding: var(--pico-spacing); }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(0%, 1fr));
            grid-column-gap: var(--pico-grid-column-gap);
            grid-row-gap: var(--pico-grid-row-gap);
        }
        h1, h2, h3, h4 {
            font-family: var(--head-font-family);
        }
        .preview-area {
            border: 1px dashed var(--pico-muted-border-color);
            border-radius: var(--pico-border-radius);
        }
        body > main > aside,
        .form-group { 
            margin-bottom: 1rem; 
            padding-bottom: .5rem; 
            border-bottom: 1px solid var(--pico-muted-border-color); 
            & [role=button], [type=button], [type=file]::file-selector-button, 
            [type=reset], [type=submit], button, 
            input:not([type=checkbox],[type=radio],[type=range],[type=file]), 
            select, textarea {
                --pico-form-element-spacing-vertical: 0.25rem;
                --pico-form-element-spacing-horizontal: 0.5rem;
            }
        }
        .form-group:last-of-type { border-bottom: none; }
        .color-option-controls { 
            margin-bottom: 0.5rem; 
            & input[type="checkbox"] { 
                margin-right: 0.5rem; 
            }
        }
        .status-message { 
            & div {
                border-radius: var(--pico-border-radius);
                padding: 0.5rem; 
                text-align: center;
            }
            & .status-green {
                background: #00800040;
                border: 1px solid green; 
                color: #00aa00;
            }
            & .status-red {
                background: #ff000040;
                border: 1px solid red;
                color: #ff0000;
            }
        }
        /* Styles for preview elements */
        .preview-area #preview-title { color: var(--pico-primary); }
        .preview-area #preview-slogan { color: var(--pico-secondary); }
        .preview-area .preview-text { color: var(--pico-color); }
        .preview-area { background-color: var(--pico-background-color); }
        .preview-area .preview-button {
            color: var(--pico-primary-inverse);
            background-color: var(--pico-primary);
            border-color: var(--pico-primary);
        }
        .preview-area .preview-button-secondary {
            color: var(--pico-secondary-inverse);
            background-color: var(--pico-secondary);
            border-color: var(--pico-secondary);
        }
        .credits {font-size: 0.7rem;}
    </style>
</head>
<body>
<header class="grid">
    <div><h1>XCL Theme Options</h1></div>
    <div>
        <?php if ($status_message): ?>
        <div class="status-message" role="alert"><?php echo $status_message; ?></div>
        <?php endif; ?>
    </div>
</header>

<main class="container">
    <aside id="nav-left">
    <nav>
        <details class="dropdown">
            <summary role="button" class="contrast">Theme Mode</summary>
            <ul>
                <li><a href="#" data-theme-switcher="auto">Auto</a></li>
                <li><a href="#" data-theme-switcher="light">Light</a></li>
                <li><a href="#" data-theme-switcher="dark">Dark</a></li>
            </ul>
        </details>
    </nav>
<hr>
    <form method="POST" action="" id="theme-options-form">
    <?php echo $GLOBALS['xoopsSecurity']->getTokenHTML(); ?>
            
        <div class="form-group">
            <label for="pico_style">Base PicoCSS Style</label>
            <select id="pico_style" name="pico_style">
                <?php foreach ($available_pico_styles as $path => $name): ?>
                    <option value="<?php echo htmlspecialchars($path); ?>" <?php selected($options['pico_style'], $path); ?>>
                        <?php echo htmlspecialchars($name); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="typography">Heading Typography (Google Font)</label>
            <select id="typography" name="typography">
                <?php foreach ($google_fonts as $font): ?>
                    <option value="<?php echo htmlspecialchars($font); ?>" <?php selected($options['typography'], $font); ?>>
                        <?php echo htmlspecialchars($font); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <?php
        $color_options_setup = [
            'background_color' => ['label' => 'Background Color', 'css_var' => '--pico-background-color'],
            'text_color' => ['label' => 'Text Color', 'css_var' => '--pico-color'],
            'primary_color' => ['label' => 'Primary Color', 'css_var' => '--pico-primary'],
            'secondary_color' => ['label' => 'Secondary Color', 'css_var' => '--pico-secondary'],
        ];

        foreach ($color_options_setup as $key_base => $config):
            $current_color_value = $options[$key_base] ?? '';
            $is_currently_custom_set = ($current_color_value !== '');
        ?>
        <div class="form-group">
            <label for="<?php echo $key_base; ?>_picker"><?php echo $config['label']; ?></label>
            <div class="color-option-controls">
                <input type="checkbox" id="<?php echo $key_base; ?>_custom_toggle"
                        name="<?php echo $key_base; ?>_custom_toggle"
                        data-target-picker="<?php echo $key_base; ?>_picker"
                        data-target-text="<?php echo $key_base; ?>_text"
                        data-target-hidden-value="<?php echo $key_base; ?>_custom_value"
                        data-css-var="<?php echo $config['css_var']; ?>"
                        data-default-hex="" 
                        <?php if ($is_currently_custom_set) echo 'checked'; ?>>
                <label for="<?php echo $key_base; ?>_custom_toggle" class="inline-label">Set Custom Color</label>
            </div>
            <div class="color-input-group">
                <input type="color" id="<?php echo $key_base; ?>_picker" value="<?php echo htmlspecialchars($current_color_value); ?>" <?php if (!$is_currently_custom_set) echo 'disabled'; ?>>
                <input type="text" id="<?php echo $key_base; ?>_text" value="<?php echo htmlspecialchars($current_color_value); ?>" <?php if (!$is_currently_custom_set) echo 'disabled'; ?>>
            </div>
            <input type="hidden" name="<?php echo $key_base; ?>_custom_value" id="<?php echo $key_base; ?>_custom_value" value="<?php echo htmlspecialchars($current_color_value); ?>">
        </div>
        <?php endforeach; ?>

        <div class="form-group">
            <label for="site_title">Site Title (for Preview)</label>
            <input type="text" id="site_title" name="site_title" value="<?php echo htmlspecialchars($options['site_title']); ?>">
        </div>
        <div class="form-group">
            <label for="site_slogan">Site Slogan (for Preview)</label>
            <input type="text" id="site_slogan" name="site_slogan" value="<?php echo htmlspecialchars($options['site_slogan']); ?>">
        </div>

        <button type="submit">Save Options</button>
        </form>
    </div>
    </aside>
    <section class="preview-area">
        <article>
            <header>
                <hgroup>
                    <h2 id="preview-title"><?php echo htmlspecialchars($options['site_title']); ?></h2>
                    <p id="preview-slogan"><?php echo htmlspecialchars($options['site_slogan']); ?></p>
                </hgroup>
            </header>
            <p class="preview-text">This is sample text. The quick brown fox jumps over the lazy dog. Check readability against the background.</p>
            <footer>
                <a href="#" role="button" class="preview-button">Primary</a>
                <a href="#" role="button" class="secondary preview-button-secondary">Secondary</a>
            </footer>
        </article>

            <!-- Tables -->
    <article id="tables">
      <h2>Tables</h2>
      <div class="overflow-auto">
        <table class="striped">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">Heading</th>
              <th scope="col">Heading</th>
              <th scope="col">Heading</th>
              <th scope="col">Heading</th>
              <th scope="col">Heading</th>
              <th scope="col">Heading</th>
              <th scope="col">Heading</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <th scope="row">1</th>
              <td>Cell</td>
              <td>Cell</td>
              <td>Cell</td>
              <td>Cell</td>
              <td>Cell</td>
              <td>Cell</td>
              <td>Cell</td>
            </tr>
            <tr>
              <th scope="row">2</th>
              <td>Cell</td>
              <td>Cell</td>
              <td>Cell</td>
              <td>Cell</td>
              <td>Cell</td>
              <td>Cell</td>
              <td>Cell</td>
            </tr>
            <tr>
              <th scope="row">3</th>
              <td>Cell</td>
              <td>Cell</td>
              <td>Cell</td>
              <td>Cell</td>
              <td>Cell</td>
              <td>Cell</td>
              <td>Cell</td>
            </tr>
          </tbody>
        </table>
      </div>
    </article>

    <!-- Typography-->
    <article id="typography">
      <h2>Typography</h2>
      <p>
        Aliquam lobortis vitae nibh nec rhoncus. Morbi mattis neque eget efficitur feugiat.
        Vivamus porta nunc a erat mattis, mattis feugiat turpis pretium. Quisque sed tristique
        felis.
      </p>

      <!-- Blockquote-->
      <blockquote>
        "Maecenas vehicula metus tellus, vitae congue turpis hendrerit non. Nam at dui sit amet
        ipsum cursus ornare."
        <footer>
          <cite>- Phasellus eget lacinia</cite>
        </footer>
      </blockquote>

      <!-- Lists-->
      <h3>Lists</h3>
      <ul>
        <li>Aliquam lobortis lacus eu libero ornare facilisis.</li>
        <li>Nam et magna at libero scelerisque egestas.</li>
        <li>Suspendisse id nisl ut leo finibus vehicula quis eu ex.</li>
        <li>Proin ultricies turpis et volutpat vehicula.</li>
      </ul>

      <!-- Inline text elements-->
      <h3>Inline text elements</h3>
      <div class="grid">
        <p><a href="#" onclick="event.preventDefault()">Primary link</a></p>
        <p>
          <a href="#" class="secondary" onclick="event.preventDefault()">Secondary link</a>
        </p>
        <p>
          <a href="#" class="contrast" onclick="event.preventDefault()">Contrast link</a>
        </p>
      </div>
      <div class="grid">
        <p><strong>Bold</strong></p>
        <p><em>Italic</em></p>
        <p><u>Underline</u></p>
      </div>
      <div class="grid">
        <p><del>Deleted</del></p>
        <p><ins>Inserted</ins></p>
        <p><s>Strikethrough</s></p>
      </div>
      <div class="grid">
        <p><small>Small </small></p>
        <p>Text <sub>Sub</sub></p>
        <p>Text <sup>Sup</sup></p>
      </div>
      <div class="grid">
        <p>
          <abbr title="Abbreviation" data-tooltip="Abbreviation">Abbr.</abbr>
        </p>
        <p><kbd>Kbd</kbd></p>
        <p><mark>Highlighted</mark></p>
      </div>

      <!-- Headings-->
      <h3>Heading 3</h3>
      <p>
        Integer bibendum malesuada libero vel eleifend. Fusce iaculis turpis ipsum, at efficitur
        sem scelerisque vel. Aliquam auctor diam ut purus cursus fringilla. Class aptent taciti
        sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.
      </p>
      <h4>Heading 4</h4>
      <p>
        Cras fermentum velit vitae auctor aliquet. Nunc non congue urna, at blandit nibh. Donec ac
        fermentum felis. Vivamus tincidunt arcu ut lacus hendrerit, eget mattis dui finibus.
      </p>
      <h5>Heading 5</h5>
      <p>
        Donec nec egestas nulla. Sed varius placerat felis eu suscipit. Mauris maximus ante in
        consequat luctus. Morbi euismod sagittis efficitur. Aenean non eros orci. Vivamus ut diam
        sem.
      </p>
      <h6>Heading 6</h6>
      <p>
        Ut sed quam non mauris placerat consequat vitae id risus. Vestibulum tincidunt nulla ut
        tortor posuere, vitae malesuada tortor molestie. Sed nec interdum dolor. Vestibulum id
        auctor nisi, a efficitur sem. Aliquam sollicitudin efficitur turpis, sollicitudin
        hendrerit ligula semper id. Nunc risus felis, egestas eu tristique eget, convallis in
        velit.
      </p>

      <!-- Medias-->
      <figure>
          <svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%">
              <path fill="#817c70" d="M0 0h1024v1024H0z" />
              <g fill-opacity=".502">
                  <path fill="#03020f" d="M178 994l580 92L402-62" />
                  <path fill="#f2e2ba" d="M638 894L614 6l472 340" />
                  <path fill="#11181e" d="M-62 854h300L138-62" />
                  <path fill="#0b4041" d="M410-62L154 530-62 38" />
                  <path fill="#62b4cf" d="M1086-2L498-30l484 408" />
                  <path fill="#010412" d="M430-2l196 52-76 306" />
                  <path fill="#eb7d3f" d="M598 594l488-32-308 500" />
              </g>
              <text fill="#d7d7d2" font-family="sans-serif" font-size="100%" dy="2" font-weight="bold" x="45%" y="70%" text-anchor="middle">Legacy to Remind Us of Where We Come From</text>
          </svg>
        <figcaption>
          Image from
          <a href="#!" target="_blank">SVG Image</a>
        </figcaption>
      </figure>
    </article>

    </section>
</main>

<script>
    // PicoCSS Theme Switcher 
    const themeSwitcher = {
        _scheme: "auto", _userPreference: "auto", menuTarget: "details.dropdown",
        buttonsTarget: "a[data-theme-switcher]", buttonAttribute: "data-theme-switcher",
        rootAttribute: "data-theme", localStorageKey: "picoPreferredColorScheme",
        init() { this._userPreference = this.schemeFromLocalStorage; this.setScheme(this._userPreference, false); this.initSwitchers(); },
        get schemeFromLocalStorage() { return window.localStorage?.getItem(this.localStorageKey) ?? "auto"; },
        get preferredColorScheme() { return window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light"; },
        initSwitchers() {
            document.querySelectorAll(this.buttonsTarget).forEach(btn => btn.addEventListener("click", e => {
                e.preventDefault(); const pref = btn.getAttribute(this.buttonAttribute);
                this.setScheme(pref, true);
                document.querySelector(this.menuTarget)?.removeAttribute("open");
            }, false));
        },
        setScheme(candidate, savePref = true) {
            let resolved = candidate;
            if (candidate === "auto") resolved = this.preferredColorScheme;
            else if (candidate !== "dark" && candidate !== "light") resolved = this.preferredColorScheme;
            if (this._scheme !== resolved) { this._scheme = resolved; this.applyScheme(); }
            if (savePref && (candidate === "auto" || candidate === "light" || candidate === "dark")) {
                this._userPreference = candidate; this.schemeToLocalStorage(candidate);
            }
        },
        get scheme() { return this._scheme; },
        applyScheme() { document.documentElement.setAttribute(this.rootAttribute, this._scheme); },
        schemeToLocalStorage(pref) { window.localStorage?.setItem(this.localStorageKey, pref); },
        suggestScheme(suggested) { // 'light' or 'dark'
            if (this._userPreference === "auto" && (suggested === "light" || suggested === "dark")) {
                if (this._scheme !== suggested) { this._scheme = suggested; this.applyScheme(); }
            }
        }
    };
    themeSwitcher.init();

    document.addEventListener('DOMContentLoaded', function() {
        const root = document.documentElement; // <html>
        const picoStyleSelect = document.getElementById('pico_style');
        const typographySelect = document.getElementById('typography');
        const siteTitleInput = document.getElementById('site_title');
        const siteSloganInput = document.getElementById('site_slogan');
        const previewTitle = document.getElementById('preview-title');
        const previewSlogan = document.getElementById('preview-slogan');
        const previewArea = document.querySelector('.preview-area');
        let googleFontLink = document.getElementById('google-font-link');
        let picoStyleLink = document.getElementById('pico-style-link');
        const picoCssBaseUrl = '<?php echo $pico_css_base_url; ?>';
        const statusMessageDiv = document.querySelector('header .status-message');

        // status message on form interaction
        if (statusMessageDiv && statusMessageDiv.innerHTML.trim() !== '') {
            const formElementsToMonitor = document.querySelectorAll(
                '#theme-options-form select, #theme-options-form input:not([type="hidden"]):not([type="submit"])'
            );

            const hideStatusMessageOnInteraction = () => {
                if (statusMessageDiv) {
                    statusMessageDiv.style.display = 'none';
                }
            };

            formElementsToMonitor.forEach(el => {
                const eventType = (el.tagName.toLowerCase() === 'select' || el.type === 'checkbox' || el.type === 'radio') 
                                    ? 'change' 
                                    : 'input';
                el.addEventListener(eventType, hideStatusMessageOnInteraction, { once: true });
            });
        }

        function loadGoogleFont(fontName) {
            if (!fontName) return;
            const formattedFontName = fontName.replace(/ /g, '+');
            const fontUrl = `https://fonts.googleapis.com/css2?family=${formattedFontName}:wght@400;700&display=swap`;
            if (!googleFontLink) {
                googleFontLink = document.createElement('link');
                googleFontLink.id = 'google-font-link';
                googleFontLink.rel = 'stylesheet';
                document.head.appendChild(googleFontLink);
            }
            googleFontLink.href = fontUrl;
            // Apply font globally
            root.style.setProperty('--head-font-family', `'${fontName}', sans-serif`);
            // Apply to preview area
            if (previewArea) previewArea.style.setProperty('--head-font-family', `'${fontName}', sans-serif`);
        }

        function setupColorOption(keyBase) {
            const checkbox = document.getElementById(`${keyBase}_custom_toggle`);
            const picker = document.getElementById(`${keyBase}_picker`);
            const textInput = document.getElementById(`${keyBase}_text`);
            const hiddenValueInput = document.getElementById(keyBase + '_custom_value');
            const cssVarName = checkbox.dataset.cssVar;
            const defaultHex = checkbox.dataset.defaultHex;

            function applyCustomColorToPreview(colorValue) {
                if (previewArea) previewArea.style.setProperty(cssVarName, colorValue);
                if (cssVarName === '--pico-background-color' && previewArea) {
                }
            }

            function removeCustomColorFromPreview() {
                if (previewArea) previewArea.style.removeProperty(cssVarName);
                    if (cssVarName === '--pico-background-color' && previewArea) {
                    }
            }

            picker.addEventListener('input', () => {
                textInput.value = picker.value;
                hiddenValueInput.value = picker.value;
                if (checkbox.checked) applyCustomColorToPreview(picker.value);
            });
            textInput.addEventListener('input', () => {
                if (/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/.test(textInput.value)) {
                    picker.value = textInput.value;
                    hiddenValueInput.value = textInput.value;
                    if (checkbox.checked) applyCustomColorToPreview(textInput.value);
                }
            });

            checkbox.addEventListener('change', () => {
                const isChecked = checkbox.checked;
                picker.disabled = !isChecked;
                textInput.disabled = !isChecked;
                if (isChecked) {
                    // is valid hex
                    if (!/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/.test(textInput.value)) {
                        textInput.value = defaultHex;
                        picker.value = defaultHex;
                    }
                    hiddenValueInput.value = textInput.value;
                    applyCustomColorToPreview(textInput.value);
                } else {
                    removeCustomColorFromPreview();
                }
            });

            // Initial state for preview area
            if (checkbox.checked) {
                applyCustomColorToPreview(hiddenValueInput.value);
            } else {
                removeCustomColorFromPreview();
            }
        }

        function updatePicoStyle(relativePath) {
            if (picoStyleLink && relativePath) {
                picoStyleLink.href = picoCssBaseUrl + relativePath;
            } else if (picoStyleLink) {
                picoStyleLink.href = "";
            }

            const isNewPicoFileDark = relativePath.toLowerCase().includes('dark') ||
                                        relativePath.toLowerCase().includes('amber') ||
                                        relativePath.toLowerCase().includes('slate');
            themeSwitcher.suggestScheme(isNewPicoFileDark ? "dark" : "light");

            // removeCustomColorFromPreview if checkbox is unchecked.
            // If checked, custom color remains on preview.
            ['background_color', 'text_color', 'primary_color', 'secondary_color'].forEach(keyBase => {
                const checkbox = document.getElementById(`${keyBase}_custom_toggle`);
                if (checkbox && !checkbox.checked) {
                    if (previewArea) previewArea.style.removeProperty(checkbox.dataset.cssVar);
                }
            });
        }

        // Initial Setup
        loadGoogleFont(typographySelect.value);
        setupColorOption('background_color');
        setupColorOption('text_color');
        setupColorOption('primary_color');
        setupColorOption('secondary_color');
        
        const initialPicoStyle = picoStyleSelect.value;
        updatePicoStyle(initialPicoStyle); // Load initial Pico theme

        // Event Listeners
        picoStyleSelect.addEventListener('change', (event) => updatePicoStyle(event.target.value));
        typographySelect.addEventListener('change', (event) => loadGoogleFont(event.target.value));
        siteTitleInput.addEventListener('input', (event) => { if(previewTitle) previewTitle.textContent = event.target.value; });
        siteSloganInput.addEventListener('input', (event) => { if(previewSlogan) previewSlogan.textContent = event.target.value; });
    });
</script>
<?php
    function selected($current_value, $option_value) {
        if ($current_value === $option_value) {
            echo ' selected';
        }
    }
?>
</body>
</html>