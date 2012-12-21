<?php defined('BASEPATH') OR exit('No direct script access allowed');

//Validates an email by the RFC2822 standard
function validate_email($str)
{
	return preg_match('/^[a-z0-9!#$%&\'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$/', $str);
}

function validate_phone($str)
{
	return preg_match('/^(\(?(\d{3})\)?\s?-?\s?(\d{3})\s?-?\s?(\d{4}))$/', $str);
}