
<?php

include('database_connection.php');

session_start();

if(isset($_POST['action'])){
    $output = '';
    if($_POST['action'] == 'insert'){
        
    $data = array(
        ':user_id' => $_SESSION['user_id'],
        ':post_content' => $_POST['post_content'],
        ':post_datetime' => date("Y-m-d").' '.date("
            H:i:s", STRTOTIME(date('h:i:sa')))
        
    );

    $query = "
    INSERT INTO tbl_samples_post
    (user_id, post_content, post_datetime)
    VALUES (:user_id, :post_content, :post_datetime)
    ";
    
    $statement = $connect->prepare($query);
    $statement->execute($data);

    }

    if($_POST['action'] == 'fetch_post'){
        $user = $_SESSION["user_id"];
        $query = "  SELECT * FROM tbl_samples_post 
        INNER JOIN tbl_twitter_user ON tbl_twitter_user.user_id = tbl_samples_post.user_id 
        LEFT JOIN tbl_follow ON tbl_follow.sender_id = tbl_samples_post.user_id 
        WHERE tbl_follow.receiver_id = '".$_SESSION["user_id"]."' OR tbl_samples_post.user_id = '".$_SESSION["user_id"]."' 
        GROUP BY tbl_samples_post.post_id 
        ORDER BY tbl_samples_post.post_id DESC
  ";
        $statement = $connect->prepare($query);
  $statement->execute();
  $result = $statement->fetchAll();
  $total_row = $statement->rowCount();
//   echo $total_row;
  if($total_row > 0)
  {
   foreach($result as $row)
   {
    $profile_image = '';
    if($row['profile_image'] != '')
    {
     $profile_image = '<img src="images/'.$row["profile_image"].'" class="img-thumbnail img-responsive" style="border-radius: 50%;" />';
    }
    else
    {
     $profile_image = '<img src="images/user.png" class="img-thumbnail img-responsive" style="border-radius: 50%;"/>';
    }

    $repost = 'disabled';
    if($row['user_id'] != $_SESSION['user_id'])
    {
     $repost = '';
    }

    $output .= '
    <div class="jumbotron" style="padding:24px 30px 24px 30px">
    <div class="row">
        <div class="col-md-2">'.$profile_image.'
        </div>
        <div class="col-md-8">
        <h3><b>@'.$row["username"].'</b></h3>

        <p>'.$row["post_content"].'
       <button type="button" class="btn btn-link post_comment" id="'.$row["post_id"].'" data-user_id="'.$row["user_id"].'">'.count_comment($connect, $row["post_id"]).' Comment</button>
       <button type="button" class="btn btn-danger repost" data-post_id="'.$row["post_id"].'" '.$repost.'><span class="glyphicon glyphicon-retweet"></span>&nbsp;&nbsp;'.count_retweet($connect, $row["post_id"]).'</button>

       </p>
       <div id="comment_form'.$row["post_id"].'" style="display:none;">
        <span id="old_comment'.$row["post_id"].'"></span>
        <div class="form-group">
         <textarea name="comment" class="form-control" id="comment'.$row["post_id"].'"></textarea>
        </div>
        <div class="form-group" align="right">
         <button type="button" name="submit_comment" class="btn btn-primary btn-xs submit_comment">Comment</button>
        </div>
       </div>
                  </div>
                </div>
               </div>';
            }
        }
        else{
            $output = '<h4>No Post Found</h4>';
        }
        echo $output;
    }

   

    if($_POST['action'] == 'fetch_user')
    {
     $query = "
     SELECT * FROM tbl_twitter_user 
     WHERE user_id != '".$_SESSION["user_id"]."' 
     ORDER BY RAND()
     LIMIT 15
     ";
     $statement = $connect->prepare($query);
     $statement->execute();
     $result = $statement->fetchAll();
     $total_row = $statement->rowCount();
        // echo $total_row;
        foreach($result as $row){
            $profile_image = '';
            if($row['profile_image'] != '')
            {
             $profile_image = '<img src="images/'.$row["profile_image"].'" class="img-thumbnail img-responsive" />';
            }
            else
            {
             $profile_image = '<img src="images/user.png" class="img-thumbnail img-responsive" />';
            }
            $output .= '
            <div class="row">
             <div class="col-md-4">
              '.$profile_image.'
             </div>
             <div class="col-md-8">
              <h4><b>@'.$row["username"].'</b></h4>
              '.make_follow_button($connect, $row["user_id"], $_SESSION["user_id"]).'
              <span class="label label-success"> '.$row["follower_number"].' Followers</span>
             </div>
            </div>
            <hr />
            ';
        }
        echo $output;
    
    }

    if($_POST['action'] == 'follow'){
        $query = "
        INSERT INTO tbl_follow
        (sender_id, receiver_id)
        VALUES ('".$_POST['sender_id']."', '".$_SESSION["user_id"]."') ";

        $statement = $connect->prepare($query);
        if($statement->execute()){
            $sub_query = "
   UPDATE tbl_twitter_user SET follower_number = follower_number + 1 WHERE user_id = '".$_POST["sender_id"]."'
   ";
   $statement = $connect->prepare($sub_query);
   $statement->execute();
        }
    }


    if($_POST['action'] == 'unfollow')
    {
     $query = "
     DELETE FROM tbl_follow 
     WHERE sender_id = '".$_POST["sender_id"]."' 
     AND receiver_id = '".$_SESSION["user_id"]."'
     ";
     $statement = $connect->prepare($query);
     if($statement->execute())
     {
      $sub_query = "
      UPDATE tbl_twitter_user 
      SET follower_number = follower_number - 1 
      WHERE user_id = '".$_POST["sender_id"]."'
      ";
      $statement = $connect->prepare($sub_query);
      $statement->execute();
     }
    }

    if($_POST['action'] == 'submit_comment'){
        $data = array(
            ':post_id' => $_POST['post_id'],
            ':user_id' => $_SESSION['user_id'],
            ':comment' => $_POST['comment'],
            ':timestamp' => date("Y-m-d") . ' ' . date("H:i:s", STRTOTIME(date('h:i:sa')))
        );

        $query = "
        INSERT INTO tbl_comment 
        (post_id, user_id, comment, timestamp) 
        VALUES (:post_id, :user_id, :comment, :timestamp)
        ";
        $statement = $connect->prepare($query);
        $statement->execute($data);
    }

   
    if($_POST["action"] == "fetch_comment"){
  $query = "SELECT * FROM tbl_comment 
  INNER JOIN tbl_twitter_user 
  ON tbl_twitter_user.user_id = tbl_comment.user_id 
  WHERE post_id = '".$_POST["post_id"]."' 
  ORDER BY comment_id ASC
  ";
  $statement = $connect->prepare($query);
  $output = '';
  if($statement->execute())
  {
   $result = $statement->fetchAll();
   foreach($result as $row)
   {
    $profile_image = '';
    if($row['profile_image'] != '')
    {
     $profile_image = '<img src="images/'.$row["profile_image"].'" class="img-thumbnail img-responsive img-circle" />';
    }
    else
    {
     $profile_image = '<img src="images/user.png" class="img-thumbnail img-responsive img-circle" />';
    }
    $output .= '
    <div class="row">
     <div class="col-md-2">
     '.$profile_image.' 
     </div>
     <div class="col-md-10" style="margin-top:16px; padding-left:0">
      <small><b>@'.$row["username"].'</b><br />
      '.$row["comment"].'
      </small>
     </div>
    </div>
    <br />
    ';
   }
  }
  echo $output;
 }

 if($_POST['action'] == 'repost'){
     $query = "
     SELECT * FROM tbl_repost
     WHERE post_id = '".$_POST['post_id']."'
     AND user_id = '".$_SESSION['user_id']."'
     ";
     $statement = $connect->prepare($query);
     $statement->execute();
     $total_row = $statement->rowCount();
     if ($total_row > 0){
        echo 'You have already repost this post';
     }else{
        $query1 = "
        INSERT INTO tbl_repost
        (post_id, user_id)
        VALUES('".$_POST['post_id']."', '".$_SESSION['user_id']."')
        ";
        $statement = $connect->prepare($query1);
        if($statement->execute()){
            $query2 = 
            "SELECT * FROM tbl_samples_post
            WHERE post_id = '".$_POST['post_id']."'
            ";
            $statement = $connect->prepare($query2);
            if($statement->execute()){
                $result = $statement->fetchAll();
                $post_content = '';
                foreach($result as $row){
                    $post_content = $row['post_content'];
                }
                $query3 = "
                INSERT INTO tbl_samples_post 
                (user_id, post_content, post_datetime) 
                VALUES ('".$_SESSION["user_id"]."', '".$post_content."', '".date("Y-m-d") . ' ' . date("H:i:s", STRTOTIME(date('h:i:sa')))."')
                ";
                $statement = $connect->prepare($query3);
                if($statement->execute())
                {
                 echo 'Repost done successfully';
                }
            }
        }
    }
 }

}

function count_retweet($connect, $post_id)
{
 $query = "
 SELECT * FROM tbl_repost 
 WHERE post_id = '".$post_id."'
 ";
 $statement = $connect->prepare($query);
 $statement->execute();
 return $statement->rowCount();
}

function count_comment($connect, $post_id){
    $query = "
    SELECT * FROM tbl_comment
    WHERE post_id = '".$post_id."'
    ";
    $statement = $connect->prepare($query);
    $statement->execute();
    return $statement->rowCount();
}



function make_follow_button($connect, $sender_id, $receiver_id)
{
 $query = "
 SELECT * FROM tbl_follow 
 WHERE sender_id = '".$sender_id."' 
 AND receiver_id = '".$receiver_id."'
 ";
 $statement = $connect->prepare($query);
 $statement->execute();
 $total_row = $statement->rowCount();
 $output = '';
 if($total_row > 0)
 {
  $output = '<button type="button" name="follow_button" class="btn btn-warning action_button" data-action="unfollow" data-sender_id="'.$sender_id.'"> Following</button>';
 }
 else
 {
  $output = '<button type="button" name="follow_button" class="btn btn-info action_button" data-action="follow" data-sender_id="'.$sender_id.'"><i class="glyphicon glyphicon-plus"></i> Follow</button>';
 }
 return $output;
}


?>
