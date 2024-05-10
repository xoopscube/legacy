<!doctype html>
<html class="no-js" lang="en">
<!-- ===========================
    XOOPSCube Theme : XCL Installer Wizard
    Distribution : XCL 2.3  PHP7
    Version : 1.0.0
    Author : Nuno Luciano aka Gigamaster
    Date : 2020-04-29
    URL : https://github.com/xoopscube/
=========================== -->
<head>

    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo _INSTALL_CHARSET ?>">

    <title>XCL Install Wizard</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Use data URI to avoid fake favicon requests by Browsers and base64 for valid HTML5 -->
    <link rel="icon" href="data:;base64,iVBORw0KGgo=">

    <link type="text/css" rel="stylesheet" media="all" href="style.css">

    <script type="text/javascript" src="../common/js/jquery.min.js"></script>

    <style>
        <?php
        $uinav = $GLOBALS['wizardSeq']->getNext($this->_op)[0];

        echo 'body.'.$uinav.' li.'.$uinav.':before > li {background: #face74 !important; border: 1px solid #face74;}'
            .'body.'.$uinav.' li.'.$uinav.' {color: #face74 !important;}'
            .'li.'.$uinav.':before {
                -webkit-animation: neon5 1.5s ease-in-out infinite alternate;
                -moz-animation: neon5 1.5s ease-in-out infinite alternate;
                animation: neon5 1.5s ease-in-out infinite alternate!important;}';
        ?>
        @-webkit-keyframes neon5 {
            from {
                box-shadow: 0 0 5px #fff, 0 0 10px #fff, 0 0 15px #fff, 0 0 20px #FF9900, 0 0 25px #face74, 0 0 30px #face74, 0 0 35px #FF9900;
            }
            to {
                box-shadow: 0 0 3px #fff, 0 0 5px #fff, 0 0 7px #fff, 0 0 10px #FF9900, 0 0 15px #face74, 0 0 20px #face74, 0 0 25px #face74;
            }
        }
    </style>

</head>

<body class="<?php echo $uinav; ?>">

<form action="index.php" method="post">

    <div class="container row">

        <header>
            <nav>
                <h2>
                    <span class="logo">
                        <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false" width="1.5em" height="1.5em" viewBox="0 0 24 24">
                        <path d="M21.406 6.086l-9-4a1.001 1.001 0 0 0-.813 0l-9 4c-.02.009-.034.024-.054.035c-.028.014-.058.023-.084.04c-.022.015-.039.034-.06.05a.87.87 0 0 0-.19.194c-.02.028-.041.053-.059.081a1.119 1.119 0 0 0-.076.165c-.009.027-.023.052-.031.079A1.013 1.013 0 0 0 2 7v10c0 .396.232.753.594.914l9 4c.13.058.268.086.406.086a.997.997 0 0 0 .402-.096l.004.01l9-4A.999.999 0 0 0 22 17V7a.999.999 0 0 0-.594-.914zM12 4.095L18.538 7L12 9.905l-1.308-.581L5.463 7L12 4.095zM4 16.351V8.539l7 3.111v7.811l-7-3.11zm9 3.11V11.65l7-3.111v7.812l-7 3.11z"
                              fill="currentColor">
                        </path></svg>
                    </span>
                    XCL Installer Wizard
                </h2>
                <ul class="steps">
                    <li class="start">Select Language
                    <li class="modcheckext"><?php echo _INSTALL_L80 ?>
                    <li class="dbform"><?php echo _INSTALL_L81 ?>
                    <li class="dbconfirm"><?php echo _INSTALL_L90 ?>
                    <li class="dbsave"><?php echo _INSTALL_L53 ?>
                    <li class="modcheck_trustext"><?php echo _INSTALL_L92 ?>
                    <li class="mainfile"><?php echo _INSTALL_L167 ?>
                    <li class="initial"><?php echo _INSTALL_L94 ?>
                    <li class="checkDB"><?php echo _INSTALL_L102 ?>
                    <li class="createDB"><?php echo _INSTALL_L104 ?>
                    <li class="siteInit"><?php echo _INSTALL_L40 ?>
                    <li class="insertData_theme"><?php echo _INSTALL_L112 ?>
                    <li class="finish"><?php echo _INSTALL_L116 ?>
                    <li class="nextStep"><?php echo _INSTALL_L117 ?>
                </ul>
            </nav>
        </header>


        <main>
			<?php if ( ! empty( $title ) ) {
                echo "<h2> $title </h2>";
			} ?>
            <div class="wizard-content">
                <?php echo $content; ?>
            </div>

            <footer>
				<?php echo b_back( $b_back ); ?>&nbsp;&nbsp;
				<?php echo b_reload( $b_reload ); ?>&nbsp;&nbsp;
				<?php echo b_next( $b_next ); ?>
            </footer>
        </main>

    </div>

</form>


<style>
    .notification {
        color           : hsl(219, 27%, 65%);
        display         : flex;
        flex-basis      : 100%;
        flex-direction  : row-reverse;
        align-items     : center;
        justify-content : center;
        float           : none!important;
    }
    .runtime {
        background: hsl(218, 15%, 25%);
        border:1px dotted hsl(220, 15%, 16%);
        border-radius: 7px;
        box-shadow: 0 14px 28px rgba(0,0,0,0.25), 0 10px 10px rgba(0,0,0,0.20);
        color: #D3D7DEFF;
        display: none;
        margin:1rem auto;
        padding: .5rem .75rem;
        text-align: center;

        position: fixed;
        top: 0;
        left: 0;
        right: 0;

        width: 270px;
        min-width: 240px;
        z-index: 1000;
    }

</style>
<div class="notification runtime">Loading...</div>
<script>
    // Notify
    $( 'a[class^="wizard-"],button[class^="wizard-"]' ).on( "click", function() {
        $('div.runtime').fadeIn( 500 ).delay( 5000 ).fadeOut( 500 );
    });
    // Click toggle all-check
    $('#all-check').click(function(event) {
        if(this.checked) {
            // Iterate each checkbox
            $(':checkbox').each(function() {
                this.checked = true;
            });
        } else {
            $(':checkbox').each(function() {
                this.checked = false;
            });
        }
    });
</script>
</body>
</html>
