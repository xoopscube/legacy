<?php
/**
 * CKEditor4 module for XCL
 * @package    CKEditor4
 * @version    2.4.0
 * @author     Other authors Nuno Luciano (aka gigamaster), 2020, XCL PHP7
 * @author     Naoki Sawada (aka nao-pon) <https://xoops.hypweb.net/>
 * @copyright  (c) 2005-2024 The XOOPSCube Project
 * @license    GPL 2.0
 */

require_once '../../../mainfile.php';
require_once XOOPS_ROOT_PATH . '/header.php';
require_once XOOPS_ROOT_PATH . '/modules/ckeditor4/class/Ckeditor4Utiles.class.php';

$mid = Ckeditor4_Utils::getMid();
if (defined('LEGACY_BASE_VERSION')) {
    $dash = XOOPS_URL . '/admin.php';
	$pref = XOOPS_MODULE_URL . '/legacy/admin/index.php?action=PreferenceEdit&amp;confmod_id=';
	$help = XOOPS_MODULE_URL . '/legacy/admin/index.php?action=Help&amp;dirname=ckeditor4';
}
?>
    <nav class="ui-breadcrumbs" aria-label="breadcrumb">
        <a href="<?php echo $dash ?>"><?php echo _CPHOME ?></a>
        »» <span class="page-title" aria-current="page"><a href="./index.php">CKEditor</a></span>
    </nav>

    <nav class="adminavi">
        <a href="<?php echo $pref . $mid ?>" class="adminavi-item"><?php echo _PREFERENCES ?></a>
        <a href="<?php echo $help ?>" class="adminavi-item"><?php echo _HELP ?></a>
    </nav>
<h3>CKEditor4 XCL</h3>

<div class="ui-card-full">

    <section id="help-features">

        <h3>Main Features</h3>
        <p>The XOOPSCube module CKEditor4 is released by default with a customized Full package bundle and provides out of the box:</p>

        <ul>
            <li><b>Control Panel</b> preferences settings e.g. toolbars, user group permissions, ui color.</li>
            <li><b>Localization</b>languages installed by default : English, French, Japanese, Portuguese.</li>
            <li><b>Template</b> a Single File Component for frontend and backend.</li>
            <li><b>Automatic change of editor</b> HTML or BBCode depending on modules and user group permissions.</li>
            <li><b>Automatic switch of ToolBar</b> based on modules preferences and user group permissions.</li>
            <li><b>BBCode editor</b> extends the CKEditor standard bbcode plugin</li>
            <li><b>HTML editor</b> with custom Toolbar for each user group</li>
            <li><b>Extra Plugins</b> customized CodeMirror, oEmbed, Paste (raw text, formatted or code).</li>
            <li><b>PHP mode</b> for PHP code blocks without the <code>&lt;?php</code> opening tag.</li>
            <li><b>Smarty mode</b> for Smarty Template Engine code blocks.</li>
            <li><b>elFinder</b> open-source web file manager with cloud storage settings.</li>
        </ul>

        <p>CKEditor WYSIWYG editor brings to the web common editing features found on desktop editing applications
            like Microsoft Word and OpenOffice.</p>

        <h3>CKEditor vs. Word</h3>
        <P>CKEditor is not a desktop application like Microsoft Word or OpenOffice.
        <P>It's a component to be used by developers to enhance their applications.
        <P>It's an editor to be used inside web pages.</p>

        <h3>Custom Theme</h3>
        <p>Theme style.css can specify the CKEditor background and color, otherwise it fallbacks to default.</p>
        <pre><code class="lang-css">/* Text color */
        /*color: #333;*/
        color: var(--ckeditor-color, #bbc6ce);
        /* Remove the background color to make it transparent. */
        /*background-color: #fff;*/
                background : var(--ckeditor-background, #11191f92);</code></pre>
    </section>

    <p><a class="button" href="https://xoopscube.github.io/ckeditor4/" target="_blank">xoopscube.github.io/ckeditor4 ⭧</a></p>

</div>

<?php
require_once XOOPS_ROOT_PATH . "/footer.php";
