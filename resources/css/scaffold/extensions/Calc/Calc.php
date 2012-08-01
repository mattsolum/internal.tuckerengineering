<?php

class Scaffold_Extension_calc extends Scaffold_Extension
{
	/**
	 * Registers a custom CSS function.
	 * @access public
	 * @param $functions
	 * @return array
	 */
	public function register_function($functions)
	{
		$functions->register('calc',array($this,'parse'));
	}
	
	/**
	 * Parses rand() functions within the CSS
	 * @access public
	 * @param $input
	 * @return string
	 */
	public function parse($input)
	{
		$formula = preg_replace("/\D/", '', $input) . ';';
		$return = '';
		return 'asd $return = ' . "$formula asdasd";
	}
}