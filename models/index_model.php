<?php

class Index_Model extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function latest(){
    	$places = $this->query('places')->lists(array('limit'=>8));

    	$timeline = array();
    	foreach ($places['lists'] as $key => $value) {
    		$theDateItem = strtotime($value['created']);
    		
    		$timeline[$value['created']] = array(
    			'url' => $value['url'],
    			'name' => 'ร้าน '.$value['place_name'],
    			'image_url' => $value['place_image_cover_url'],
    			'created_str' => date('d', $theDateItem) . ' / ' . date('m', $theDateItem). ' / '.date('Y', $theDateItem),
    			'visited' => $value['visited'],
    			'short_str' => $this->fn->q('text')->more($value['place_detail'])
    		);
    	}

        $foods = $this->query('foods')->lists(array('limit'=>8));
        foreach ($foods['lists'] as $key => $value) {
            $theDateItem = strtotime($value['food_created']);
            
            $timeline[$value['food_created']] = array(
                'url' => $value['url'],
                'name' => $value['food_name'],
                'image_url' => $value['food_image_cover_url'],
                'created_str' => date('d', $theDateItem) . ' / ' . date('m', $theDateItem). ' / '.date('Y', $theDateItem),
                'visited' => $value['visited'],
                'short_str' => $this->fn->q('text')->more($value['food_detail'])
            );
        }

        krsort($timeline);
    	// print_r($places); die;
    	

    	return $timeline;
    }

    public function topics(){
        $data = $this->query('topics')->lists(array('limit'=>8));
        // print_r($data); die;
        return $data['lists'];
    }

    public function food_category(){
        return $this->db->select("SELECT food_category_id as id,food_category_name as name FROM food_category");
    }

    public function place_category(){
        return $this->db->select("SELECT place_category_id as id,place_category_name as name FROM place_category");
    }
}