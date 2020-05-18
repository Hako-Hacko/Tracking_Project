<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {
	
/***************************************** Start Constructer part *****************************************/	
// quand on appelle une laibrairie dans le constructeur, on aura pas besoin de l'appeler dans les functions.
	public function __construct()
	{
		parent::__construct();
		$this->load->library('session');				// call session library
		$this->load->helper('cookie');					// call cookie helper
		$this->load->model('db_data'); 					// loading model


	}
/***************************************** End Constructer part *****************************************/	

/***************************************** Start default page *****************************************/	
	public function index()
	{
		$user = $this->session->userdata('userAdmin'); 	// code to show data session
		$pass = $this->session->userdata('passAdmin'); 	// code to show data session

		echo "<h2>admin hako page</h2>" . '<br>';
		echo $user . '<br>';
		echo $pass . '<br>';
		//$this->load->view('welcome_message');
	}
/***************************************** End default page *****************************************/

/***************************************** Start login page *****************************************/
	public function login()
	{
		$this->load->helper('form');
		$this->load->view('admin/login');
	}
/**************************************** End login page *****************************************/

/***************************************** Start *****************************************/
	public function do_login() 
	{
		$data = array();
		$data = $this->db_data->select('private_admin'); 			// loading function exist in model

		$this->load->helper(array('form'));				// Invite helper of form_validation
		$this->load->library('form_validation');

		$this->form_validation->set_rules('username', 'Username', 'trim|required|min_length[4]|max_length[20]'); 	// set rule for our input username 
		$this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[4]');					// set rule for our input password
		
		if($this->form_validation->run() == FALSE) {
			$data['error'] = validation_errors(); 		// when input empty, this variable will show us the errors
		} else {

			$user = $this->input->post('username'); 	// username from post (form)
			$pass = $this->input->post('password'); 	// password from post (form)

			foreach($data as $key) { 					// foreach to extraire data from admin table in ddb
				$username = $key->username; 			// username from database
				$password = $key->password; 			// password from database
			}
			$remember = $this->input->post('remember');

			if($user == $username && $pass == $password) {
				$newdata = array(
					'userAdmin'		=> $username,
					'passAdmin'		=> $password,
					'logged_in'		=> TRUE
				);
				$this->session->set_userdata($newdata); // add data in session 

				// start set cookie (push button remember-me = send cookie file to navigator)
				if(!empty($remember)) {
					$cookie = array(
						'name'		=> 'Cookie',
						'value'		=> $username . '^' . $password, 
						'expire'	=> '86500'
					);
				set_cookie($cookie);
				}
				// End set cookie

				redirect('admin/index'); 				// redirect user to the main page
			} else {
				echo "Error";
			}
		}

		$this->load->view('admin/login', $data);

	}
/***************************************** End *****************************************/

/***************************************** Start checking data in session *****************************************/
	private function checkSession() 
	{
		$this->load->model('db_data'); 					// loading model
		$data = $this->db_data->selectFromDb(); 		// loading function exist in model

		foreach($data as $key) { 						// foreach to extraire data from admin table in ddb
			$username = $key->username; 				// username from database
			$password = $key->password; 				// password from database
		}

		$user = $this->session->userdata('userAdmin'); 	// code to show data session
		$pass = $this->session->userdata('passAdmin'); 	// code to show data session

		if(!empty($user) && !empty($pass)) {
			if($user !== $username || $pass !== $password) {
				redirect('admin/login');
			}
		} else {
			redirect('admin/login');
		}
	}
/***************************************** End checking data in session *****************************************/

/***************************************** Start page show articles *****************************************/
	public function viewArticle() 							// function to show articles
	{
		$this->checkCookie();
		$this->checkSession();
	}
/***************************************** End page show articles *****************************************/

/***************************************** Start page cookies *****************************************/
	private function checkCookie()
	{
		$this->load->model('db_data'); 						// loading model
		$data = $this->db_data->selectFromDb(); 			// loading function exist in model

		foreach($data as $key) { 							// foreach to extraire data from admin table in ddb
			$username = $key->username; 					// username from database
			$password = $key->password; 					// password from database
		}

		$cookie = get_cookie('Cookie'); 					// get data from cookie file using get_cookie function
		$check 	= explode('^', $cookie); 					// separate words when you find '^'
		$user 	= $check[0];
		$pass 	= $check[1];
		
		if(!empty($user) || !empty($pass)) { 				// pass from cookie file
			if($user !== $username || $pass !== $password) {
				redirect('admin/login');
			} else {
				$newdata = array( 							// give a new session with username & password values
					'userAdmin'		=> $username,
					'passAdmin'		=> $password,
					'logged_in'		=> TRUE
				);
				$this->session->set_userdata($newdata); 	// add data in session 
			}
		} else {
			redirect('admin/login');
		}
	}
/***************************************** End page cookies *****************************************/

/***************************************** Start page logout *****************************************/
	public function logout()  								// vérifier "check up" s'il y a une session ou pas, si oui, il va quitter la page (logout)
	{ 														// pas de session, il ne va pas exécuter le code. ne laisse pas sortir (logout)
		$this->checkSession();								/* call checkSession (if session exist, read the code below and logout)
		session for admin), (if there's no session, we can't access to this function "logout" by link)*/ 
		$data = $this->db_data->select('private_admin'); 	// loading function exist in model

		foreach($data as $key) { 							// foreach to extraire data from admin table in ddb
			$username = $key->username; 					// username from database
			$password = $key->password; 					// password from database
		}

		$user = $this->session->userdata('userAdmin'); 		// code to show data session
		$pass = $this->session->userdata('passAdmin'); 		// code to show data session

		if(empty($user) || empty($pass)) {
			redirect('admin/login');
		}
		if($user !== $username || $pass !== $password) {
			redirect('admin/login');
		}

		delete_cookie('Cookie'); 							// to delete the cookie
		$this->session->sess_destroy();						// code to delete session
		redirect('admin/login');
	}
/***************************************** End page logout *****************************************/

/***************************************** Start page test *****************************************/
	public function viewCat() // function to show catygories
	{
		$this->checkCookie(); 								// function to check that the session is for admin
		$this->checkSession();								// not for another person, that means we enter in this page in case that the sessino is for admin
	}
/***************************************** End page test *****************************************/

}
