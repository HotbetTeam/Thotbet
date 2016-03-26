<div style="width: 360px;margin: auto;background: #ffffff;padding: 30px;">
    <h3 style="color: #631306">สมัคร เอเย่นต์ </h3>
<?php

$f = new Form();
echo $f->create()
    
    // attr, options
    ->addClass('form-large')
    ->method('post')
    ->url(URL.'agent/register')

    // set field
 

    ->field("agent_email")
    	->label('Email* ')
        // ->placeholder("โทรศัพท์หรืออีเมล์")
         ->type('email')
        ->addClass('inputtext')
        ->required(true)
        ->autocomplete("off")
        ->value( !empty($this->post['agent_email'])? $this->post['agent_email'] : '' )
        ->notify( !empty($this->error['agent_email']) ? $this->error['agent_email'] : '' )

    ->field("agent_password")
    	->label('รหัสผ่าน*')
        ->type('password')
        ->required(true)
        ->addClass('inputtext')
         ->placeholder("รหัสผ่าน")
         ->value( !empty($this->post['agent_password'])? $this->post['agent_password'] : '' )
        ->notify( !empty($this->error['agent_password']) ? $this->error['agent_password'] : '' )

    ->hr( !empty($this->next)
            ? '<input type="hidden" autocomplete="off" value="'.$this->next .'" name="next">' 
            : '' 
    )
       ->field("agent_name")
        ->label('ชื่อ*')
        // ->placeholder("ชื่อ")
        ->addClass('inputtext')
        ->required(true)
          ->type('text')
        ->autocomplete("off")
        ->value( !empty($this->post['agent_name'])? $this->post['agent_name'] : '' )
        ->notify( !empty($this->error['agent_name']) ? $this->error['agent_name'] : '' )
    
     ->field("agent_tel")
        ->label('เบอร์โทร ')
        // ->placeholder("ชื่อ")
        ->addClass('inputtext')
        ->required(true)
          ->type('tel')
        ->autocomplete("off")
        ->value( !empty($this->post['agent_tel'])? $this->post['agent_tel'] : '' )
        ->notify( !empty($this->error['agent_tel']) ? $this->error['agent_tel'] : '' )

    ->submit()
        ->addClass('btn btn-yellow btn-submit btn-login')
        ->value('สมัครใช้งาน')
        
        ->html();

        
?>
</div>