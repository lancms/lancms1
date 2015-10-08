<!DOCTYPE html>
<html xml:lang="en" lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <meta charset="utf-8" />
    {$head}

    <link rel="stylesheet" href="{$siteUrl}/templates/ontime/css/style.css" type="text/css" />
    <link rel="stylesheet" href="{$siteUrl}/templates/ontime/css/responsive.css" type="text/css" />

    <link rel="shortcut icon" type="image/x-icon" href="{$siteUrl}/favicon.ico">
    <link rel="apple-touch-icon" href="{$siteUrl}/favicon.ico">
    
    <script src="{$siteUrl}/templates/ontime/js/jquery.min.js"></script>
    <script src="{$siteUrl}/templates/ontime/js/mobilemenu.js"></script>
    <script src="{$siteUrl}/templates/ontime/js/scripts.js"></script>

    {if $eventinfo->eventname}
        <title>{$eventinfo->eventname}</title>
    {else}
        <title>{$title}</title>
    {/if}
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
                            {$menu}
                        </ul>
                    </nav>
                </div>
            </div>
            {if $eventmenu}
            <div class="section">
                <div class="title">Eventenu</div>
                <div class="section-content">
                    {$eventmenu}
                </div>
            </div>
            {/if}
            {if $userinfo}
            <div class="section">
                <div class="title">Userinfo</div>
                <div class="section-content">
                    {$userinfo}
                </div>
            </div>
            {/if}
            {if $eventlist}
            <div class="section">
                <div class="title">Events</div>
                <div class="section-content">
                    <ul>
                            {$eventlist}
                        </ul>
                </div>
            </div>
            {/if}
            {if $grouplist}
            <div class="section">
                <div class="title">Groups</div>
                <div class="section-content">
                    <ul>{$grouplist}</ul>
                </div>
            </div>
            {/if}
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
                        {if $eventinfo->eventname}
                            <a href="/">{$eventinfo->eventname}</a>
                        {else}
                            <a href="/">{$title}</a>
                        {/if}
                    </h1>
                </div>
                <nav class="main-menu">
                    <ul>
                        {$menu}
                    </ul>
                </nav>
                <div class="clear"></div>
            </div>
        </div>

        <div class="container page">
            <div class="left">
                {$eventmenu}
                <div class="box userinfo">
                    <div class="title">
                        {if $sessioninfo->userID <= 1}
                            <h2>Login</h2>
                        {else}
                            <h2>Userinfo</h2>
                        {/if}
                    </div>
                    <div class="cont">
                        {$userinfo}
                    </div>
                </div>
                <div class="box events">
                    <div class="title">
                        <h2>Events</h2>
                    </div>
                    <div class="cont">
                        <ul>
                            {$eventlist}
                        </ul>
                    </div>
                </div>
                {if $grouplist}
                    <div class="box groups">
                        <div class="title">
                            <h2>Groups</h2>
                        </div>
                        <div class="cont">
                            <ul>
                                {$grouplist}
                            </ul>
                        </div>
                    </div>
                {/if}
            </div>
            <div class="content">
                {$content}
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


    <script src="{$siteUrl}/templates/ontime/js/jquery.min.js"></script>
    <script src="{$siteUrl}/templates/ontime/js/mobilemenu.js"></script>
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
