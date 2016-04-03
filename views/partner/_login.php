<div style="width: 360px;margin: auto;background: #ffffff;padding: 30px;">
    <h3 style="color: #631306">ล๊อกสำหรับเอเย่นต์</h3>
<?php

$f = new Form();
echo $f->create()
    
    // attr, options
    ->addClass('form-large')
    ->method('post')
    ->url(URL.'agent/login')

    // set field
 

    ->field("agent_email")
    	->label('อีเมล')
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
      ->notify( !empty($this->error['error']) ? $this->error['error'] : '' )
    ->submit()
        ->addClass('btn btn-yellow btn-submit btn-login')
        ->value('Agent Login ')
          
        ->html();

        
?>
</div>