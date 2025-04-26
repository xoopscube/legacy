<?php
/**
 * Recovery for XOOPSCube XCL
 *
 * @package    XCL
 * @version    XCL 2.5.0
 * @author     Nuno Luciano aka gigamaster
 * @copyright  (c) 2005-2025 The XOOPSCube Project
 * @license    GPL 2.0
 */

// XOOPSCube XCL Language File Audit Tool
// Scans all modules in XOOPS_ROOT_PATH and XOOPS_TRUST_PATH for language files
// Compares all constants in /english/*.php to those in each other language folder
// Outputs an HTML report with emoji for success/fail
// Now also reports extra constants
// (defined in translation but not in English, or not used anywhere on the code)

// Polyfill for PHP < 8 str_contains
if (!function_exists('str_contains')) {
    function str_contains($haystack, $needle) {
        return $needle !== '' && mb_strpos($haystack, $needle) !== false;
    }
}

// Emergency language reset to handler (must be at top, before any output)
if (isset($_GET['resetlang']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    require_once dirname(__FILE__) . '/mainfile.php';
    $db = XoopsDatabaseFactory::getDatabaseConnection();
    $table = XOOPS_DB_PREFIX . '_config';
    $sql = "UPDATE `$table` SET conf_value='english' WHERE conf_name='language' AND conf_modid=0";
    $ok = $db->queryF($sql);
    if ($ok) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'DB update failed']);
    }
    exit;
}

require_once __DIR__ . '/mainfile.php';

// Only enforce admin session for normal web requests
global $xoopsUser;
if (!is_object($xoopsUser) || !in_array(1, $xoopsUser->getGroups())) {
    header('HTTP/1.1 403 Forbidden');
    echo '<h1>403 Forbidden</h1><p>Admin access only.</p>';
    exit();
}

// Extras for D3 modules prefix
function extract_defines($file, $mydirname = null) {
    $defines = [];
    if (!file_exists($file)) return $defines;
    $content = file_get_contents($file);
    // D3 module support: detect $constpref assignment
    $constpref = null;
    if (preg_match('/\$constpref\s*=\s*([\'\"])?(_MB_|_MI_|_MD_)?\.'
        . '\s*strtoupper\s*\(\s*\$mydirname\s*\)\s*([\'\"])?/i', $content)) {
        // If $mydirname is not set, try to infer from path
        if (!$mydirname) {
            // e.g. .../modules/d3forum/language/...
            if (preg_match('#modules/([^/]+)/language/#', str_replace('\\','/',$file), $m)) {
                $mydirname = $m[1];
            }
        }
        if ($mydirname) {
            // Try to infer prefix (e.g. _MB_D3FORUM_)
            if (preg_match('/\$constpref\s*=\s*([\'\"])?(_MB_|_MI_|_MD_)?\.'
                . '\s*strtoupper\s*\(\s*\$mydirname\s*\)\s*([\'\"])?/i', $content, $pm)) {
                $constpref = ($pm[2] ?? '') . strtoupper($mydirname) . '_';
            }
        }
    }
    // Match define($constpref . '_XYZ', ...)
    if ($constpref && preg_match_all('/define\s*\(\s*\$constpref\s*\.\s*[\'\"](_[A-Z0-9]+)[\'\"]\s*,/', $content, $matches)) {
        foreach ($matches[1] as $suffix) {
            $defines[] = $constpref . ltrim($suffix, '_');
        }
    }
    // Also match normal define('CONSTANT', ...)
    if (preg_match_all("/define\\s*\\(\\s*['\"]([^'\"]+)['\"]\\s*,/", $content, $matches2)) {
        foreach ($matches2[1] as $c) {
            // Avoid duplicates
            if (!in_array($c, $defines)) $defines[] = $c;
        }
    }
    return $defines;
}

// Get all used constants in codebase (for extra constant reporting)
function get_all_used_constants($root_dirs) {
    $used = [];
    foreach ($root_dirs as $dir) {
        $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
        foreach ($rii as $file) {
            if ($file->isFile() && strtolower($file->getExtension()) === 'php') {
                $content = file_get_contents($file->getPathname());
                // Match both constant('_CONST') and _CONST direct usage
                if (preg_match_all("/(['\"])(_[_A-Z0-9]+)\\1|\b(_[_A-Z0-9]+)\b/", $content, $matches)) {
                    foreach (array_merge($matches[2], $matches[3]) as $const) {
                        if ($const) $used[$const] = true;
                    }
                }
            }
        }
    }
    return $used;
}

$roots = [
    __DIR__ . '/modules',
    dirname(__DIR__) . '/xoops_trust_path/modules'
];
$code_roots = [
    __DIR__,
    dirname(__DIR__) . '/xoops_trust_path'
];
$used_constants = get_all_used_constants($code_roots);

$all_reports = [];

foreach ($roots as $base) {
    foreach (glob($base . '/*', GLOB_ONLYDIR) as $moddir) {
        $module = basename($moddir);
        $langbase = $moddir . '/language';
        if (!is_dir($langbase)) continue;
        $languages = [];
        foreach (glob($langbase . '/*', GLOB_ONLYDIR) as $langdir) {
            $langcode = basename($langdir);
            $languages[$langcode] = true;
        }
        $englishdir = $langbase . '/english';
        if (!is_dir($englishdir)) continue;
        foreach (glob($englishdir . '/*.php') as $efile) {
            $fname = basename($efile);
            $edefs = extract_defines($efile);
            foreach ($languages as $langcode => $_) {
                if ($langcode == 'english') continue;
                $tfile = $langbase . '/' . $langcode . '/' . $fname;

// --- Add Debugging Here ---
//     if ($langcode == 'pt_utf8' && $fname == 'admin.php') { // Optional: Only print for the specific file
//      error_log("Checking for file: " . $tfile); // Log to PHP error log
       // Or use echo for direct output (might clutter the page):
//       echo "Checking for file: " . $tfile . "<br>";
//       echo "File exists check: " . (file_exists($tfile) ? 'TRUE' : 'FALSE') . "<br>";
//       echo "File is readable check: " . (is_readable($tfile) ? 'TRUE' : 'FALSE') . "<br>";
//  }
 // --- End Debugging ---

                $tdefs = extract_defines($tfile);
                $missing = array_diff($edefs, $tdefs);
                $extra   = array_diff($tdefs, $edefs);
                $extra_unused = array_filter($extra, function($const) use ($used_constants) {
                    return !isset($used_constants[$const]);
                });
                $all_reports[] = [
                    'module' => $module,
                    'lang' => $langcode,
                    'file' => $fname,
                    'missing' => $missing,
                    'extra' => $extra,
                    'extra_unused' => $extra_unused,
                    'ok' => empty($missing)
                ];
            }
        }
    }
}

// Collect unique modules and languages
$modules = [];
$languages = [];
foreach ($all_reports as $r) {
    $modules[$r['module']] = true;
    $languages[$r['lang']] = true;
}
ksort($modules); ksort($languages);

// --- END PHP PROCESSING SECTION ---

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html
<head>
    <title>XOOPSCube XCL - Recovery Language Audit</title>
    <meta charset="UTF-8">
    <!-- CSS STYLES SECTION -->
    <style>
    *::before,
*::after {
  box-sizing: border-box;
}
    body{font:14px sans-serif;} 
    .ok{color:green;}
    .fail{color:red;} 
    ul{margin:0 0 1em 2em;} 
    li{margin:0.2em 0;}
    section {max-width: 1024px; margin:0 auto 2rem;}
    .audit-header{background:#f5f5f5;padding:1em 2em 1em 2em;border-bottom:1px solid #ccc;}
    .audit-btn{background:#0074d9;color:#fff;border:none;padding: 0.65em;margin: 1em 0 0;margin-top:1em;font-size:1.1em;border-radius:3px;cursor:pointer;}
    .audit-btn:active{background:#005fa3;}

    /* Layout Styles */
    .audit-result-wrap {
        margin: 1em auto 0 auto;
        max-width: 1100px;
        background: #fff;
        position: relative;
    }

    .audit-controls {
        position: sticky;
        top: 0;
        z-index: 10;
        background-color: #f8f8f8;
        padding: 1em 1.5em;
        border-bottom: 1px solid #ddd;
        margin: 0 auto 0 auto;
        max-width: 1100px;
        box-sizing: border-box;
    }

    #audit-results-inner {
        height: 480px;
        overflow-y: auto;
        padding: 1.5em 2em;
        border: 1px solid #eee;
        margin-top: 1em;
    }

    /* Button Styles */
    .audit-export-btn{
        background: #2abf3b;
        color: #fff;
        border: none;
        padding: 0.65em;
        margin: 1em 0 0;
        font-size: 1em;
        border-radius: 3px;
        cursor: pointer;
    }
    .audit-export-btn:active{
        background:#229c2b;
    }
    input, select {
        max-width: 200px;
        position: relative;
    }
    input, select {
        appearance: none;
        width: 100%;
        font-size: 1rem;
        padding: 0.5em;
        background-color: #fff;
        border: 1px solid #caced1;
        border-radius: 0.25rem;
        color: #111;
        cursor: pointer;
    }
    select {
        background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24'%3E%3Cpath fill='%23000' d='m13 16.172l5.364-5.364l1.414 1.414L12 20l-7.778-7.778l1.414-1.414L11 16.172V4h2z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-size: 0.8em auto;
        background-position: right 5% bottom 50%;
    }
    </style>
</head>
<body>
    <!-- HTML CONTENT SECTION -->
    <section>

    <div class="audit-header">
        <h1>XOOPSCube XCL Language Audit Tool</h1>
        <p>This tool scans all modules for language files and compares constants between English and other languages.</p>
        <button id="runbtn" class="audit-btn" onclick="runAudit()">Start Audit</button>
        <button id="newauditbtn" class="audit-btn" onclick="newAudit()" style="display:none;background:#ff851b;">New Audit</button>
        <div id="audit-loading" style="display:none;margin-top:1em;">
            <p>Analyzing language files... please wait.</p>
        </div>
    </div>

    <!-- Controls Section -->
    <div id="audit-controls-section" class="audit-controls" style="display:none;">
        <div style="display:flex;flex-wrap:wrap;gap:1em;align-items:center;">
            <div style="flex:1;">
                <label for="audit-module"><b>Filter by Module:</b></label>
                <select id="audit-module" onchange="filterReports()" style="width:100%;">
                    <option value="">All Modules</option>
                    <?php foreach (array_keys($modules) as $m): ?>
                    <option value="<?php echo htmlspecialchars($m); ?>"><?php echo htmlspecialchars($m); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div style="flex:1;">
                <label for="audit-lang"><b>Filter by Language:</b></label>
                <select id="audit-lang" onchange="filterReports()" style="width:100%;">
                    <option value="">All Languages</option>
                    <?php foreach (array_keys($languages) as $l): ?>
                    <option value="<?php echo htmlspecialchars($l); ?>"><?php echo htmlspecialchars($l); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div style="flex:1;">
                <label for="audit-file"><b>Filter by Filename:</b></label>
                <input type="text" id="audit-file" onkeyup="filterReports()" placeholder="e.g. admin.php" style="width:100%;">
            </div>
            <div style="flex:1;">
                <label for="audit-export-select"><b>Export:</b></label>
                <select id="audit-export-select" style="min-width:150px;">
                    <option value="">Export Options</option>
                    <option value="view-html">View as HTML</option>
                    <option value="download-html">Download HTML</option>
                    <option value="download-csv">Download CSV</option>
                    <option value="download-json">Download JSON</option>
                </select>
            </div>
            <div><button id="audit-send-email-btn" class="audit-export-btn">Send Report via Email</button></div>
        </div>
<!-- // --- Email Modal (can stay here, it's position:fixed) -->

<div id="audit-email-modal" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;">
  <div style="background:firebrick;color:#fff;padding:2em 2.5em;border-radius:8px;max-width:350px;margin:auto;box-shadow:0 4px 18px #0002;">
    <h>Send Report via Email</h3>
    <form id="audit-email-form" onsubmit="return false;">
      <label>Email: <input type="email" id="audit-email-to" required style="width:100%;margin-bottom:1em;"></label>
      <div style="text-align:right;">
        <button type="button" onclick="closeAuditEmailModal()" style="margin-right:0.7em;">Cancel</button>
        <button type="submit">Send</button>
      </div>
      <div id="audit-email-status" style="color:#0074d9;margin-top:0.6em;"></div>
    </form>
  </div>
</div>
</div><!-- // End of audit-controls -->

        <!-- Summary container -->
        <div id="audit-summary-container"></div>
    </div>

    <!-- Results Section -->
    <div id="audit-results" class="audit-result-wrap" style="display:none;">
        <div id="audit-results-inner"></div>
    </div>

    <!-- JAVASCRIPT SECTION -->
    <script>
    // All JavaScript functions grouped together
    let allReports = <?php echo json_encode($all_reports, JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_UNESCAPED_UNICODE); ?>;
    
    // Filter reports based on selected criteria
    function filterReports() {
        let mod = document.getElementById("audit-module").value;
        let lang = document.getElementById("audit-lang").value;
        let fileFilter = document.getElementById("audit-file").value.toLowerCase();

        let results = allReports.filter(r =>
            (mod === "" || r.module === mod) &&
            (lang === "" || r.lang === lang) &&
            (fileFilter === "" || r.file.toLowerCase().includes(fileFilter))
        );
        renderReports(results);
    }

    // Render the filtered reports
    function renderReports(reports) {
        // Summary statistics
        let totalModules = new Set(reports.map(r => r.module)).size;
        let totalLangs = new Set(reports.map(r => r.lang)).size;
        let totalFiles = reports.length;
        let totalMissing = reports.reduce((sum, r) => sum + (r.missing ? r.missing.length : 0), 0);
        let totalExtra = reports.reduce((sum, r) => sum + (r.extra ? r.extra.length : 0), 0);
        let totalUnused = reports.reduce((sum, r) => sum + (r.extra_unused ? r.extra_unused.length : 0), 0);
        let totalOk = reports.filter(r => r.ok).length;
        
        // Generate summary HTML
        let summaryHtml = `<div id="action-control" style="border:1px solid #eee; background:#f8f8f8; padding:1em 1.5em; margin-bottom:1.2em; border-radius:4px;">
            <b>Summary:</b>
            <span title='Modules'><b>🧩 ${totalModules}</b> modules</span> &nbsp;
            <span title='Languages'><b>🌐 ${totalLangs}</b> languages</span> &nbsp;
            <span title='Files'><b>📄 ${totalFiles}</b> files</span> &nbsp;
            <span title='OK'><b>✅ ${totalOk}</b> all present</span> &nbsp;
            <span title='Missing'><b>❌ ${totalMissing}</b> missing</span> &nbsp;
            <span title='Extra'><b>⚠️ ${totalExtra}</b> extra</span> &nbsp;
            <span title='Unused'><b>🚫 ${totalUnused}</b> unused</span>
        </div>`;
        
        // Update summary container
        document.getElementById("audit-summary-container").innerHTML = summaryHtml;
        
        // Generate detailed report HTML
        let reportHtml = "";
        for (let report of reports) {
            let icon = report.ok ? "✅" : "❌";
            reportHtml += `<h3>${icon} Module: <b>${report.module}</b> &mdash; Language: <b>${report.lang}</b> &mdash; File: <b>${report.file}</b></h3>`;
            if (report.ok) {
                reportHtml += `<div class="ok">All constants present.</div>`;
            } else {
                // Check for missing constants
                if (report.missing && report.missing.length) {
                    reportHtml += `<div class="fail">Missing constants:<ul>`;
                    for (let c of report.missing) reportHtml += `<li>❌ <code>${c}</code></li>`;
                    reportHtml += `</ul></div>`;
                }
                
                // Check for extra constants
                if (report.extra && report.extra.length) {
                    reportHtml += `<div>Extra constants:<ul>`;
                    for (let c of report.extra) {
                        let warn = (report.extra_unused && report.extra_unused.includes(c)) ? " <span style='color:red'>🚫 Not used in code</span>" : "";
                        reportHtml += `<li>⚠️ <code>${c}</code>${warn}</li>`;
                    }
                    reportHtml += `</ul></div>`;
                }
            }
        }
        
        // Add legend
        reportHtml += `<hr><div>✅ = All constants present<br>❌ = Missing constant<br>⚠️ = Extra constant in translation<br>🚫 = Extra constant not used anywhere in codebase</div>`;
        
        // Update results container
        document.getElementById("audit-results-inner").innerHTML = reportHtml;
    }

    // Start the audit process
    function runAudit() {
        const runBtn = document.getElementById("runbtn");
        if (runBtn) runBtn.disabled = true;
        document.getElementById("audit-loading").style.display = "block";
        if (runBtn) runBtn.style.display = "none";

        setTimeout(() => {
            filterReports();
            document.getElementById("audit-controls-section").style.display = "block";
            document.getElementById("audit-results").style.display = "block";
            document.getElementById("audit-loading").style.display = "none";
            document.getElementById("newauditbtn").style.display = "inline-block";
        }, 250);
    }

    // Reset for a new audit
    function newAudit() {
        const runBtn = document.getElementById("runbtn");
        if (runBtn) runBtn.disabled = false;
        document.getElementById("audit-controls-section").style.display = "none";
        document.getElementById("audit-results").style.display = "none";
        document.getElementById("audit-loading").style.display = "none";
        document.getElementById("audit-results-inner").innerHTML = "";
        
        // Clear filters and localStorage
        localStorage.removeItem('auditModule');
        localStorage.removeItem('auditLang');
        localStorage.removeItem('auditFile');
        
        // Reset filter fields
        document.getElementById("audit-module").value = "";
        document.getElementById("audit-lang").value = "";
        document.getElementById("audit-file").value = "";

        document.getElementById("newauditbtn").style.display = "none";
        if (runBtn) runBtn.style.display = "inline-block";
    }


  // Email modal logic.
  // Note that email depends on the context settings
  function openAuditEmailModal() {
      document.getElementById('audit-email-modal').style.display = 'flex';
      document.getElementById('audit-email-to').focus();
      document.getElementById('audit-email-status').textContent = '';
  }
  function closeAuditEmailModal() {
      document.getElementById('audit-email-modal').style.display = 'none';
  }
  window.addEventListener('DOMContentLoaded', function() {
      var sendBtn = document.getElementById('audit-send-email-btn');
      if (sendBtn) sendBtn.addEventListener('click', openAuditEmailModal);
  
      var emailForm = document.getElementById('audit-email-form');
      if (emailForm) emailForm.addEventListener('submit', function(e) {
          e.preventDefault();
          var to = document.getElementById('audit-email-to').value.trim();
          if (!to) return;
          var mod = document.getElementById('audit-module').value;
          var lang = document.getElementById('audit-lang').value;
          var reports = allReports.filter(r =>
              (mod === "" || r.module === mod) && (lang === "" || r.lang === lang)
          );
          var data = JSON.stringify(reports, null, 2);
          var xhr = new XMLHttpRequest();
          xhr.open('POST', window.location.pathname + '?sendmail=1');
          xhr.setRequestHeader('Content-Type', 'application/json');
          xhr.onload = function() {
              var msg = (xhr.status === 200) ? '✅ Email sent!' : '❌ Failed to send: ' + xhr.responseText;
              document.getElementById('audit-email-status').textContent = msg;
              if (xhr.status === 200) setTimeout(closeAuditEmailModal, 1800);
          };
          xhr.send(JSON.stringify({ to: to, mod: mod, lang: lang, data: data }));
          document.getElementById('audit-email-status').textContent = 'Sending...';
      });
  });

    // Export functionality
    function exportAudit(format, action) {
        let mod = document.getElementById("audit-module").value;
        let lang = document.getElementById("audit-lang").value;
        let fileFilter = document.getElementById("audit-file").value.toLowerCase();
        
        // Filter reports based on current selections
        let reports = allReports.filter(r =>
            (mod === "" || r.module === mod) &&
            (lang === "" || r.lang === lang) &&
            (fileFilter === "" || r.file.toLowerCase().includes(fileFilter))
        );
        
        let content = '';
        let filename = 'lang_audit_report';
        let mimeType = 'text/plain';
        
        // Add module/language info to filename if filtered
        if (mod) filename += '_' + mod;
        if (lang) filename += '_' + lang;
        
        // Generate content based on format
        if (format === 'json') {
            content = JSON.stringify(reports, null, 2);
            mimeType = 'application/json';
            filename += '.json';
        } else if (format === 'csv') {
            // CSV header
            content = 'Module,Language,File,Status,Missing Constants,Extra Constants,Unused Constants\n';
            
            // CSV rows
            for (let r of reports) {
                let status = r.ok ? 'OK' : 'Missing';
                let missing = r.missing ? r.missing.join('|') : '';
                let extra = r.extra ? r.extra.join('|') : '';
                let unused = r.extra_unused ? r.extra_unused.join('|') : '';
                
                // Escape quotes in CSV fields
                missing = missing.replace(/"/g, '""');
                extra = extra.replace(/"/g, '""');
                unused = unused.replace(/"/g, '""');
                
                content += `"${r.module}","${r.lang}","${r.file}","${status}","${missing}","${extra}","${unused}"\n`;
            }
            mimeType = 'text/csv';
            filename += '.csv';
        } else if (format === 'html') {
            // Create HTML report
            content = `<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Language Audit Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { color: #333; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .ok { color: green; }
        .missing { color: red; }
        .extra { color: orange; }
    </style>
</head>
<body>
    <h1>Language Audit Report</h1>
    <p>Generated: ${new Date().toLocaleString()}</p>`;
            
            if (mod || lang) {
                content += '<p><strong>Filters:</strong> ';
                if (mod) content += `Module: ${mod} `;
                if (lang) content += `Language: ${lang}`;
                content += '</p>';
            }
            
            content += `
    <table>
        <tr>
            <th>Module</th>
            <th>Language</th>
            <th>File</th>
            <th>Status</th>
            <th>Missing Constants</th>
            <th>Extra Constants</th>
        </tr>`;
            
            for (let r of reports) {
                let statusClass = r.ok ? 'ok' : 'missing';
                let statusText = r.ok ? '✅ OK' : '❌ Missing';
                
                content += `
        <tr>
            <td>${r.module}</td>
            <td>${r.lang}</td>
            <td>${r.file}</td>
            <td class="${statusClass}">${statusText}</td>
            <td>`;
                
                if (r.missing && r.missing.length) {
                    content += '<ul>';
                    for (let c of r.missing) {
                        content += `<li class="missing">${c}</li>`;
                    }
                    content += '</ul>';
                }
                
                content += `</td>
            <td>`;
                
                if (r.extra && r.extra.length) {
                    content += '<ul>';
                    for (let c of r.extra) {
                        let unusedMark = (r.extra_unused && r.extra_unused.includes(c)) ? ' 🚫' : '';
                        content += `<li class="extra">${c}${unusedMark}</li>`;
                    }
                    content += '</ul>';
                }
                
                content += `</td>
        </tr>`;
            }
            
            content += `
    </table>
    <p>✅ = All constants present<br>❌ = Missing constant<br>⚠️ = Extra constant in translation<br>🚫 = Extra constant not used anywhere in codebase</p>
</body>
</html>`;
            
            mimeType = 'text/html';
            filename += '.html';
        }
        
        // Handle view or download based on action
        if (action === 'view') {
            // Open in new tab
            let blob = new Blob([content], {type: mimeType});
            let url = URL.createObjectURL(blob);
            window.open(url, '_blank');
        } else if (action === 'download') {
            // Download file
            let blob = new Blob([content], {type: mimeType});
            let url = URL.createObjectURL(blob);
            let a = document.createElement('a');
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            setTimeout(function() {
                document.body.removeChild(a);
                window.URL.revokeObjectURL(url);
            }, 0);
        }
    }

    // Event listeners
    window.addEventListener('DOMContentLoaded', function() {
        // Export select handler
        var exportSel = document.getElementById('audit-export-select');
        if (exportSel) {
            exportSel.addEventListener('change', function(e) {
                const val = e.target.value;
                if (!val) return;
                let [action, format] = val.split('-');
                exportAudit(format, action);
                e.target.value = '';
            });
        }
        
        // Restore filters from localStorage
        const savedMod = localStorage.getItem('auditModule');
        const savedLang = localStorage.getItem('auditLang');
        const savedFile = localStorage.getItem('auditFile');
        
        if (savedMod) {
            const modSel = document.getElementById('audit-module');
            if (modSel) modSel.value = savedMod;
        }
        if (savedLang) {
            const langSel = document.getElementById('audit-lang');
            if (langSel) langSel.value = savedLang;
        }
        if (savedFile) {
            const fileInput = document.getElementById('audit-file');
            if (fileInput) fileInput.value = savedFile;
        }

        // If results are visible, apply filters
        if (document.getElementById('audit-results').style.display === 'block') {
            filterReports();
        }
    });

    // Save filter selections to localStorage
    document.addEventListener('change', function(e) {
        if (e.target && e.target.id === 'audit-module') {
            localStorage.setItem('auditModule', e.target.value);
        }
        if (e.target && e.target.id === 'audit-lang') {
            localStorage.setItem('auditLang', e.target.value);
        }
        if (e.target && e.target.id === 'audit-file') {
            localStorage.setItem('auditFile', e.target.value);
        }
    });
    </script>

</section>
</body>
</html>


<script>
window.addEventListener('DOMContentLoaded', function() {
    var resetBtn = document.getElementById('lang-reset-english-btn');
    if (resetBtn) {
        resetBtn.addEventListener('click', function() {
            if (!confirm('Are you sure you want to reset the site language to English? This is for emergency recovery.')) return;
            resetBtn.disabled = true;
            resetBtn.textContent = 'Resetting...';
            fetch(window.location.pathname + '?resetlang=1', {method:'POST'})
                .then(r => r.json())
                .then(resp => {
                    if (resp.success) {
                        alert('Site language has been reset to English. Please reload the page.');
                        location.reload();
                    } else {
                        alert('Failed to reset language: ' + (resp.error || 'Unknown error'));
                        resetBtn.disabled = false;
                        resetBtn.textContent = 'Reset Site Language to English';
                    }
                })
                .catch(e => {
                    alert('Error: ' + e);
                    resetBtn.disabled = false;
                    resetBtn.textContent = 'Reset Site Language to English';
                });
        });
    }
});
const savedFile = localStorage.getItem('auditFile');
if (savedFile) {
    const fileInput = document.getElementById('audit-file');
    if (fileInput) fileInput.value = savedFile;
}
// Re-apply filters if needed (already there)
// if (document.getElementById('audit-results').style.display === 'block') {
//     filterReports();
// }

</script>

<?php
// TODO check if any setup available
// PHP: handle email send request using Mailer
if (isset($_GET['sendmail']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $input = json_decode(file_get_contents('php://input'), true);
    $to = filter_var($input['to'] ?? '', FILTER_VALIDATE_EMAIL);
    $mod = $input['mod'] ?? '';
    $lang = $input['lang'] ?? '';
    $data = $input['data'] ?? '';
    if (!$to || !$data) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid email or data.']);
        exit;
    }
    require_once dirname(__FILE__) . '/mainfile.php';
    require_once XOOPS_ROOT_PATH . '/class/xoopsmailer.php';
    $subject = 'XOOPSCube XCL - Recovery Language Audit Report';
    if ($mod || $lang) $subject .= " [" . ($mod ? "Module: $mod " : "") . ($lang ? "Lang: $lang" : "") . "]";
    $body = "Attached is the audit report as a JSON file.\n\n";
    $mailer = new xoopsmailer();
    $mailer->useMail();
    $mailer->setToEmails([$to]);
    $mailer->setFromEmail($GLOBALS['xoopsConfig']['adminmail'] ?? 'webmaster@' . $_SERVER['SERVER_NAME']);
    $mailer->setFromName($GLOBALS['xoopsConfig']['sitename'] ?? 'XOOPS Site');
    $mailer->setSubject($subject);
    $mailer->setBody($body);
    // Attach the JSON report
    $filename = 'lang_audit_report.json';
    if (method_exists($mailer->multimailer, 'addStringAttachment')) {
        $mailer->multimailer->addStringAttachment($data, $filename, 'base64', 'application/json');
    }
    $ok = $mailer->send();
    if ($ok) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        $errors = method_exists($mailer, 'getErrors') ? $mailer->getErrors(false) : ['Mail send failed.'];
        echo json_encode(['error' => 'Mail send failed.', 'details' => $errors]);
    }
    exit;
}
