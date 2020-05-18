<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {
/***************************************** Start index page *****************************************/	
	public function index()
	{
		$this->load->view('template/index');
	}
/***************************************** End index page *****************************************/	

/***************************************** Start header page *****************************************/	
	private function head($data = '') 							// when we call footer, may we pass datas or not 
	{
		$this->load->view('template/header', $data);
	}
/***************************************** End header page *****************************************/	

/***************************************** Start footer page *****************************************/	
	private function foot($data = '') 							// when we call footer, may we pass datas or not 
	{
		$this->load->view('template/footer', $data);
	}
/***************************************** End footer page *****************************************/	

/***************************************** Start blog page *****************************************/	
	public function blog($data = '')
	{
		$this->head(); 											// call header (fixed)
		$data = array('title' => 'Simple Blog by HAKO'); 
		$this->load->view('template/body', $data); 				// call body of the page (changeable)
		$this->foot(); 											// call footer (fixed)
	} 
/***************************************** End blog page *****************************************/	

/***************************************** Start  page *****************************************/	
	public function test($data = '')
	{
		$this->load->library('parser');  						// call library parser
		$data = array( 											// variable for datas
			'blog_title' => 'My Blog Title HAKO',
			'blog_heading' => 'My Blog Heading HAKO'
		);

		$this->parser->parse('template/body', $data); 			// link to our template
	} 
/***************************************** End  page *****************************************/	

}
