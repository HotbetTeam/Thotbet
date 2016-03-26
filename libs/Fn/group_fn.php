<?php

class Group_Fn extends _function
{
	/* 
		options = {
			display: The item, Full item(Default)
		}
	*/
	private $_options = null;

	private $_currentItem = null;
	private $_outer = true;
	private $_data = null;

	public function _config($current, $result){
		$this->_outer = false;
		$this->_currentItem = $current;
		$this->_data[$this->_currentItem]['display'] = "full";
		$this->_data[$this->_currentItem]['result'] = $result;

		return $this;
	}

	public function display($display){
		$this->_data[$this->_currentItem]['display'] = $display;
		return $this;
	}

	// return: type html
	public function html(){
		return $this->{$this->_currentItem}( $this->_data[$this->_currentItem]['result'] );
	}


	public function listbox( $result=array() ){
		$item = "";
        foreach ($result as $key => $value) {

            $avatar = "";
            if($value['members_count']>0){
                $avatar = isset($value['members'][0]['avatar'])? $value['members'][0]['avatar'] :"";
            }
            
            $item .= '<li class="listGroupsItem" data-group-id="'.$value['group_id'].'">'.
            	'<label class="checkbox hidden_elem"><input type="checkbox" autocomplete="off" class="hiddenInput" name="group_id[]" value="'.$value['group_id'].'"></label>'.
                '<div class="avatarContent"><div class="avatar">'._function::avatar($avatar, 160).'</div></div>'.
                '<div class="content">'.
                    '<h3>'.$value['group_name'].'</h3>'.
                    '<div class="detel fsm fcg">สมาชิก '.$value['members_count'].' คน</div>'.
                '</div>'.
            '</li>';
        }

        return '<div id="pagelet_groups_listBox"><ul class="listGroups listBox">'.$item.'</ul></div>';
	}

	// public
	public function listbox_user($result=null){
		$item = "";
		if(!$result){
			$btnEmpty = "";
			$item .='<li class="listItemEmpty emptyAccount"><div class="textEmpty">ไม่มีสมาชิก!</div>'.$btnEmpty.'</li>';
		}else{

		    foreach ($result as $key => $value) {

		        $control = '<div class="actions">'.
		            '<a class="action_checked js-checkmark"><i class="icon-tumblr-checkmark lfloat"></i></a>'.
		        '</div>';

		        
		        $disabled = "";
		        if(isset($value['display'])){
		        	$disabled = $value['display']=="disabled"? " disabled":"";
		        }


		        $item .= '<li class="listItem uid_'.$value['user_id'].$disabled.'" data-user-id="'.$value['user_id'].'" data-group-id="'.$value['group_id'].'">'.

		            '<div class="casingTop"></div><div class="casingRight"></div><div class="casingBottom"></div><div class="casingLeft"></div>'.

		            '<div class="clearfix">'.
		            	'<div class="avatar lfloat">'._function::avatar($value['avatar'], 80).'</div>'.
		                '<div class="content"><div class="spacer"></div>'.
		                    '<div class="messages">'.
		                    	// '<div class="header">'.
		                            '<div class="fullname">'.$value['fullname'].'</div>'.
		                        // '</div>'.
		                        '<div class="fwn fcg">'.$value['username'].'</div>'.
		                        // '<div class="fwn fcg">กลุ่ม'.$value['group_name'].'</div>'.
		                   	'</div>'.
		                        
		                '</div>'.
		            '</div>'.
		            
		            '<div class="noit-status hidden_elem" title="สถานะ: ปิดการใช้งาน"><i class="img icon-lock"></i><span class="text">สถานะ: ปิดการใช้งาน</span></div>'.
		            
		            $control.

		        '</li>';
		    } // loop for result

		}// if result

		if( $this->_outer ){
			return '<ul class="uiListAccounts listGroupMember" role="listbox">'.$item.'</ul>';
		}
		else{
			return ($this->_data[$this->_currentItem]['display'] == "item")
				? $item
				: '<ul class="uiListAccounts listGroupMember" role="listbox">'.$item.'</ul>';
		}

	}
}