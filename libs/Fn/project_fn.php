<?php

class Project_Fn extends _function{

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

	public function header($options=null)
	{
		
		$action_str = '';

		// show: status
		$action_str .='<li><span class="fcg mrs">โครงงาน</span><span class="uiToggle">
			<a href="#" rel="toggle" aria-expanded="true"><span>ล่าสุด</span><i class="icon-caret-down mls"></i></a>
			<div class="uiToggleFlyout">
				<ul class="uiMenu" role="menu">
					<li class="menuItem"><a class="itemAnchor" href="#"><span class="itemLabel">ล่าสุด</span></a></li>
					<li class="menuItem"><a class="itemAnchor" href="#"><span class="itemLabel">ของฉัน</span></a></li>
				</ul>
			</div>
		</span></li>';


		// 
		$action_str .= '<li class="r group-btn">'._function::actionPager($options['pager']).'</li>';

		// button create
		if(!empty($options['create'])){
			$action_str .= '<li class="r btn btn-red"><a class="btn-link" href="<?=URL?>projects/create"><i class="icon-plus img mrs"></i><span class="btn-text">เสนอหัวข้อโครงงาน</span></a></li>';
		}
		
				
		/*<!-- <li class="r group-btn"><span class="mlm mrs fcg">รูปแบบสตรีม</span><a class="btn" href="#"><i class="icon-th"></i><i class=""></i></a><a class="btn active" href="#"><i class="icon-align-justify"></i><i class=""></i></a></li> -->*/
			
		$action_str = '<ul class="tab-actions clearfix">'.$action_str.'</ul>';

		return '<header class="headerPage">'.$action_str.'</header>';
	}

	public function listbox( $result=array(), $me=null ){

		$item = "";
		foreach ($result as $key => $value) {

			# members
			$members_html = "";
			$members = $this->members( $value['members'], "admin" );
			if( count($members)>0 ){
				foreach ($members as $i => $user) {

					if( $i==4 ) break;
						
					if( count($members)==1 ){
						$members_html.='<div class="anchor anchor32"><div class="clearfix"><div class="avatar lfloat size32">'._function::avatar($user['avatar'], 32).'</div><div class="content"><div class="spacer"></div><div class="massages clearfix"><div>'.$user['fullname'].'</div></div></div></div></div>';
					}
					else{
						$members_html.='<a class="avatar size32">'._function::avatar($user['avatar'], 32).'</a>';
					}

				}

				if( count($members) > 5){
					$members_html.='<a class="avatar size32"><div class="avatarPlus"><span>+'.( count($members)-4 ).'</span></div></a>';
				}
			}

			$oddeven = $key%2? "even":"odd";

			$item .= '<tr class="'.$oddeven.'">'.
				#star
				// '<td class="star"><a><i class="icon-star-empty"></i></a></td>'.

				#Title
				'<td class="name">'.
					'<a class="fullname" href="'.URL.'projects/about/'.$value['project_id'].'">'.$value['name_TH'].' ('.$value['name_us'].')</a>'.
				'</td>'.

				#member
				'<td class="member"><div>'. $members_html .'</div></td>'.

				# Updated
				'<td class="date"><div class="fsm">'.$this->q('time')->live($value['updated']).'</div></td>'.
			'</tr>';

		}

		# Head
		$head = '<div class="js-table-title"><table class="root_table" cellspacing="0"><thead><tr>'.
			#star
			// '<th class="star"></th>'.

			#Title
			'<th class="name">ชื่อโครงงาน</th>'.

			#member
			'<th class="member">ผู้จัดทำโครงงาน</th>'.

			# Updated
			'<th class="date">แก้ไขล่าสุด</th>'.
		'</tr></thead></table></div>';

		$item = '<div class="js-table-list"><table class="root_table" cellspacing="0"><tbody>'.$item.'</tbody></table></div>';

		$html = '<section class="doclist">'.$head.$item.'</section>';

		if( $this->_outer ){
			return $html;
		}
		else{
			return ($this->_data[$this->_currentItem]['display'] == "item")
				? $item
				: $html;
		}

	}

	public function members($results, $is_role=false)
	{
		$members = array();
		if($is_role){
			foreach ($results as $key => $value) {
				
				if($value['role_id']===$is_role){
					$members[] = $value;
				}
					
				
			}
		}else{
			$members = $results;
		}

		return $members;
	}

}