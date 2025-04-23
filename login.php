<?php include 'includes/session.php'; ?>
<?php
  if(isset($_SESSION['user'])){
    header('location: cart_view.php');
  }
?>
<?php include 'includes/header.php'; ?>

<style>
  body {
    background: linear-gradient(to right, #8360c3, #2ebf91);
    font-family: 'Arial', sans-serif;
  }
  .login-box {
    width: 360px;
    margin: 7% auto;
    padding: 30px;
    background: rgba(255, 255, 255, 0.9);
    border-radius: 10px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    text-align: center;
  }
  .login-box-body {
    text-align: center;
  }
  .login-box-msg {
    font-size: 18px;
    font-weight: bold;
    color: #333;
  }
  .form-control {
    border-radius: 20px;
  }
  .btn-primary {
    background: #2ebf91;
    border: none;
    border-radius: 20px;
    font-weight: bold;
    transition: 0.3s;
  }
  .btn-primary:hover {
    background: #1d9b74;
  }
  a {
    color: #8360c3;
    font-weight: bold;
  }
  a:hover {
    text-decoration: underline;
  }
  .logo {
    width: 150px;
    margin-bottom: 20px;
  }
</style>

<body class="hold-transition login-page">
<div class="login-box">
    <img src="img/ZAKKstore.png" alt="ZAKKstore Logo" class="logo">
   	<?php
      if(isset($_SESSION['error'])){
        echo "<div class='alert alert-danger text-center'>
                <p>".$_SESSION['error']."</p>
              </div>";
        unset($_SESSION['error']);
      }
      if(isset($_SESSION['success'])){
        echo "<div class='alert alert-success text-center'>
                <p>".$_SESSION['success']."</p>
              </div>";
        unset($_SESSION['success']);
      }
    ?>
   	<div class="login-box-body">
     	<p class="login-box-msg">Sign in to start your session</p>

     	<form action="verify.php" method="POST">
      		<div class="form-group has-feedback">
        		<input type="email" class="form-control" name="email" placeholder="Email" required>
        		<span class="glyphicon glyphicon-envelope form-control-feedback"></span>
      		</div>
          <div class="form-group has-feedback">
            <input type="password" class="form-control" name="password" placeholder="Password" required>
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
          </div>
      		<div class="row">
    			<div class="col-xs-12">
          			<button type="submit" class="btn btn-primary btn-block btn-flat" name="login"><i class="fa fa-sign-in"></i> Sign In</button>
        		</div>
      		</div>
     	</form>
      <br>
      <a href="password_forgot.php">I forgot my password</a><br>
      <a href="signup.php" class="text-center">Register a new membership</a><br>
      <a href="index.php"><i class="fa fa-home"></i> Home</a>
   	</div>
</div>

<?php include 'includes/scripts.php' ?>
</body>
</html>
