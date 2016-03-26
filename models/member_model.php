<?php

class Member_Model extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function insert(&$data) {

        $user = array(
            'name' => $data['name'],
            'email' => !empty($data['email'])? $data['email']: '',
            'phone_number' => !empty($data['phone_number'])? $data['phone_number']: '',
            'pass' => $data['pass'],
            'display' => 'enabled',
            'access_id' => 2
        );
    	$this->query('users')->insert($user);
        $data['user_id'] = $user['user_id'];

        $member = array(
            'user' => $data['m_user'],
            'pass' => $data['m_pass'],
            'status' => 'play',
            'user_id' => $user['user_id']
        );
    	$this->query('member')->insert($member);
    	$data['m_id'] = $member['m_id'];
    }
}