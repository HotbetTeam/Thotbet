<?php

class Operator_table extends Model {

    public function __construct() {
        parent::__construct();
    }

    public function lists($options = array()) {
        $options = array_merge(array(
            'pager' => isset($_REQUEST['pager']) ? $_REQUEST['pager'] : 1,
            'limit' => isset($_REQUEST['limit']) ? $_REQUEST['limit'] : 50,
            'time' => isset($_REQUEST['time']) ? $_REQUEST['time'] : time(),
            'q' => isset($_REQUEST['q']) ? $_REQUEST['q'] : null,
            'more' => true
                ), $options);
        $date = date('Y-m-d H:i:s', $options['time']);

        $sql = ' FROM operator  AS o LEFT JOIN users AS u ON u.user_id = op_user_id';
        $sth = $this->db->prepare('SELECT count(*) ' . $sql);
        $sth->execute();

        $arr['total'] = $sth->fetchColumn();

        $limit = $this->limited($options['limit'], $options['pager']);
        $arr['lists'] = $this->buildFrag($this->db->select("SELECT * 
            $sql ORDER BY op_id DESC  {$limit}", array(':time' => $date)
        ));

        if (($options['pager'] * $options['limit']) >= $arr['total']) {
            $options['more'] = false;
        }

        $arr['options'] = $options;

        // print_r($arr); die;

        return $arr;
    }

    public function buildFrag($results) {
        $data = array();
        foreach ($results as $key => $value) {
            if (empty($value))
                continue;
            $data[] = $this->convert($value);
        }

        return $data;
    }

    public function get($id) {

        $sth = $this->db->prepare("SELECT * 
            FROM operator  AS o LEFT JOIN users AS u ON u.user_id = op_user_id
            WHERE o.op_id = :id 
            LIMIT 1");
        $sth->execute(array(
            ':id' => $id
        ));

        $result = $sth->fetch(PDO::FETCH_ASSOC);
        if (!empty($result)) {
            $result = $this->convert($result);
        }
        
        return $result;
    }

    public function convert($data) {

        $data['url'] = URL . 'operator/' . $data['op_id'];
        return $data;
    }

    public function getCoverImage($id) {
        $sth = $this->db->prepare("SELECT image_cover as name, image_cover_type as type
            FROM topics WHERE topic_id=:id LIMIT 1");
        $sth->execute(array(
            ':id' => $id
        ));

        $data = array();
        if ($sth->rowCount() == 1) {
            $data = $sth->fetch(PDO::FETCH_ASSOC);

            $dest = $data['type'] == 'jpg' ? WWW_IMAGES_PRODUCTS . $data['name'] . "n.jpg" : WWW_IMAGES_PRODUCTS . $data['name'] . "o.{$data['type']}";

            if (file_exists($dest)) {

                $data['url'] = $data['type'] == 'jpg' ? IMAGES_PRODUCTS . $data['name'] . "n.jpg" : IMAGES_PRODUCTS . $data['name'] . "o.{$data['type']}";
            } else {
                $data = array();
            }
        }

        return $data;
    }

    public function category($id, $options = array()) {
        $options = array_merge(array(
            'pager' => isset($_REQUEST['pager']) ? $_REQUEST['pager'] : 1,
            'limit' => isset($_REQUEST['limit']) ? $_REQUEST['limit'] : 12,
            'time' => isset($_REQUEST['time']) ? $_REQUEST['time'] : time(),
            'more' => true
                ), $options);
        $date = date('Y-m-d H:i:s', $options['time']);

        $sth = $this->db->prepare("SELECT COUNT(t.topic_id) 
            FROM (topics t LEFT JOIN posts p ON t.topic_id=p.topic_id AND p.post_sequence=0)
            INNER JOIN forums f ON t.forum_id=f.forum_id
            WHERE p.created<=:time AND t.forum_id=:id");
        $sth->execute(array(
            ':time' => $date,
            ':id' => $id
        ));
        $arr['total'] = $sth->fetchColumn();

        $limit = $this->limited($options['limit'], $options['pager']);
        $arr['lists'] = $this->buildFrag($this->db->select("SELECT * 
            FROM (topics t LEFT JOIN posts p ON t.topic_id=p.topic_id AND p.post_sequence=0)
            INNER JOIN forums f ON t.forum_id=f.forum_id
            WHERE p.created<=:time AND t.forum_id=:id
            ORDER BY p.created DESC {$limit}", array(':time' => $date, ':id' => $id)
        ));

        if (($options['pager'] * $options['limit']) >= $arr['total']) {
            $options['more'] = false;
        }

        $arr['options'] = $options;
        return $arr;
    }

}
