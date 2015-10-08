<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>{$title}</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link href="templates/GlobeLAN11/style.css" rel="stylesheet" type="text/css">
{$head}
</head>

<body>
  
<div id="hele_top">&nbsp;</div>
<div id="hele">

  <!-- TOP -->

  <div id="top">
    
	<h1><span>Globelan #11</span></h1>
	
  </div>
  
  <!-- MENY -->

  <div id="meny">
    
	<div id="navigasjon_top">&nbsp;</div>
      
 	 <ul id="navigasjon">

			{$menu}
			<hr>
			{$eventmenu}
			
	
    </ul>
	
	<div id="navigasjon_bottom">&nbsp;</div>

  <!-- LOGG INN  -->

	<div id="login_top">&nbsp;</div>
	  
	  <div id="login">
	  
	  	{$userinfo}
		
	  </div>
	  
	<div id="login_bottom">&nbsp;</div>
	
  <!-- REKLAME -->
	
 
	<div id="navigasjon_top">&nbsp;</div>
	  
	  <div id="navigasjon">
	  <ul id="navigasjon">
	     {$eventlist}
	     <br><hr><br>
	     {$grouplist}
	   </ul>
	  </div>
	
	<div id="navigasjon_bottom">&nbsp;</div>

	  
  </div>
   
  <!-- HOVEDVINDU -->
  	
  <div id="hoved">
{$content}



</div>

  <div id="bunn">Design is copyright Aleksander Lengard.
  </div>

</div>
<div id="hele_bottom">&nbsp;</div>
  </body>

</html>
