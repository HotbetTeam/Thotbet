<?php
	
class Places_Table extends Model {
	
	public function __construct() {
		parent::__construct();
	}

    public function topfive(){
        
        return $this->buildFrag( $this->db->select("SELECT * FROM places ORDER BY place_star DESC, created DESC LIMIT 0,5" ) );
    }

    public function lists($options=array()){
        $options = array_merge(array(
            'pager' => isset($_REQUEST['pager'])? $_REQUEST['pager']:1,
            'limit' => isset($_REQUEST['limit'])? $_REQUEST['limit']:12,
            'time'=> isset($_REQUEST['time'])? $_REQUEST['time']:time(),
            'q' => isset($_REQUEST['q'])? $_REQUEST['q']:null,
            'more' => true
        ), $options);
        $date = date('Y-m-d H:i:s', $options['time']);

        $arr['total'] = $this->db->count('places', "created<=:time", array(':time'=>$date));

        $limit = $this->limited( $options['limit'], $options['pager'] );
        $arr['lists'] = $this->buildFrag( $this->db->select("SELECT * FROM places WHERE created<=:time ORDER BY created DESC {$limit}", array(':time'=>$date) ) );

        if( ($options['pager']*$options['limit']) >= $arr['total'] ) {
            $options['more'] = false;
        }

        $arr['options'] = $options;

        return $arr;
    }
    public function buildFrag($results){
        $data = array();
        foreach ($results as $key => $value) {
            if( empty($value) ) continue;
            $data[] = $this->convert($value);
        } 

        return $data;
    }

    public function get($id){
        $sth = $this->db->prepare("SELECT * FROM places WHERE place_id=:id LIMIT 1");
        $sth->execute( array(
            ':id' => $id
        ) );

        $result = $sth->fetch( PDO::FETCH_ASSOC );
        if( !empty($result) ){
            $result = $this->convert($result);
        }
        return $result;
    }

    public function convert($data) {
        
        $data['url'] = URL.'places/'.$data['place_id'];
        $data['category'] = $this->db->select("SELECT place_category_id,place_category_name FROM place_category c LEFT JOIN places_category_permit p ON c.place_category_id=p.category_id WHERE p.place_id=:id", array(':id'=>$data['place_id']));

        $data['place_category_name_str'] = '';
        foreach ($data['category'] as $key => $value) {

            $data['place_category_name_str'] .= !empty($data['place_category_name_str'])? ', ': '';
            $data['place_category_name_str'] .='<a href="'.URL.'places/category/'.$value['place_category_id'].'">'.$value['place_category_name'].'</a>';
        }

        $data['place_image_cover'] = $this->getCoverImage($data['place_id']);
        if(!empty($data['place_image_cover'])){
            $data['place_image_cover_url'] = $data['place_image_cover']['url'];
        }

        $data['photos'] = $this->getImage( $data['place_id'] );
        $data['menu_count'] = $this->db->count("foods", "place_id=:id", array(':id'=>$data['place_id']));


        $theDateItem = strtotime($data['created']);
        $data['create_str'] = date('d', $theDateItem) . ' / ' . date('m', $theDateItem). ' / '.date('Y', $theDateItem);
        $data['visited'] = $this->db->count("insights", "obj_type=:type AND obj_id=:id AND action=:action", array(':type'=>'place', ':id'=>$data['place_id'], ':action'=>'view'));

        $data['menu'] = $this->getMenu($data['place_id']);
        // print_r( $data ); die;
        return $data;
    }
    public function convertItem($result){

        $data = array(
            'id' => $result['place_id'],
            'url' => $result['url'],
            'name' => $result['place_name'],
            'detail' => $result['place_detail'],
        );

        return $data;
    }

    // Category
    public function listsCategory( $options=array() ){
    
        $options = array_merge(array(
            'pager' => isset($_REQUEST['pager'])? $_REQUEST['pager']:1,
            'limit' => isset($_REQUEST['limit'])? $_REQUEST['limit']:12,
            'time'=> isset($_REQUEST['time'])? $_REQUEST['time']:time(),
            'q' => isset($_REQUEST['q'])? $_REQUEST['q']:null,
            'more' => true
        ), $options);
        $date = date('Y-m-d H:i:s', $options['time']);

        $arr['total'] = $this->db->count('place_category', "created<=:time", array(':time'=>$date));

        $arr['lists'] = $this->db->select("SELECT * FROM place_category WHERE created<=:time ORDER BY created DESC", array(':time'=>$date) );

        if( ($options['pager']*$options['limit']) >= $arr['total'] ) {
            $options['more'] = false;
        }

        $arr['options'] = $options;

        return $arr;
    }
    public function getCategory($id){
        $sth = $this->db->prepare("SELECT * FROM place_category WHERE place_category_id=:id LIMIT 1");
        $sth->execute( array(
            ':id' => $id
        ) );

        return $sth->fetch( PDO::FETCH_ASSOC );
    }

    public function getImage( $id, $options=array() ){
        $options = array_merge(array(
            'pager' => isset($_REQUEST['pager'])? $_REQUEST['pager']:1,
            'limit' => isset($_REQUEST['limit'])? $_REQUEST['limit']:12,
            'time'=> isset($_REQUEST['time'])? $_REQUEST['time']:time(),
            'more' => true
        ), $options);
        // $date = date('Y-m-d H:i:s', $options['time']);

        $arr['total'] = $this->db->count('places_photos', "place_id=:id", array(':id'=>$id) );
        $arr['lists'] = $this->buildFragImage( $this->db->select("SELECT * FROM places_photos WHERE place_id=:id ORDER BY photo_id DESC", array(':id'=>$id) ) );

        if( ($options['pager']*$options['limit']) >= $arr['total'] ) {
            $options['more'] = false;
        }

        $arr['options'] = $options;
        return $arr;
    }
    public function buildFragImage($results){
        $data = array();
        foreach ($results as $key => $value) {
            if( empty($value) ) continue;
            $data[] = $this->convertImage($value);
        } 

        return $data;
    }
    public function convertImage( $result ) {
        $result['url'] = IMAGES_PRODUCTS.$result['photo_name'];

        /*$dest = WWW_IMAGES_PRODUCTS.$result['photo_name'];
        if( file_exists($dest) ){
            list($result['width'], $result['height']) = getimagesize($dest);
        }*/

        return $result;
    }

    public function getCoverImage($id){
        $sth = $this->db->prepare("SELECT place_image_cover as name
            FROM places WHERE place_id=:id LIMIT 1");
        $sth->execute( array(
            ':id' => $id
        ) );

        $data = array();
        if( $sth->rowCount()==1 ){
            $fdata = $sth->fetch( PDO::FETCH_ASSOC );
            if( $fdata['name']!="" ){
                $data = $fdata;
                $data['type'] = 'jpg';
                $data['url'] = IMAGES_PRODUCTS.$data['name'].'n.jpg';
                // $data['url_o'] = IMAGES_PRODUCTS.$data['name'].'o.jpg';
            }
        }

        return $data;
    }

    public function getMenu($id, $options=array()){
        $options = array_merge(array(
            'pager' => isset($_REQUEST['pager'])? $_REQUEST['pager']:1,
            'limit' => isset($_REQUEST['limit'])? $_REQUEST['limit']:12,
            'time'=> isset($_REQUEST['time'])? $_REQUEST['time']:time(),
            'q' => isset($_REQUEST['q'])? $_REQUEST['q']:null,
            'more' => true
        ), $options);
        $date = date('Y-m-d H:i:s', $options['time']);

        $arr['total'] = $this->db->count('foods', "food_created<=:time AND place_id=:id", array(':time'=>$date, ':id'=>$id));

        $limit = $this->limited( $options['limit'], $options['pager'] );
        $arr['lists'] = $this->query('foods')->buildFrag( $this->db->select("SELECT 
            food_id, food_name, food_updated, food_created, food_image_cover,food_star,food_detail
            , p.place_id, p.place_name
            , c.food_category_id, c.food_category_name
            , r.country_id, r.country_name
            FROM foods f 
                LEFT JOIN places p ON f.place_id=p.place_id
                LEFT JOIN food_category c ON f.food_category_id=c.food_category_id
                LEFT JOIN country r ON f.food_country_id=r.country_id
            WHERE food_created<=:time AND f.place_id=:id ORDER BY food_created DESC {$limit}", array(':time'=>$date, ':id'=>$id) ) );

        if( ($options['pager']*$options['limit']) >= $arr['total'] ) {
            $options['more'] = false;
        }

        $arr['options'] = $options;

        return $arr;
    }

    public function search($options=array()){
        $options = array_merge(array(
            'pager' => isset($_REQUEST['pager'])? $_REQUEST['pager']:1,
            'limit' => isset($_REQUEST['limit'])? $_REQUEST['limit']:12,
            'time'=> isset($_REQUEST['time'])? $_REQUEST['time']:time(),
            'q' => isset($_REQUEST['q'])? $_REQUEST['q']:null,
            'more' => true
        ), $options);
        $date = date('Y-m-d H:i:s', $options['time']);

        $arr['total'] = $this->db->count('places', "created<=:time AND food_name LIKE :begin", array(':time'=>$date, ":begin"=>"{$options['q']}%"));

        $limit = $this->limited( $options['limit'], $options['pager'] );
        $arr['lists'] = $this->buildFrag( $this->db->select("SELECT * FROM places WHERE created<=:time AND place_name LIKE :begin ORDER BY created DESC {$limit}", array(':time'=>$date, ":begin"=>"{$options['q']}%") ) );

        if( ($options['pager']*$options['limit']) >= $arr['total'] ) {
            $options['more'] = false;
        }

        $arr['options'] = $options;

        return $arr;
    }
}