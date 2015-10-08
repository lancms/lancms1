<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<!-- module head code -->
		{$head}
		<!-- end module head code -->

		<!-- chrome fix -->
		{if "Chrome"|eregi:$smarty.server.HTTP_USER_AGENT}
		<link rel="stylesheet" href="templates/Alfa1/design/extra_chrome.css" type="text/css" />
		{else}
		<link rel="stylesheet" href="templates/Alfa1/design/extra_normal.css" type="text/css" />
		{/if}
		<!-- end chrome fix -->
		
		<link rel="stylesheet" href="templates/Alfa1/design/default.css" type="text/css" />

		{if $eventinfo->eventname}
		<title>{$eventinfo->eventname}</title>
		{else}
		<title>{$title}</title>
		{/if}
	</head>
	<body>

		<div id="wrap">
			<div id="top">
				<h2>
					{if $eventinfo->eventname}
					<a href="/">{$eventinfo->eventname}</a>
					{else}
					<a href="/">{$title}</a>
					{/if}
				</h2>
				<div id="menu">
					<ul>
						{$menu}
					</ul>
				</div>
			</div>
			<div id="content">
				<div id="left">
					{$content}
				</div>
				<div id="right">
					<ul id="nav">
						{$eventmenu}
					</ul>
					<div class="box">
						{if $sessioninfo->userID <= 1}
						<h2 style="margin-top: 17px;">Login</h2>
						{else}
						<h2 style="margin-top: 17px;">Userinfo</h2>
						{/if}
						{$userinfo}
					</div>
					<div class="box">
						<h2 style="margin-top: 17px;">Events</h2>
						<ul>
							{$eventlist}
						</ul>
					</div>
					{if $grouplist}
					<div class="box">
						<h2 style="margin-top: 17px;">Groups</h2>
						<ul>
							{$grouplist}
						</ul>
					</div>
					{/if}
				</div>
				<div id="clear"></div>
			</div>
			<div id="footer">
				<p>Design by Mathias B&oslash;hn Grytemark, based on an <a href="http://www.oswd.org/">open source design</a> by <a href="http://www.loadfoo.org/">LoadFoO</a>.</p>
				<p>Powered by <a href="http://launchpad.net/lancms/">lancms</a> by Mathias B&oslash;hn Grytemark and Lars &Aring;ge Kamfjord.</p>
				{if $footer}
				<a href="{$footer.url}"><img width="{$footer.width}" height="{$footer.height}" src="{$footer.logo}" /></a>
				{/if}
			</div>




	</body>
</html>
