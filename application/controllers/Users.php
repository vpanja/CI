<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * User Management class created by CodexWorld
 */
class Users extends CI_Controller {
    
    function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('user');
    }
    
public function index()
{
        $this->load->view('users/login');
}
        
    /*
     * User account information
     */
    public function account(){
        $data = array();
        if($this->session->userdata('isUserLoggedIn')){
            $data['user'] = $this->user->getRows(array('id'=>$this->session->userdata('userId')));
            //load the view
            $this->load->view('users/account', $data);
        }else{
            redirect('users/login');
        }
    }
    
    /*
     * User login
     */
    public function login(){
        $data = array();
        if($this->session->userdata('success_msg')){
            $data['success_msg'] = $this->session->userdata('success_msg');
            $this->session->unset_userdata('success_msg');
        }
        if($this->session->userdata('error_msg')){
            $data['error_msg'] = $this->session->userdata('error_msg');
            $this->session->unset_userdata('error_msg');
        }
        if($this->input->post('loginSubmit') ){
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
            $this->form_validation->set_rules('password', 'password', 'required');
            if ($this->form_validation->run() == true) {
                $con['returnType'] = 'single';
                $con['conditions'] = array(
                    'user_email'=>$this->input->post('email'),
                    'user_pass' => md5($this->input->post('password'))
                    /*,
                    'status' => '1'*/
                );
//                $checkLogin = $this->user->getRows($con);
//                if($checkLogin){
//                    $this->session->set_userdata('isUserLoggedIn',TRUE);
//                    $this->session->set_userdata('userId',$checkLogin['ID']);
//                    redirect('users/account');
//                }else{
//                    $data['error_msg'] = 'Wrong email or password, please try again.';                     
//                }
                $this->checkLogin($con);
            }
        }else{
                  $this->load->view('users/login', $data);
        }
    }
    
    /*
     * User registration
     */
    public function registration(){
        $data = array();
        $userData = array();
        if($this->input->post('regisSubmit')){
            $this->form_validation->set_rules('name', 'Name', 'required');
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email|callback_email_check');
            $this->form_validation->set_rules('password', 'password', 'required');
            $this->form_validation->set_rules('conf_password', 'confirm password', 'required|matches[password]');

            $userData = array(
                'user_name' => strip_tags($this->input->post('name')),
                'user_email' => strip_tags($this->input->post('email')),
                'user_pass' => md5($this->input->post('password'))
                /*,
                'gender' => $this->input->post('gender'),
                'phone' => strip_tags($this->input->post('phone'))*/
            );

            if($this->form_validation->run() == true){
                $insert = $this->user->insert($userData);
                if($insert){
                    $this->session->set_userdata('success_msg', 'Your registration was successfully. Please login to your account.');
                    redirect('users/login');
                }else{
                    $data['error_msg'] = 'Some problems occured, please try again.';
                }
            }
        }
        $data['user'] = $userData;
        //load the view
        $this->load->view('users/registration', $data);
    }
    
    /*
     * User logout
     */
    public function logout(){
        $this->session->unset_userdata('isUserLoggedIn');
        $this->session->unset_userdata('userId');
        $this->session->sess_destroy();
        redirect('users/login');
    }
    
    /*
     * Existing email check during validation
     */
    public function email_check($str){
        $con['returnType'] = 'count';
        $con['conditions'] = array('user_email'=>$str);
        $checkEmail = $this->user->getRows($con);
        if($checkEmail > 0){
            $this->form_validation->set_message('email_check', 'The given email already exists.');
            return FALSE;
        } else {
            return TRUE;
        }
    }
    
    /*
     * Existing email check during validation
     */
    public function gmailLogin(){
//        $this->load->library('googleapi');
        $client = new Google_Client();
        $client->setAuthConfigFile(base_url()."assets/client_secrets.json");
        $client->setRedirectUri( base_url(). 'users/gmailLogin');
        $client->addScope(array(Google_Service_Oauth2::USERINFO_PROFILE,Google_Service_Oauth2::USERINFO_EMAIL,Google_Service_YouTube::YOUTUBE_UPLOAD,Google_Service_YouTube::YOUTUBE_FORCE_SSL));
        if (! isset($_GET['code'])) {
            $auth_url = $client->createAuthUrl();
            header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
          }else {
            $client->authenticate($_GET['code']);
            if($client->getAccessToken()){
                $this->session->set_userdata('access_token',$client->getAccessToken());
                $client->setAccessToken($this->session->userdata('access_token'));
                $profileService = new Google_Service_Oauth2($client);
                $profileDetails = $profileService->userinfo->get();
                $userInfo = (array)$profileDetails;
                $videos = $this->getUserVideos($client);
                $userFullDetails = array_merge($userInfo,array('videoslist'=>$videos));
                if(isset($videos[0]['id']['channelId'])){
                    $userFullDetails['channel_id']=$videos[0]['id']['channelId'];
                }
                  
//                                            echo '<pre>';print_r($userFullDetails);exit;       
                $con['returnType'] = 'single';
                $con['loginType'] = 'Google';
                $con['conditions'] = array(
                    'user_email'=>$userFullDetails['email'],
                    'user_pass' => md5('stellar'.$userFullDetails['id'])
                    );  
                $this->checkLogin($con,$userFullDetails);
            }
          }       
    }
    
    function checkLogin($data,$userDetails=null){
        $checkLogin = $this->user->getRows($data);
        if($checkLogin){
            $this->session->set_userdata('isUserLoggedIn',TRUE);
            $this->session->set_userdata('userId',$checkLogin['ID']);
            redirect('users/account');
        }elseif($data['loginType']=='Google'){
            $newUser = array(
                'user_name'=>$userDetails['name'],
                    'user_email'=>$userDetails['email'],
                    'user_pass' => md5('stellar'.$userDetails['id'])
                    );
            
            $insert = $this->user->insert($newUser);
            $newSocialUser = array(
                'linked_social_app'=>$data['loginType'],
                'linked_email'=>$userDetails['email'],
                'social_username' => $userDetails['name'],
                'user_id' => $insert,
                'identifier'=>$userDetails['id']
                    );
            $insertsocial = $this->user->insertSocial($newSocialUser);
            if($insert){  
                $this->session->set_userdata('isUserLoggedIn',TRUE);
                $this->session->set_userdata('userId',$insert);
                $userDetails['userId']=$insert;
                $this->addVideos($userDetails);             
                redirect('users/account');
            }
        }else{
           $message['error_msg'] = 'Wrong email or password, please try again.';   
           $this->load->view('users/login', $message);
        }
    }

    function getUserVideos($client){
        $videoService = new Google_Service_YouTube($client);
        $params = array('mine'=>1);
        $channels = $videoService->channels->listChannels('id',$params)->getChannelDetails();
        foreach($channels as $cId){
            $videos = $videoService->search->listSearch('id',array('channelId'=>$cId['id']))->getChannelItems();
        }
        return $videos;
    }
    function showUserVideos(){
        if($this->session->userdata('access_token') || $this->session->userdata('userId')){
            $data =array();
            $user_id=$this->session->userdata('userId');
            $userVideos = $this->user->showVideos($user_id);
//            echo '<pre>';print_r($userVideos);exit;
            if($userVideos){
                $data['userVideos']=$userVideos;
            }else{
                $data['video_message']="No Videos";
            }
            $this->load->view('users/videos', $data);
        }
    }
    function fetchVideos(){
        $videos=array();
        $client = new Google_Client();
        $client->setAccessToken($this->session->userdata('access_token'));
        $videos['videoslist'] = $this->getUserVideos($client);
        $videos['userId']=$this->session->userdata('userId');
        if(isset($videos['videoslist'][0]['id']['channelId'])){
            $videos['channel_id']=$videos['videoslist'][0]['id']['channelId'];
        }
        $this->addVideos($videos);
        redirect('users/showUserVideos');
    }
    
    function addVideos($userDetails){
        foreach($userDetails['videoslist'] as $videos){
            if(isset($videos['id']['videoId'])){
                $videoDetails = array(
                    'user_id'=>$userDetails['userId'],
                    'channel_id'=>$userDetails['channel_id'],
                    'video_id'=>$videos['id']['videoId'],
                    'created_at'=> date("Y-m-d H:i:s")
                );
                $insertVideos = $this->user->insertVideos($videoDetails);
            }
        }  
    }
}