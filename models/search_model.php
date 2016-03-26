<?php

class Search_Model extends Model {
    public function __construct() {
        parent::__construct();
    }
    public function results($objects, $q='', $options=array()){

    	$results = array();
    	$total = 0;
    	$options = array_merge(array(), $options);
    	// if( !empty($q) ){
    	foreach ($objects as $key => $object) {

    		if( in_array($key, array('type_food', 'type_place')) ){
    			$result = $this->{$key}($options);
    		}
    		else{
    			$result = $this->query($key)->search($options);
    		}
    		$total += $result['total'];
    		
    		if(!empty($result)){
    			$results[] = array(
		            'object_type'=>$object['type'],
		            'object_name'=>$object['name'],
		            'total' => $result['total'],
		            'data'=>$this->convert($object['type'],$result['lists'])
		        );
    		}
    	}

        // }


        // print_r($results); die;
    	return array(
    		'total' => $total,
    		'options' => $options,
    		'lists' => $results
    	);
    }

    public function convert($object_type, $result){
    	
    	$data = array();
    	
		foreach ($result as $key => $value) {

	        if( $object_type =="foods")
	        {
	        	$id = $value['food_id'];
	            $text = $value['food_name'];
	            // $username = $value['username'];
	            $subtext = "ประเภท{$value['food_category_name']}";
	            $category = $value['food_star'];
	            $image_url = $value['food_image_cover_url'];
	            $url = $value['url'];
	        }
	        elseif( $object_type =="places" )
	        {

	        	$category = '';
	        	foreach ($value['category'] as $i => $item) {
	        		$category .= !empty($category)? ', ':'';
	        		$category .= $item['place_category_name'];
	        	}
	        	$id = $value['place_id'];
	            $text = $value['place_name'];
	            // $username = $value['username'];
	            $subtext = "ประเภท: {$category}";
	            $category = $value['place_star'];
	            $image_url = $value['place_image_cover_url'];
	            $url = $value['url'];
	        }
	        elseif( $object_type =="type_food" )
	        {
	        	$id = $value['id'];
	            $text = $value['name'];
	            $url = URL."foods/category/{$value['id']}";
	        }
	        elseif( $object_type =="type_place" )
	        {
	        	$id = $value['id'];
	            $text = $value['name'];
	            $url = URL."places/category/{$value['id']}";
	        }
	        

	        $data[] = array(
	        	'id'=>$id,
	        	'username'=>isset($username)?$username:"",
	            'text'=>$text,
	            "url"=> isset($url)?$url:"",
	            "category"=>isset($category)?$category:"",
	            "subtext"=>isset($subtext)?$subtext:"",
	            "image_url"=>isset($image_url)?$image_url:""
	        );
	    }

        return $data;
    }

    public function type_food($options=array()){
    	$options = array_merge(array(
            'pager' => isset($_REQUEST['pager'])? $_REQUEST['pager']:1,
            'limit' => isset($_REQUEST['limit'])? $_REQUEST['limit']:12,
            'time'=> isset($_REQUEST['time'])? $_REQUEST['time']:time(),
            'q' => isset($_REQUEST['q'])? $_REQUEST['q']:null,
            'more' => true
        ), $options);
        $date = date('Y-m-d H:i:s', $options['time']);

        if( !empty($options['q']) ){
	        $arr['total'] = $this->db->count('food_category','food_category_name LIKE :begin',
	        	array(
	            ":begin"=>"{$options['q']}%"
	        ));

	        $limit = $this->limited( $options['limit'], $options['pager'] );
	        $arr['lists'] = $this->db->select("SELECT food_category_id as id,food_category_name as name 
	            FROM food_category 
	            WHERE food_category_name LIKE :begin {$limit}", 
	            array(":begin"=>"{$options['q']}%")
	        );
        }
        else{
        	$arr['total'] = $this->db->count('food_category');

        	$limit = $this->limited( $options['limit'], $options['pager'] );
	        $arr['lists'] = $this->db->select("SELECT food_category_id as id,food_category_name as name FROM food_category {$limit}");
	        
        }

        if( ($options['pager']*$options['limit']) >= $arr['total'] ) {
            $options['more'] = false;
        }

        $arr['options'] = $options;
        return $arr;
    }

    public function type_place($options=array()){
    	 $options = array_merge(array(
            'pager' => isset($_REQUEST['pager'])? $_REQUEST['pager']:1,
            'limit' => isset($_REQUEST['limit'])? $_REQUEST['limit']:12,
            'time'=> isset($_REQUEST['time'])? $_REQUEST['time']:time(),
            'q' => isset($_REQUEST['q'])? $_REQUEST['q']:null,
            'more' => true
        ), $options);
        $date = date('Y-m-d H:i:s', $options['time']);

        $sth = $this->db->prepare("SELECT COUNT(*) FROM place_category WHERE place_category_name LIKE :begin");
        $sth->execute( array(
            ":begin"=>"{$options['q']}%"
        ) );
        $arr['total'] = $sth->fetchColumn();

        if( !empty($options['q']) ){
	        $arr['total'] = $this->db->count('place_category','place_category_name LIKE :begin',
	        	array(
	            ":begin"=>"{$options['q']}%"
	        ));

	        $limit = $this->limited( $options['limit'], $options['pager'] );
	        $arr['lists'] = $this->db->select("SELECT place_category_id as id,place_category_name as name 
	            FROM place_category 
	            WHERE place_category_name LIKE :begin {$limit}", 
	            array(":begin"=>"{$options['q']}%")
	        );
        }
        else{
        	$arr['total'] = $this->db->count('place_category');

        	$limit = $this->limited( $options['limit'], $options['pager'] );
	        $arr['lists'] = $this->db->select("SELECT place_category_id as id,place_category_name as name FROM place_category {$limit}");
	        
        }

        if( ($options['pager']*$options['limit']) >= $arr['total'] ) {
            $options['more'] = false;
        }

        $arr['options'] = $options;
        return $arr;
    }

}