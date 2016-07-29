  function statusChangeCallback(response) {
	if (response.status === 'connected') {
      // Logged into your app and Facebook.
		  _i();
    } else if (response.status === 'not_authorized') {
      // The person is logged into Facebook, but not your app.
		document.getElementById('status').innerHTML = 'Please log into this app.';
    }
  }  

  // This function is called when someone finishes with the Login
  // Button.  See the onlogin handler attached to it in the sample
  // code below.
  function checkLoginState() {
    FB.getLoginStatus(function(response) {
	  statusChangeCallback(response);
    });
  }

  var id_button;
  
  window.fbAsyncInit = function() {
  FB.init({
    appId      : '1418052968220483',
    cookie     : true,  // enable cookies to allow the server to access the session
    xfbml      : true,  // parse social plugins on this page
    version    : 'v2.7' // use version 2.2
  });
  
  
	$('#login_with_fb').on('click', function(){
		id_button = 'login_with_fb';
	});
	
	$('#register_with_fb').on('click', function(){
		id_button = 'register_with_fb';
	});

  // Now that we've initialized the JavaScript SDK, we call 
  // FB.getLoginStatus().  This function gets the state of the
  // person visiting this page and can return one of three states to
  // the callback you provide.  They can be:
  //
  // 1. Logged into your app ('connected')
  // 2. Logged into Facebook, but not your app ('not_authorized')
  // 3. Not logged into Facebook and can't tell if they are logged into
  //    your app or not.
  //
  // These three cases are handled in the callback function.

  /*
  FB.getLoginStatus(function(response) {
alert('d');    statusChangeCallback(response);
  });
*/
  };

  // Load the SDK asynchronously
  (function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/en_US/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
  }(document, 'script', 'facebook-jssdk'));

  // Here we run a very simple test of the Graph API after login is
  // successful.  See statusChangeCallback() for when this call is made.
  function _i(id_button) {
    //console.log('Welcome!  Fetching your information.... ');
    FB.api('/me',
			'GET',
		  {"fields":"first_name,last_name,email"},
		  function(response) {
			//$("#nome").val(response.first_name);
			//$("#cognome").val(response.last_name);
			//$("#email").val(response.email);			
			if (id_button == 'register_with_fb') {
				$.post( '', { register_with_fb: 'register_with_fb', fb_userid: response.id, user: response.email, nome: response.first_name, cognome: response.last_name, email: response.email }, function(data) {
					if ($( data ).find("#registered_with_fb")) {
					  id_button = 'login_with_fb';
					  _i(id_button);
                    }
				});
			}
			
			if (id_button == 'login_with_fb') {
				$.post( '', { login: 'login', login_with_fb: 'login_with_fb', user: response.email, nome: response.first_name, cognome: response.last_name, email: response.email, verify_email: response.email }, function(data) {
					if ($( data ).find("#logged_with_fb")) {
					   location.reload();
					}
				});	
			}
			
		  });
  }
  
	function _login() {
		FB.login(function(response) {
		   // handle the response
		   if(response.status==='connected') {
			_i(id_button);
		   }
		 }, {scope: 'public_profile,email'});
	 }