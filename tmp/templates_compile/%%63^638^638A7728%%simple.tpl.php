<?php /* Smarty version 2.6.18, created on 2007-04-11 22:22:07
         compiled from simple/simple.tpl */ ?>
<html>

<head><title><?php echo $this->_tpl_vars['title']; ?>
</title>

<body>
<h1>Header</h1>

<!-- I know, I really should learn to program CSS some day... --!>
<table>
<tr><td width=10%>

<?php echo $this->_tpl_vars['userinfo']; ?>


<br><br><hr><br><br>
<?php echo $this->_tpl_vars['menu']; ?>


</td><td><?php echo $this->_tpl_vars['content']; ?>
</td></tr>
</table>