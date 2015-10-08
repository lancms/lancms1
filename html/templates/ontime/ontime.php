<?php

/**
 * This is a PHP version of ontime template since Smarty started to argue...
 */

?><!DOCTYPE html>
<html xml:lang="en" lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <meta charset="utf-8" />
    <?php echo $design_head; ?>

    <link rel="stylesheet" href="<?php echo $siteUrl; ?>/templates/ontime/css/style.css" type="text/css" />
    <link rel="stylesheet" href="<?php echo $siteUrl; ?>/templates/ontime/css/responsive.css" type="text/css" />

    <link rel="shortcut icon" type="image/x-icon" href="<?php echo $siteUrl; ?>/favicon.ico">
    <link rel="apple-touch-icon" href="<?php echo $siteUrl; ?>/favicon.ico">
    
    <script src="<?php echo $siteUrl; ?>/templates/ontime/js/jquery.min.js"></script>
    <script src="<?php echo $siteUrl; ?>/templates/ontime/js/mobilemenu.js"></script>
    <script src="<?php echo $siteUrl; ?>/templates/ontime/js/scripts.js"></script>

    <?php if ($eventinfo->eventname) { ?>
        <title><?php echo $eventinfo->eventname; ?></title>
    <?php } else { ?>
        <title><?php echo $design_title; ?></title>
    <?php } ?>
</head>
<body>

<div id="hole-page">
    <div id="mobilemenu">
        <div class="inner">
            <div class="section">
                <div class="title">Hovedmeny</div>
                <div class="section-content">
                    <nav>
                        <ul>
                            <?php echo $design_menu; ?>
                        </ul>
                    </nav>
                </div>
            </div>
            <?php if ($design_eventmenu) { ?>
            <div class="section">
                <div class="title">Eventenu</div>
                <div class="section-content">
                    <?php echo $design_eventmenu; ?>
                </div>
            </div>
            <?php }
            if ($design_userinfo) { ?>
            <div class="section">
                <div class="title">Userinfo</div>
                <div class="section-content">
                    <?php echo $design_userinfo; ?>
                </div>
            </div>
            <?php }
            if ($design_eventlist) { ?>
            <div class="section">
                <div class="title">Events</div>
                <div class="section-content">
                    <ul>
                            <?php echo $design_eventlist; ?>
                        </ul>
                </div>
            </div>
            <?php }
            if ($design_grouplist) { ?>
            <div class="section">
                <div class="title">Groups</div>
                <div class="section-content">
                    <ul><?php echo $design_grouplist; ?></ul>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>

    <div id="page">
        <div class="header">
            <div class="container">
                <div class="always-gone" id="mobile-menu-button">
                    <div class="sr-bar"></div>
                    <div class="sr-bar"></div>
                    <div class="sr-bar"></div>
                </div>
                <div class="logo">
                    <h1>
                        <a href="/">
                            <?php if ($eventinfo->eventname) {
                                echo $eventinfo->eventname;
                            } else {
                                echo $design_title;
                            } ?>
                        </a>
                    </h1>
                </div>
                <nav class="main-menu">
                    <ul>
                        <?php echo $design_menu; ?>
                    </ul>
                </nav>
                <div class="clear"></div>
            </div>
        </div>

        <div class="container page">
            <div class="left">
                <?php echo $design_eventmenu; ?>
                <div class="box userinfo">
                    <div class="title">
                        <h2><?php if ($sessioninfo->userID <= 1) { ?>
                            Login
                        <?php } else { ?>
                            Userinfo
                        <?php } ?></h2>
                    </div>
                    <div class="cont">
                        <?php echo $design_userinfo; ?>
                    </div>
                </div>
                <div class="box events">
                    <div class="title">
                        <h2>Events</h2>
                    </div>
                    <div class="cont">
                        <ul>
                            <?php echo $design_eventlist; ?>
                        </ul>
                    </div>
                </div>
                <?php if ($design_grouplist) { ?>
                    <div class="box groups">
                        <div class="title">
                            <h2>Groups</h2>
                        </div>
                        <div class="cont">
                            <ul>
                                <?php echo $design_grouplist; ?>
                            </ul>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <div class="content">
                <?php echo $content; ?>
            </div>
            <div class="clear"></div>
        </div>
        <footer>
            <div class="container">
                <p>Powered by <a href="http://launchpad.net/lancms/">lancms</a> by Mathias B&oslash;hn Grytemark, Lars &Aring;ge Kamfjord and Edvin Hultberg.</p>
            </div>
        </footer>
    </div>
</div>

    <script>
    $(document).ready(function(){
        var mob = $(window).mobileMenu();

        $("#mobile-menu-button").on('click', function(){
            mob.toggle();
        });
    });
    </script>

</body>
</html>