<?php
    
class Insights_Table extends Model {

	private $current = null;
	private $data = null;
	private $username = 'u_online';
	private $sep = '^^'; // characters used to separate the user name and date-time
	private $vst_id = '-vst-'; // an identifier to know that it is a visitor, not logged user

    public function __construct(){
        parent::__construct();

        // set 
        $this->dry = WWW_VIEW. "Layouts". DS . "online.json";

        $this->notPath = array("error","", "manage","agent", "register", "login");
        $this->path = array( "place", 'food', 'topic' );

        $this->getData();
    }

    public function set(){
    	$key = $this->IP().$this->vst_id.$this->sep.$this->time;
    	// echo "NEW: {$key}<br>";
    	$this->setData( $key );
    	
		Session::init();
		Session::set($this->username, $key);
    }

    public function get($current='', $action='view', $id=null, $timeon=null) {
        
    	if( in_array($current, $this->notPath) ){
    		// echo 'Not current'; die;
    		return false;
    	}

    	$this->current = (in_array($current, $this->path) && !empty($id))
    		? $current
    		: "main";

    	$this->action = $action;
    	$this->id = $id;
    	$this->time = time();

    	// number of secconds to keep a user online
    	$this->timeon = !empty($timeon) ? $timeon: $this->getTimeon( $this->current, $this->action );

    	Session::init();
    	$online = Session::get($this->username);
        
    	// echo "KEY: {$online}<br>";

    	if( !isset($online) ){
    		
    		$this->set();
    	}
    	else{
			if( $this->dataTimeout($online)==false ){

				// Remove All Timeout
				// echo "Remove All Timeout: {$online}</br>";
				unset($this->data[$online]);

				$this->updateData();
				Session::destroy( $this->username );

				// set new sesstion
				$this->set();
			}
			else{

				// echo "Update timeout : {$online}</br>";
				$is_update = false;
				if( !empty($this->id) && empty($this->data[$online][$this->current][$this->action][$this->id]) ){
                    $is_update = true;
                }elseif( empty($this->id) && empty($this->data[$online][$this->current][$this->action] ) ){
                    $is_update = true;
                }
				else{
					$timeout = $this->data[$online][$this->current][$this->action];
					if( !empty($this->id) ){
                        $timeout = $timeout[$this->id];
                    }

					$is_update = $this->timeout( $timeout );
				}

				if( $is_update ){
					// update timeout 
					// echo "update timeout {$this->current}=>{$this->action}</br>";
					$this->setData($online);
				}
			}
    	}

    	// 
    	foreach ($this->data as $key => $value) {

    		if( $this->dataTimeout( $key )==false ){
    			unset($this->data[$key]);
    			$this->updateData();
    		}
    	}

        
        $insights = $this->db->select("SELECT count FROM insights WHERE obj_type=:type AND obj_id=:id", array(':type'=>'main', ':id'=>1));

        $all = 0;
        foreach ($insights as $key => $value) {
            $all += intval($value['count']);
        }

        $data = array(
            'now' =>  count($this->data),
            'all' => $all,
            'share' => 0
        );

        if( !empty($id) ){
            $data['current_view'] = $this->db->count("insights", "obj_type=:type AND obj_id=:id AND action='view'", array(':type'=>$current, ':id'=>$id));
        }

        return $data;

        

    	// $online = Session::get($this->username);
    	// echo "Online: {$online}<br>";
    	/*echo $current."-".$this->current;

    	echo '<pre>';
		print_r($this->data);
		echo '</pre>';

    	die;*/
    }

    public function dataTimeout($key='')
    {
    	$is_timeout = false;
    	if( !empty($this->data[$key]) ){

	    	foreach ($this->data[$key] as $obj => $actions) {

				foreach ($actions as $action => $timeout) {
					if( is_array($timeout) ){
						foreach ($timeout as $ids => $time) {
							
							if( !$this->timeout($time) ){
								$is_timeout = true;
							}
							// echo "155{$obj}=>{$action}=>{$ids}<br>";
						}
					}
					elseif( !$this->timeout($timeout) ){
						$is_timeout = true;
						// echo "121{$obj}=>{$action}<br>";
					}
				}
			}
		}

		return $is_timeout;
    }

    public function setData($key)
    {
    	
    	if( !empty($this->id) ){
    		$this->data[$key][$this->current][$this->action][$this->id] = $this->time;
    	}
    	else{
    		$this->data[$key][$this->current][$this->action] = $this->time;
    	}


    	$data = array(
			'date' => date("Y-m-d"),
            'obj_type' => $this->current,
            'obj_id' => !empty($this->id) ? $this->id: 1,
            'action' => $this->action,
            'count'=>1
    	);

    	// Update To Database
    	$sth = $this->db->prepare("SELECT insights_id as id,count FROM insights WHERE date=:d AND obj_type=:type AND obj_id=:id AND action=:action LIMIT 1");
        $sth->execute(array(
            ':d' => $data['date'],
            ':type' => $data['obj_type'],
            ':id' => $data['obj_id'],
            ':action' => $data['action']
        ));

        if( $sth->rowCount()==0 ){
        	// insert
        	$this->db->insert("insights", $data);
        }
        else{
        	// update
        	$fdata = $sth->fetch( PDO::FETCH_ASSOC );
        	$data = array( 'count' => intval($fdata['count'])+1 );
        	$this->db->update("insights", $data, "`insights_id`={$fdata['id']}");
        }
       	
    	// echo "Update To Database: {$key}</br>";

    	// Update To File
    	$this->updateData();
    }

    public function getData()
    {

    	if( file_exists($this->dry) ){
    		$file = fopen($this->dry, "r");
			$string = fread($file,filesize($this->dry));
			fclose($file);
			
			$this->data = json_decode($string,true);
    	}
    	else{
    		$this->data = array();
    	}
    }

    public function updateData()
    {
    	file_put_contents($this->dry, json_encode($this->data));
		// 'Error: Recording file not exists, or is not writable';
    }

    public function timeout($time='', $timeon=null)
    {
    	$timeon = !empty( $timeon ) ? $timeon:$this->timeon;
    	return (intval($time)+$timeon ) <= $this->time ? true: false;
    }

    private function generateRandomString($length = 10) {
    	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    	$charactersLength = strlen($characters);
    	$randomString = '';
	    for ($i = 0; $i < $length; $i++) {
	        $randomString .= $characters[rand(0, $charactersLength - 1)];
	    }
    	return $randomString;
	}

	private function IP()
	{
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		
		return $ip;
	}

	public function getTimeon($key='main', $action='view')
    {
    	if( $key=='main'){
    		$timeon = 60*5;
    	}
    	else{
    		switch ($action) {
    			case 'view':
    				$timeon = 60*2;
    				break;
    			
    			default:
    				$timeon = 30;
    				break;
    		}
    	}

    	return $timeon;
    }

}