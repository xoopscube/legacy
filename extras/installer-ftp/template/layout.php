<html>
<head>
  <title><?php $this->e('title')?> | <?php $this->e('subtitle')?></title>
  <meta http-equiv="Content-Type" content="text/html; charset=<?php $this->c('_INSTALL_CHARSET')?>" />
  <link rel="stylesheet" type="text/css" media="all" href="style_xcl.css" />
  <script type="text/javascript">var lang='<?php $this->e('lang')?>';</script>
  <script type="text/javascript" src="./js/prototype-1.6.0.2.js"></script>
  <script type="text/javascript" src="./js/hd_ftp_installer.js"></script>
</head>
<body>


<div id="container">

    <h1>XOOPS Cube Legacy - Web FTP Installer</h1>
	
	<noscript><div class="confirmMsg"><?php $this->_('Please set your browser with JavaScript ON.');?></div></noscript>
	
	<div class="maincontents">
	
    	<div class="title"><?php $this->e('subtitle')?></div>
		
		<div id="main">
		<?php echo $this->template_vars['content']?>
			<div id="ftpcheck" style="display:none;"></div>
			<div id="getRepository" style="display:none;"></div>
			<div id="selectPackage" style="display:none;"></div>
			<div id="xoopsParam" style="display:none;"></div>
			<div id="installprocess" style="display:none;">
				<ul class="indicator" id="processIndicator"><li class="process"><?php $this->_('Downloading package file from selected server. Please wait until the download is completed.')?></li></ul>
			</div>
			<div id="installprocessSuccess"></div>
			<div id="installprocessError"></div>
			<div id="install2ndProcess" style="display:none;">
				<h2><?php $this->_('XOOPSCube installer 2nd STEP Start.')?></h2>
				<ul class="indicator">
					<li class="success"><?php $this->_('Login as XOOPS Cube Admin User.')?></li>
					<li class="success" id="loginSuccess" style="display:none;"><?php $this->_("Login Successful.");?></li>
					<li class="success" id="moduleInstallSuccess" style="display:none;"><?php $this->_("Modules Installed Successfully");?></li>
					<li class="failed"  id="loginFailed" style="display:none;"><?php $this->_("Login Failed.");?></li>
				</ul>
				<div id="AllFinished" style="display:none;"><a href="#"><?php $this->_('XOOPS Cube Install Finished. Start to Click HERE.')?></a></div>
			</div>
		</div>
		<div id="loading" style="display:none;">
			<img src="./img/ajax-loader.gif"
		</div>
	</div>
	
	<div id="footer">
		<?php $this->_('This Web FTP Installer is based on HD Extended Installer released under BSD Licence.')?>
	</div>

</div>
<div id="temp" style="display:none;"></div>
</body>
</html>
