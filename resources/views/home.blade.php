
<!DOCTYPE html>
<html>
<head>
	<!-- Basic Page Info -->
	<meta charset="utf-8">
	<title>School Asist</title>

	<!-- Site favicon -->
	<!-- Site favicon -->
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}">
<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16.png') }}">


	<!-- Mobile Specific Metas -->
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

	<!-- Google Font -->
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
	<!-- CSS -->
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
    @vite('resources/css/app.css')
    @vite('resources/css/icon-font.min.css')
    @vite('resources/css/style.css')
    @vite('resources/js/app.js')
    @vite('resources/js/script.min.js')
    @vite('resources/js/process.js')
    @vite('resources/js/show.js')


	<!-- Global site tag (gtag.js) - Google Analytics -->
	<!--<script async src="https://www.googletagmanager.com/gtag/js?id=UA-119386393-1"></script>
	<script>
		window.dataLayer = window.dataLayer || [];
		function gtag(){dataLayer.push(arguments);}
		gtag('js', new Date());

		gtag('config', 'UA-119386393-1');
	</script> -->
</head>
<body class="login-page">
	<div class="login-header box-shadow">
		<div class="container-fluid d-flex justify-content-between align-items-center">
			<div class="brand-logo">
				
            <img src="{{ asset('images/schaxist.png') }}" alt="Example Image">
				
			</div>
		</div>
	</div>
	<div class="login-wrap d-flex align-items-center flex-wrap justify-content-center">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6 col-lg-7">
                <img src="{{ asset('images/login-page-img.png') }}" alt="login Image">
            </div>
            <div class="col-md-6 col-lg-5">
                <div class="login-box bg-white box-shadow border-radius-10">
                
                    
                    <!-- Tab content -->
                    <div class="tab-content">
                        <!-- Staff Login Form -->
                        <div id="staff-login" class="tab-pane fade show active">
                            <div class="login-title">
                                <h2 class="text-center text-primary">User Login</h2>
                            </div>
                            <form name="signin_staff" method="post">
                                <div class="input-group custom">
                                    <input type="text" class="form-control form-control-lg" placeholder="Email ID" name="username" id="username">
                                    <div class="input-group-append custom">
                                        <span class="input-group-text"><i class="icon-copy fa fa-envelope-o" aria-hidden="true"></i></span>
                                    </div>
                                </div>
                                <div class="input-group custom">
                                    <input type="password" class="form-control form-control-lg" placeholder="**********" name="password" id="password">
                                    <div class="input-group-append custom">
                                        <span class="input-group-text"><i class="material-icons visibility" onclick="show()">visibility</i></span>
                                    </div>
                                </div>
                                <div class="row pb-30">
                                    <div class="col-6">
                                        <div class="forgot-password"><a href="forgot-password.html">Forgot Password</a></div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="input-group mb-0">
                                            <button class="btn btn-primary btn-lg btn-block" id="signinBtn">Sign In</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Senior Login Form -->
                        
                    </div>
                    <!-- End Tab content -->
                </div>
            </div>
        </div>
    </div>
</div>

	<!-- js -->
</body>
<script>
        $(document).ready(function() {
            $('#signinBtn').on('click', function(event) {
    event.preventDefault(); // Prevent the default form submission

    var username = $('#username').val();
    var password = $('#password').val();

    // Check if the inputs are empty
    if (username === '') {
        showMessage('Please enter your email.', true);
        return;
    }
    if (password === '') {
        showMessage('Please enter your password.', true);
        return;
    }

    $.ajax({
        url: 'auth.php', // URL to your PHP login script
        type: 'POST',
        data: {
            username: username,
            password: password
        },
        success: function(response) {
            var data = JSON.parse(response);
            if (data.success) {
                showMessage('Login successful! Redirecting...', false);
                // Redirect based on user role
                window.location.href = data.redirect_url;
            } else {
                // Show error message from the server
                showMessage(data.message || 'Invalid username or password.', true);
            }
        },
        error: function() {
            showMessage('Error occurred during login. Please try again.', true);
        }
    });
});
});


        function showMessage(message, isError) {
            let messageDiv = $('#messageDiv');
            if (messageDiv.length === 0) {
                messageDiv = $('<div id="messageDiv"></div>');
                $('body').append(messageDiv);
            }
            
            messageDiv.text(message);
            messageDiv.css({
                'position': 'fixed',
                'top': '20px',
                'left': '20px',
                'padding': '15px',
                'border-radius': '5px',
                'color': 'white',
                'z-index': '1051',
                'display': 'block'
            });
            
            if (isError) {
                messageDiv.css('background-color', '#f44336'); // Red for errors
            } else {
                messageDiv.css('background-color', '#4CAF50'); // Green for success
            }
            
            setTimeout(() => {
                messageDiv.hide();
            }, 3000); // Hide the message after 3 seconds
        }
    </script>

</html>