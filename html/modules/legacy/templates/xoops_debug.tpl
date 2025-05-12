{* Smarty *}
{* debug.tpl, last updated version 2.6.34-dev customized for XCL *}
{assign_debug_info}
{capture assign=debug_output}
    <!doctype html>
    <html class="no-js" lang="<{$xoops_langcode}>">
    <meta charset="<{$xoops_charset}>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
<head>
    <title>Smarty Debug Console</title>
{literal}
<style>
/* <![CDATA[ */

body.smarty-debug {
  background: hsl(225, 15%, 5%);
  color: #f0ead8;
  scrollbar-width: thin;
  scrollbar-gutter: stable both-edges;
}
div.smarty {
& .smarty-content {
  scrollbar-color: #face74 #face7447;
  scrollbar-width: thin;
  scrollbar-gutter: stable;
}
& details {
    margin-bottom:2px;
}
& summary {
    line-height: 1rem;
    list-style-type: none;
    cursor: pointer;
    transition: transform 0.3s ease-in-out;
}
& summary::marker {
  content: none;
	font-size: 0;
} 
}
div.smarty summary {
  position: relative;
  anchor-name: --summary;
  &::marker {
    content: "";
  }
  &::after {
    all:revert;
  }
  &::before,
  &::after {
    content: "";
    border-block-start: 3px solid #face7474;
    height: 0;
    width: 0.85rem;
    inset-block-start: 50%;
    inset-inline-end: 0;
    position: absolute;
    position-anchor: --summary;
    position-area: top end;
  }
  &::after {
    transform: rotate(90deg);
    transform-origin: 50%;
  }
}
div.smarty details[open] summary {
margin-bottom:2px;
&::after {
  transform: rotate(0deg);
}
}
div.smarty-content {
overflow: hidden; 
}
div.smarty details + div.smarty-content {
  max-height: 0;
  transition: max-height 0.5s ease;
  border:1px solid #facd7447;
}
div.smarty details[open] + div.smarty-content {
max-height: 350px;
overflow: auto;
}
div.smarty {
& h2,h3,h4 {
    margin: 0;
    text-align: left;
    padding: 2px;
    background-color:rgba(250, 205, 115, 0.15);
    color: #face74;
    font: normal  300 13px/1.5 monospace;
    text-transform: uppercase;
    font-weight: 300;
}
& span {
    font: normal  300 13px/1.5 monospace;
}
& table.smarty {
    width: 100%;
}
& table.smarty th,
& table.smarty td {
    padding:5px;
    font: normal  300 13px/1.5 monospace;
    vertical-align: top;
    text-align: left;
}
& table.smarty td {
    color: hsl(90, 50%, 45%);
}
& table.smarty .odd {
    background-color: hsl(215, 15%, 15%);
}
& table.smarty .even {
    background-color: hsl(216, 15%, 13%);
}
& table.smarty tr:hover {
    background: hsl(216, 17%, 20%);
}
& .exectime {
    font-size: 0.8em;
    font-style: italic;
}
& table.smarty #table_assigned_vars th {
    color: hsl(207, 90%, 54%);
}
& table.smarty #table_config_vars th {
    color: hsl(0, 100%, 65%);
}
}
/* ]]> */
</style>
{/literal}
</head>
<body class="smarty-debug">

<div class="smarty">

<details>
  <summary>
<h4 class="smarty">Smarty Debug Console—included templates &amp; config files (load time in seconds)</h4>
</summary>
</details>
<div class="smarty-content">
{section name=templates loop=$_debug_tpls}
    {section name=indent loop=$_debug_tpls[templates].depth}&nbsp;&nbsp;&nbsp;{/section}
    <span color={if $_debug_tpls[templates].type eq "template"}deepskyblue{elseif $_debug_tpls[templates].type eq "insert"}coral{else}cadet{/if}>
        {$_debug_tpls[templates].filename|escape:html}</span>
    {if isset($_debug_tpls[templates].exec_time)}
        <span class="exectime">
        ({$_debug_tpls[templates].exec_time|string_format:"%.5f"})
        {if %templates.index% eq 0}(total){/if}
        </span>
    {/if}
    <br>
{sectionelse}
    <p>no templates included</p>
{/section}
</div>


<details>
  <summary><h3 class="smarty">assigned template variables</h3></summary>
</details>
<div class="smarty-content">
<table id="table_assigned_vars" class="smarty">
    {section name=vars loop=$_debug_keys}
        <tr class="{cycle values="odd,even"}">
            <th>{ldelim}${$_debug_keys[vars]|escape:'html'}{rdelim}</th>
            <td>{$_debug_vals[vars]|@debug_print_var}</td></tr>
    {sectionelse}
        <tr><td><p>no template variables assigned</p></td></tr>
    {/section}
</table>
</div>

<details>
  <summary><h3 class="smarty">assigned config file variables (outer template scope)</h3></summary>
</details>
<div class="smarty-content">
<table id="table_config_vars" class="smarty">
    {section name=config_vars loop=$_debug_config_keys}
        <tr class="{cycle values="odd,even"}">
            <th>{ldelim}#{$_debug_config_keys[config_vars]|escape:'html'}#{rdelim}</th>
            <td>{$_debug_config_vals[config_vars]|@debug_print_var}</td></tr>
    {sectionelse}
        <tr><td><p>no config vars assigned</p></td></tr>
    {/section}
</table>
</div>

</div>

</body>
</html>
{/capture}

{if isset($_smarty_debug_output) and $_smarty_debug_output eq "html"}

    {$debug_output}

    {else}

    <div id="smarty-debug-console" style="position:fixed;bottom:0;left:0;right:0;background:#111;z-index:9999;max-height:50vh;overflow:visible;border-top:1px solid #000;">
        <div style="padding:10px;">
            <p style="margin:0 0 10px 0;cursor:pointer;" onclick="this.parentNode.style.display='none';">Smarty Debug Console [x]</p>
            {$debug_output}
        </div>
    </div>

{/if}
