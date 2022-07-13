<?php  
defined("BASEPATH") OR exit("No direct script access allowed");

require_once APPPATH . 'libraries/API_Controller.php';

class Test extends API_Controller{

	public function __construct(){
		parent::__construct();
	}

	public function index()
	{
		 $this->_apiConfig([
            /**
             * By Default Request Method `GET`
             */
            'methods' => ['GET'], // 'GET', 'OPTIONS'

            /**
             * Number limit, type limit, time limit (last minute)
             */
            'limit' => [5, 'ip', 5],

          
        ]);
	}
	/**
	 * API Limit
	 * @link : api/v1/limit
	 * @return [type] [description]
	 */
	
	public function api_limit(){
		/**
		 * API Limit
		 * -----------------------
		 * @param {int} Api Limit Number
		 * @param {string} Api Limit Type (IP)
		 * @param {int} Api Limit Time (minute)
		*/
		
		$this->_apiConfig([
			'methods' => ['POST'],
			/**
			 * Number Limit, type limit, time limit (last minute)
			 */
			'limit' =>[5, 'ip', 'everyday'],
		]);
		$this->api_return(['status' => false, 'error' => 'Invalid data'], '200');

	}

	/**
	 * API Key Without Database
	 * 
	 */
	
	public function api_key(){
		/**
		 * Use API Key without Database
		 * ---------------------------------------------------------
		 * @param: {string} Types
		 * @param: {string} API Key
		 */

		$this->_APIConfig([
		    'key' => ['header', 'abiha#2412'],
		]);
	}

	/**
	 * Api Key With Database
	 * 
	 */
	public function api_keys(){
		/**
		 * API Key
		 * ---------------------------------------------------------
		 * @param: {string} Types
		 * @param: {string} [table]
		 */
		$this->_APIConfig([
		    'key' => ['header'], 
		    'data' => [ 'is_login' => false ] // custom data

		]);
	}

	/**
	 * Api with Custom Response
	 * 
	 */
	public function api_response(){
		/**
		 * API Key
		 * ---------------------------------------------------------
		 * @param: {string} Types
		 * @param: {string} [table]
		 */
		$this->_APIConfig([

		    'methods' => ['POST'],

		    // 'key' => ['header', 'table'],
		    // 'key' => ['header'], 
		    // Add Custom data in response
		    'data' => [ 'is_login' => false ] // custom data
		]);

		/**
		 * Return API Response
		 * ---------------------------------------------------------
		 * @param: API Data
		 * @param: Request Status Code
		 */
		// $data = array('status'=>'OK','data'=>['user_id'=>12]);
		$data = array();
		if(!empty($data)){
			$this->api_return($data, '200');
		}else{
			$this->api_return(['status' => false, 'error' => 'Invalid data'], '404');
		}
	}

	/**
     * login method 
     *
     * @link [api/v1/login]
     * @method POST
     * @return Response|void
     */
    public function login()
    {
        header("Access-Control-Allow-Origin: *");

        // API Configuration
        $this->_apiConfig([
            'methods' => ['POST'],
        ]);

        // you user authentication code will go here, you can compare the user with the database or whatever
        $payload = [
            'id' => "Your User's ID",
            'other' => "Some other data"
        ];

        // Load Authorization Library or Load in autoload config file
        $this->load->library('authorization_token');

        // generate a token
        $token = $this->authorization_token->generateToken($payload);

        // return data
        $this->api_return(
            [
                'status' => true,
                "result" => [
                    'token' => $token,
                ],
                
            ],
        200);
    }

    /**
     * view method
     *
     * @link [api/user/view]
     * @method POST
     * @return Response|void
     */
    public function view()
    {
        header("Access-Control-Allow-Origin: *");

        // API Configuration [Return Array: User Token Data]
        $user_data = $this->_apiConfig([
            'methods' => ['POST'],
            'requireAuthorization' => true,
        ]);

        // return data
        $this->api_return(
            [
                'status' => true,
                "result" => [
                    'user_data' => $user_data['token_data']
                ],
            ],
        200);
    }
}

// Request Status Code List
// Status Code	Status Text
// 200	OK
// 401	UNAUTHORIZED
// 404	NOT FOUND
// 408	Request Timeout
// 400	BAD REQUEST
// 405	Method Not Allowed
?>