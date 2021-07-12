<?php

require_once 'XHTML/QuickForm.php';
require_once 'XHTML/Togglebox.php';

$form =& new XHTML_QuickForm('post', '?demo=quickform', array(
	'class'	=> 'loginForm',
));
$form->addElement('text', 'username', 'Username: ', array(
	'class'	=> 'loginTextbox',
));
$form->addElement('password', 'password', 'Password: ', array(
	'class'	=> 'loginTextbox',
));

$togglebox =& new XHTML_Togglebox(array(
	'name'	=> 'remember',
));
$togglebox->setMetas(array(
	'label'	=> 'Remember Me',
));
$form->addElement($togglebox);

/*
$form->addElement('hidden', 'remember');
$form->addElement('checkbox', 'remember', 'Remember Me');
*/

$form->addElement('submit', null, 'Log In', array(
	'class'	=> 'loginButton',
));

?>