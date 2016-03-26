<div class="wrapper clearfix login-1" id="login-1">
<header>
        <h1>Forgot your password?</h1>
</header>

<section>
		<form id="loginhelp_form" action="/ajax/account/forgotpassword" method="POST" novalidate="novalidate">
			<div>
				<aside>
					<i class="emaillogin forgot_login_70 massive"></i>
				</aside>
				<aside class="instructions">
					<p>Enter your email or username. We'll email instructions on how to reset your password.</p>
					<p>
					<strong>Need help?</strong> Learn more about how to <a href="https://myspace.zendesk.com/hc/en-us/sections/200421370-Log-in" target="_blank">retrieve an existing account.</a>
					</p>
				</aside>
				<fieldset class="errorContainer">
					<div class="control-group email">
						<div class="msRadio white">
							<input id="loginhelp_email" type="radio" name="remindoption" value="em" checked="">
							<label for="loginhelp_email"><span>Your email</span>
								<span class="helptip csstip">
	<b>?</b>
	
<span class="top   ">
	
	<span>
		The email you used to create your account.
		<span></span>
	</span>
</span>

</span>

							</label>
						</div>
						<div class="control">
							<input type="email" name="email" required="required" data-err-req-message="Please enter your email." data-err-type-message="Please enter a valid email.">
							<p class="tipHolder"></p>
						</div>
					</div>
					<div class="control-group username">
						<div class="msRadio white">
							<input id="loginhelp_username" type="radio" name="remindoption" value="un">
							<label for="loginhelp_username"><span>Username</span>
								<span class="helptip csstip">
	<b>?</b>
	
<span class="top   ">
	
	<span>
		The last part of your Myspace URL. Ex: myspace.com/<strong>username</strong>
		<span></span>
	</span>
</span>

</span>

							</label>
						</div>
						<div class="control">
							<input type="text" name="username" required="required" data-err-req-message="Please enter your username.">
							<p class="tipHolder"></p>
						</div>
					</div>

					<p class="genericError tipHolder"></p>
				</fieldset>
			</div>
			<footer class="formFooter">
				<button class="back" type="button">Back</button>
				<button class="primary" type="submit" form="loginhelp_form">Submit</button>
			</footer>
		</form>
    </section>

</div>