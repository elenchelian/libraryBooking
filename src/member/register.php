<?php
	require "../db_connect.php";
	require "../message_display.php";
	require "../header.php";
?>

<html>
	<head>
		<title>Register</title>
		<link rel="stylesheet" type="text/css" href="../css/global_styles.css">
		<link rel="stylesheet" type="text/css" href="../css/form_styles.css">
		<link rel="stylesheet" href="css/register_style.css">
	</head>
	<body>
		<form class="cd-form" method="POST"  onsubmit="return CheckPassword()">
			<legend>Enter your details</legend>

				<div class="error-message" id="error-message">
					<p id="error"></p>
				</div>

				<div class="icon">
					<input class="m-user" type="text" name="m_user" id="m_user" placeholder="Username" required />
				</div>

				<div class="icon">
					<input class="m-pass" type="password" name="m_pass" id="m_pass" placeholder="Password" required />
				</div>

				<div class="icon">
					<input class="m-name" type="text" name="m_name" placeholder="Full Name" required />
				</div>

				<div class="icon">
					<input class="m-email" type="email" name="m_email" id="m_email" placeholder="Email" required />
				</div>

				<div class="icon">
					<input class="m-balance" type="number" name="m_balance" id="m_balance" placeholder="Initial Balance" required />
				</div>

				<br />
				<input type="submit" name="m_register" value="Register" />
		</form>

		<script>
		function CheckPassword()
		{
			var passw = document.getElementById('m_pass').value;
			// var passw2 = document.getElementById('confirm_password').value;
			var upper  =/[A-Z]/;
			var number = /[0-9]/;

			if(passw.length < 8 || passw.length > 20 || !number.test(passw) || !upper.test(passw)) {
				if(passw.length<8){
					alert("Please make sure password is longer than 8 characters.")
					return false;
				}
				if(passw.length>20){
					alert("Please make sure password is shorter than 20 characters.")
					return false;
				}
				if(!number.test(passw)){
					alert("Please make sure password includes a digit")
					return false;
				}
				if(!upper.test(passw)) {
					alert("Please make sure password includes an uppercase letter.")
					return false;
				}
			}
			else
				alert("Password is strong");
		}
		</script>

	</body>

	<?php
		if(isset($_POST['m_register']))
		{
			if($_POST['m_balance'] < 500)
				echo error_with_field("You need a balance of at least 500 to open an account", "m_balance");
			else
			{
				$query = $con->prepare("(SELECT username FROM member WHERE username = ?) UNION (SELECT username FROM pending_registrations WHERE username = ?);");
				$query->bind_param("ss", $_POST['m_user'], $_POST['m_user']);
				$query->execute();
				if(mysqli_num_rows($query->get_result()) != 0)
					echo error_with_field("The username you entered is already taken", "m_user");
				else
				{
					$query = $con->prepare("(SELECT email FROM member WHERE email = ?) UNION (SELECT email FROM pending_registrations WHERE email = ?);");
					$getemail = $_POST['m_email'];
					$query->bind_param("ss", $getemail, $getemail);
					$query->execute();
					if(mysqli_num_rows($query->get_result()) != 0)
						echo error_with_field("An account is already registered with that email", "m_email");
					else
					{
						$query = $con->prepare("INSERT INTO pending_registrations(username, password, name, email, balance) VALUES(?, ?, ?, ?, ?);");
						$getuser=$_POST['m_user'];
						$getemail = $_POST['m_email'];
						$getpass=sha1($_POST['m_pass']);
						$getname=$_POST['m_name'];
						$getbalance=$_POST['m_balance'];

						$query->bind_param("ssssd",$getuser, $getpass,$getname,$getemail,$getbalance);
						if($query->execute())
							echo success("Details recorded. You will be notified on the email ID provided when your details have been verified");
						else
							echo error_without_field("Couldn\'t record details. Please try again later");
					}
				}
			}
		}
	?>

</html>
