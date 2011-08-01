<?php
//Path To NOLOH - Use your path
require_once("/var/www/htdocs/Projects/NOLOH/NOLOH.php");
require_once("../ContentSlider.php");

class ContentSliderExample extends WebPage 
{	
	function ContentSliderExample()
	{
		parent::WebPage('ContentSlider Example 1');
		//Define Images 1
	/*	$images = array(
			'Images/1.jpg',
			'Images/2.jpg',
			'Images/3.jpg');*/
		//Define Images 2
		$images = array(
			array('Path' => 'Images/1.jpg', 'URL'  => 'http://www.noloh.com'),
			array('Path' => 'Images/2.jpg', 'URL'  => 'http://www.google.com'),
			array('Path' => 'Images/3.jpg', 'URL'  => 'http://www.facebook.com'));
		//Randomize
		shuffle($images);
		//Create ContentSlider
		$contentSlider = new ContentSlider($images, 0, 0, 300, 200);
		$this->Controls->Add($contentSlider);
	}
}
?>