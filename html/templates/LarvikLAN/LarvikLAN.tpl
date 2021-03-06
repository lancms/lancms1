<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
	<title>{$eventtitle}</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link rel="stylesheet" href="templates/LarvikLAN/style.css" type="text/css" />
	{$head}
</head>
<body>
<div id="PageBackgroundSimpleGradient"></div>
<div id="Main">
	<div id="Sheet">
		<div id="Sheet-body">
			<div id="Header">
				<div id="Header-left"></div>
				<div id="Header-repeat"><div></div></div>
				<div id="Header-right"></div>
					<div id="logo">
							{if $eventinfo}
							<h1 id="name-text" class="logo-name"><a href="#">{$eventinfo->eventname}</a></h1>
							<div id="slogan-text" class="logo-text">{$eventinfo->eventsubtext}</div>
							{else}
							<h1 id="name-text" class="logo-name"><a href="#">{$title}</a></h1>
							<div id="slogan-text" class="logo-text">lancms</div>
							{/if}
						</div>
						
				
			</div>
			
			<div id="MainContent">
<div id="Menu">

        <!-- MENU -->
        <div class="Block">
                <div class="Block-tl"></div>
                <div class="Block-tr"><div></div></div>
                <div class="Block-bl"><div></div></div>
                <div class="Block-br"><div></div></div>
                <div class="Block-tc"><div></div></div>
                <div class="Block-bc"><div></div></div>
                <div class="Block-cl"><div></div></div>
                <div class="Block-cr"><div></div></div>
                <div class="Block-cc"></div>
                <div class="Block-body">
                        <div class="BlockHeader">
                                <div class="BlockHeader-text">
                                        MENU
                                </div>
                        </div>
                        <div class="BlockContent">
                                <div class="BlockContent-body">
                                        <div>
                                                <ul>
							{$menu}
							{$eventmenu}
                                                </ul>
                                        </div>
                                </div>
                        </div>
                </div>
        </div>
        <!-- END MENU -->

        <!-- LOGIN -->
        <div class="Block">
                <div class="Block-tl"></div>
                <div class="Block-tr"><div></div></div>
                <div class="Block-bl"><div></div></div>
                <div class="Block-br"><div></div></div>
                <div class="Block-tc"><div></div></div>
                <div class="Block-bc"><div></div></div>
                <div class="Block-cl"><div></div></div>
                <div class="Block-cr"><div></div></div>
                <div class="Block-cc"></div>
                <div class="Block-body">
                        <div class="BlockHeader">
                                <div class="BlockHeader-text">
					{if $sessioninfo->userID <= 1}
					LOGIN!
					{else}
					USERINFO
					{/if}
                                </div>
                        </div>
                        <div class="BlockContent">
                                <div class="BlockContent-body">
                                        <div>
					{$userinfo}
                                        </div>
                                </div>
                        </div>
                </div>
        </div>
        <!-- END LOGIN -->
        <!-- SOME KINDA EVEN MENU -->
        <div class="Block">
                <div class="Block-tl"></div>
                <div class="Block-tr"><div></div></div>
                <div class="Block-bl"><div></div></div>
                <div class="Block-br"><div></div></div>
                <div class="Block-tc"><div></div></div>
                <div class="Block-bc"><div></div></div>
                <div class="Block-cl"><div></div></div>
                <div class="Block-cr"><div></div></div>
                <div class="Block-cc"></div>
                <div class="Block-body">
                        <div class="BlockHeader">
                                <div class="BlockHeader-text">
                                        EVENTS
                                </div>
                        </div>
                        <div class="BlockContent">
                                <div class="BlockContent-body">
                                        <div>
                                                <ul>
																	{$eventlist}	
                                                </ul>
                                        </div>
                                </div>
                        </div>
								{if $grouplist}
                        <div class="BlockHeader">
                                <div class="BlockHeader-text">
                                        GROUPS
                                </div>
                        </div>
                        <div class="BlockContent">
                                <div class="BlockContent-body">
                                        <div>
																<ul>
	                                              	{$grouplist}
																</ul>
                                                
                                        </div>
                                </div>
                        </div>
								{/if}
                </div>
        </div>
        <!-- SOME KINDA EVEN MENU -->

</div>
			
				<div id="Content">
			{$content}

                        </div>
                        </div>


                        <div id="Footer">
                        <div id="Footer-left"></div>
                        <div id="Footer-repeat"><div></div></div>
                        <div id="Footer-right"></div>

                                <div id="Footer-inner">
                                        <div id="Footer-text">
                                                <p>Copyright &copy; 2009 ---. All Rights Reserved.</p>
                                        </div>
                                </div>
                                <div id="Footer-background"></div>
                        </div>


                </div>
        </div>
</div>

</body>
</html>

