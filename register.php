<?php

include('database_connection.php');

session_start();

$message = '';

if(isset($_SESSION['user_id'])){
    header('location:index.php');
}

if(isset($_POST['register'])){
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $check_query = "
    SELECT * FROM tbl_twitter_user
    WHERE username = :username
    ";
    $statement = $connect->prepare($check_query);
    $check_data = array(
        ':username' => $username
    );
    if($statement->execute($check_data)){
        if($statement->rowCount() > 0){
            $message .= 
            '<p><label>Username Already taken</label></p>';
        }else{
            if(empty($username)){
                $message .= 
                '<p><label>Username is required</label></p>';
    
            }
            if(empty($password)){
                $message .= 
                '<p><label>Password is required</label></p>';
    
            }
            else{
                if($password != $_POST['confirm_password']){
                        $message .= 
                        '<p><label>
                        Password does not match
                        </label></p>';                   
                }
            }
            if($message == ''){
                $data = array(
                    ':username' => $username,
                    ':password' => password_hash($password, PASSWORD_DEFAULT)
                );

                $query = "
                INSERT INTO tbl_twitter_user
                (username, password)
                VALUES (:username, :password)
                ";

                $statement = $connect->prepare($query);
                if($statement->execute($data)){
                    $message .= 
                    '<p><label>
Registration Completed
                    </label></p>'; 
                }
            }
            
        }
    }
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PubSub</title>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<style>
.bg {
  /* The image used */
  background-image: url("https://i.ibb.co/mJd8wh3/img2.jpg");

  /* Full height */
  height: 100%; 

  /* Center and scale the image nicely */
  background-position: right;
  background-repeat: no-repeat;
  background-size: cover;
}

#panelllll {
    box-shadow: 0 10px 16px 0 rgba(0, 0, 255, 0.2);
  }

</style>
</head>

<body>  
        <div class="container">
   <br />
   <br />
   <div class="row" style="margin-top: 100px;">   
   <div class="col-md-6">
   
   <div class="panel panel-default" id="panelllll">
   <div class="panel-heading" style="background:tomato; font-weight:bold; color: white; align: center;">Register</div>

    <div class="panel-body">
     <form method="post">
      <span class="text-danger">
      <?php echo $message; ?>
      </span>
      <div class="form-group">
       <label>Enter Username</label>
       <input type="text" name="username" class="form-control" />
      </div>
      <div class="form-group">
       <label>Enter Password</label>
       <input type="password" name="password" id="password" class="form-control" />
      </div>
      <div class="form-group">
       <label>Re-enter Password</label>
       <input type="password" name="confirm_password" id="confirm_password" class="form-control" />
      </div>
      <div class="form-group">
       <input type="submit" name="register" class="btn btn-info" value="Register" style="background: tomato;" />
      </div>
      <div align="center" style="font-weight:bold;">
      <span class="text-muted">Already Registered ?</span> <a href="login.php">Login Here</a>
    
       
      </div>
     </form>
    </div>
   </div>

    </div>
  

   <div class="col-md-6">
        <img src="https://i.ibb.co/41cLHTF/img2.jpg" style="float: right; height:300px; width: 300px;">
    </div>

  </div>
    </body>  

</html>