<?php

/**
 * This is a PHP version of ontime template since Smarty started to argue...
 */

$assetsVersion = '?v=1.2.3';

?><!DOCTYPE html>
<html xml:lang="en" lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <meta charset="utf-8" />
    <?php echo $design_head; ?>

    <link rel="stylesheet" href="templates/ontime/css/style.css<?php echo $assetsVersion; ?>" type="text/css" />

    <link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
    <link rel="apple-touch-icon" href="favicon.ico">

    <script src="templates/ontime/js/jquery.min.js<?php echo $assetsVersion; ?>"></script>
    <script src="templates/ontime/js/mobilemenu.js<?php echo $assetsVersion; ?>"></script>
    <script src="templates/ontime/js/timeago.min.js<?php echo $assetsVersion; ?>"></script>
    <script src="templates/ontime/js/scripts.js<?php echo $assetsVersion; ?>"></script>

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
                <div class="title"><?php echo _("Main menu"); ?></div>
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
                <div class="title"><?php _("Eventmenu"); ?></div>
                <div class="section-content">
                    <?php echo $design_eventmenu; ?>
                </div>
            </div>
            <?php }
            if ($design_userinfo) { ?>
            <div class="section">
                <div class="title"><?php echo _("Userinfo"); ?></div>
                <div class="section-content">
                    <?php echo $design_userinfo; ?>
                </div>
            </div>
            <?php }
            if ($design_eventlist) { ?>
            <div class="section">
                <div class="title"><?php _("Events"); ?></div>
                <div class="section-content">
                    <ul>
                            <?php echo $design_eventlist; ?>
                        </ul>
                </div>
            </div>
            <?php }
            if ($design_grouplist) { ?>
            <div class="section">
                <div class="title"><?php echo _("My groups"); ?></div>
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
                    <h2 class="title">
                        <span><?php if ($sessioninfo->userID <= 1) {
                            echo _("Login");
                        } else {
                            echo _("Userinfo");
                        } ?></span>
                    </h2>
                    <div class="cont">
                        <?php echo $design_userinfo; ?>
                    </div>
                </div>
                <div class="box events">
                    <h2 class="title">
                        <span><?php echo _("Events"); ?></span>
                    </h2>
                    <div class="cont">
                        <nav class="bot pad">
                            <ul class="nav">
                                <?php echo $design_eventlist; ?>
                            </ul>
                        </nav>
                    </div>
                </div>
                <?php if ($design_grouplist) { ?>
                    <div class="box groups">
                        <h2 class="title">
                            <span><?php echo _("My groups"); ?></span>
                        </h2>
                        <div class="cont">
                            <nav class="bot pad">
                                <ul class="nav">
                                    <?php echo $design_grouplist; ?>
                                </ul>
                            </nav>
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
            <div class="container footer">
                <p><?php echo _("Powered by"); ?> <a href="https://github.com/lancms/lancms1/">lancms</a></p>
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
