<script type="text/javascript">

	// alert( 1 );
	var myApp = window.opener.myApp;
	myApp.asyncCall(<?=json_encode($this->data)?>);

	window.close();
</script>