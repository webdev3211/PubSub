
<?php

include('database_connection.php');
session_start();

$message = '';

if(isset($_SESSION['user_id'])){
    header('location:index.php');
}


if(isset($_POST['login'])){
    $query = "
    SELECT * FROM tbl_twitter_user
    WHERE username = :username
    ";
    $statement = $connect->prepare($query);
    $statement->execute(
        array(
            ':username' => $_POST['username']
        )
    );
    $count = $statement->rowCount();
    if($count > 0){
        $result = $statement->fetchAll();
        foreach($result as $row){
            if(password_verify($_POST["password"], $row["password"])){
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['username'] = $row['username'];
                header('location:index.php');

            }else{
                $message = '<label>Wrong Password</label>';
            }
        }
    }else{
        $message = '<label>Wrong Username</label>';
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
    <link href="https://fonts.googleapis.com/css?family=Satisfy" rel="stylesheet">
    
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    <style>

      body, html {
  height: 100%;
  margin: 0;
}  
.bg {
  /* The image used */
  background-image: url("https://i.ibb.co/WvWPgZC/img1.jpg");

  /* Full height */
  height: 100%; 

  /* Center and scale the image nicely */
  background-position: right;
  background-repeat: no-repeat;
}


#panelllll {
    box-shadow: 0 10px 16px 0 rgba(0, 0, 255, 0.2);
  }


        </style>

</head>

<body class="bg">  



        <div class="container">
   
<div class="row" style="margin-top: 100px;">
    <div class="col-md-6" >
    <div class="panel panel-default" id="panelllll">
      <div class="panel-heading" style="background:tomato; font-weight:bold; color: white; align: center;">Login</div>
    <div class="panel-body">
     <form method="post">
      <p class="text-danger"><?php echo $message; ?></p>
      <div class="form-group">
       <label>Enter Username</label>
       <input type="text" placeholder="john..." name="username" class="form-control" autocomplete="off" required />
      </div>
      <div class="form-group">
       <label>Enter Password</label>
       <input type="password" name="password" class="form-control" required />
      </div>
      <div class="form-group">
       <input type="submit" name="login" class="btn btn-info" value="Login" style="background: tomato;"/>
      </div>
      <div align="left" style="font-weight:bold;">
       <span class="text-muted">New Here ?</span> <a href="register.php">Register Now</a>
      </div>
     </form>
    </div>
   </div>
  
    </div>
    <div class="col-md-6">
        <div class="container-fluid text-center" >
 
        <span class="txt-type" style="font-family: Satisfy; font-weight: bold; font-size: 3.7em; color: tomato;" data-wait="3000" data-words='["Thinking   of   writing   something","Want   to   share   your   views",  "Connect   with   us!!"]'></span>

        </div>
    </div>
</div>

</div>
    </body>  


</html>

 <script>
    class TypeWriter {
      constructor(txtElement, words, wait = 3000) {
        this.txtElement = txtElement;
        this.words = words;
        this.txt = '';
        this.wordIndex = 0;
        this.wait = parseInt(wait, 10);
        this.type();
        this.isDeleting = false;
      }

      type() {
        // Current index of word
        const current = this.wordIndex % this.words.length;
        // Get full text of current word
        const fullTxt = this.words[current];

        // Check if deleting
        if (this.isDeleting) {
          // Remove char
          this.txt = fullTxt.substring(0, this.txt.length - 1);
        } else {
          // Add char
          this.txt = fullTxt.substring(0, this.txt.length + 1);
        }

        // Insert txt into element
        this.txtElement.innerHTML = `<span class="txt">${this.txt}</span>`;

        // Initial Type Speed
        let typeSpeed = 100;

        if (this.isDeleting) {
          typeSpeed /= 2;
        }

        // If word is complete
        if (!this.isDeleting && this.txt === fullTxt) {
          // Make pause at end
          typeSpeed = this.wait;
          // Set delete to true
          this.isDeleting = true;
        } else if (this.isDeleting && this.txt === '') {
          this.isDeleting = false;
          // Move to next word
          this.wordIndex++;
          // Pause before start typing
          typeSpeed = 500;
        }

        setTimeout(() => this.type(), typeSpeed);
      }
    }


    // Init On DOM Load
    document.addEventListener('DOMContentLoaded', init);

    // Init App
    function init() {
      const txtElement = document.querySelector('.txt-type');
      const words = JSON.parse(txtElement.getAttribute('data-words'));
      const wait = txtElement.getAttribute('data-wait');
      // Init TypeWriter
      new TypeWriter(txtElement, words, wait);
    }

  </script>