<?php

$form = new Form();
$form = $form->create()
	
	// set From
	->elem('div')
	->attr('data-plugins',"formInsert")
	->addClass('form-insert');
	// ->style('horizontal')

require WWW_VIEW."employees/form/{$this->step}.php";
$form_html = $form->html();