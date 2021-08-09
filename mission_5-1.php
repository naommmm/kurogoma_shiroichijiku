<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>mission_5-1</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <style>
    .container {
      margin: 10px;
    }
    .error {
      color: red;
    }
    .title {
      font-size: 700;
      font-weight: bold;
    }
  </style>
</head>
<body>
 <?php
 
    $dsn = 'mysql:dbname=***;host=localhost';
    $user = '***';
    $password = '***';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
 
    // Create a table if it doesn't exist just yet
    $sql = 'CREATE TABLE IF NOT EXISTS mission5'
    ." ("
    . "id INT AUTO_INCREMENT PRIMARY KEY,"
    . "name char(32),"
    . "comment TEXT,"
    . "date TEXT,"
    . "password TEXT"
    . ");";
    $stmt = $pdo->query($sql);
    
    //※ データベース接続は上記で行っている状態なので、その部分は不要
    
    $name = "";
    $comment_val = "";
    $id_update = "";
    $pass_update = "";
    $pass_up = "";

    $error_msg = "";

    // uploading a comment
    if (isset($_POST["submit_1"])) {
      if (!empty($_POST["username"]) && !empty($_POST["comment"]) && !empty($_POST["password"])) {
        $username = $_POST["username"];
        $comment = $_POST["comment"];
        $password = $_POST["password"];
        $date = date("Y/m/d H:i:s");

        // handling updating comments
        if (!empty($_POST["id_for_update"]) && !empty($_POST["pass_update"])) {
          $id_for_update = $_POST["id_for_update"];
          $pass_update = $_POST["pass_update"];

          $sql = 'UPDATE mission5 SET name=:name, comment=:comment, date=:date WHERE id=:id';
          $stmt = $pdo->prepare($sql);
          $stmt->bindParam(':id', $id_for_update, PDO::PARAM_INT);
          $stmt->bindParam(':name', $username, PDO::PARAM_STR);
          $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
          // $stmt->bindParam(':pasword', $pass_for_update, PDO::PARAM_STR);
          $stmt->bindParam(':date', $date, PDO::PARAM_STR);   
          $stmt->execute();

        } else {
          $stmt = $pdo->prepare("INSERT INTO mission5 (name, comment, password, date) VALUES (:name, :comment, :password, :date)");
          $stmt->bindParam(':name', $username, PDO::PARAM_STR);
          $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
          $stmt->bindParam(':password', $password, PDO::PARAM_STR);
          $stmt->bindParam(':date', $date, PDO::PARAM_STR);
          $stmt->execute();
        }
      }
    }

    // Deleting a comment by an id
    if (isset($_POST["submit_2"])) {
      if (!empty($_POST["commentID"]) && !empty($_POST["pass_del"])) {
        $id_del = $_POST["commentID"];
        $pass_del = $_POST["pass_del"];
        
        $sql = 'DELETE FROM mission5 WHERE id=:id AND password=:password';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id_del, PDO::PARAM_INT);
        $stmt->bindParam(':password', $pass_del, PDO::PARAM_STR);
        $stmt->execute();
        
      } else {
        $error_msg = "<span class='error'>Error: please fill in the fields</span><br>";
      }
    }

    // Updating a comment by an id
    if (isset($_POST["submit_3"])) {
      if (!empty($_POST["updateID"]) && !empty($_POST["pass_up"])) {
        $id_update = $_POST["updateID"];
        $pass_up = $_POST["pass_up"];

        $sql = 'SELECT * FROM mission5 WHERE id=:id AND password=:password';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id_update, PDO::PARAM_INT);
        $stmt->bindParam(':password', $pass_up, PDO::PARAM_STR);
        $stmt->execute();
        $results = $stmt->fetchAll();
        foreach($results as $row) {
          $name = $row['name'];
          $comment_val = $row['comment'];
          $id_update = $row['id'];
          $pass_update = $row['password'];
        }
      } else {
        $error_msg = "<span class='error'>Error: invalid password</span><br>";
      }
    }
    
  ?>

  <!-- BELOW IS ALL THE HTML STUFF THAT GETS RENDERED ON THE PAGE -->

  <!-- FORM COMPONENT -->
  <div class="container">
    <h1 class="title">MISSION 5-1</h1>
    <p class="h4">
    </p>
    <!-- Upload Form -->
    <div class="mb-3 p-3 row border border-success border-2 rounded-3">
      <h4>投稿フォーム</h4>
      <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" class="form-floating">
        <!-- id form to be hidden -->
        <input type="hidden" name="id_for_update" value="<?php echo htmlspecialchars($id_update, ENT_QUOTES); ?>" >
        <input type="hidden" name="pass_update" value="<?php echo htmlspecialchars($pass_up, ENT_QUOTES); ?>" >
        <!-- Username text input field -->
        <div class="mb-1 col">
          <label for="username" class="form-label">名前：</label>
          <input class="form-control" type="text" id="username" name="username" 
            value="<?php echo htmlspecialchars($name, ENT_QUOTES); ?>" placeholder="名前">
        </div>
        <!-- Comment text input field -->
        <div class="mb-1 col">
          <label for="comment" class="form-label">コメント：</label>
          <input class="form-control" type="text" id="comment" name="comment"
            value="<?php echo htmlspecialchars($comment_val, ENT_QUOTES); ?>" placeholder="ここにコメント">
        </div>
        <!-- Password text input field -->
        <div class="mb-1 col">
          <label for="password" class="form-label">パスワード：</label>
          <input class="form-control" type="password" id="password" name="password" placeholder="パスワード">
        </div>
        <div class="col">
          <input class="btn btn-primary shadow" type="submit" name="submit_1" value="投稿">
        </div>
      </form>
    </div>
    <!-- END Upload Form -->

    <!-- Deletion Form -->
    <div class="mb-3 p-3 row border border-danger border-2 rounded-3">
      <h4>削除フォーム</h4>
      <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"
        method="post" class="form-floating">
        <!-- Comment ID text input field -->
        <div class="mb-1 col">
          <label for="commentID" class="form-label">ID: </label>
          <input class="form-control" type="text" id="commentID" name="commentID" placeholder="id here">
        </div>
        <!-- Password text input field -->
        <div class="mb-1 col">
          <label for="pass_del" class="form-label">Password:</label>
          <input class="form-control" type="password" id="pass_del" name="pass_del" placeholder="password here">
        </div>
        <div class="col">
          <input class="btn btn-danger" type="submit" name="submit_2" value="削除">
        </div>
      </form>
    </div>
    <!-- END Deletion Form -->

    <!-- Update Form -->
      <div class="mb-3 p-3 row border border-primary border-2 rounded-3">
        <h4>編集用フォーム</h4>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" 
          method="post" class="form-floating">
          <!-- Comment ID text input field -->
          <div class="mb-1 col">
            <label for="updateID" class="form-label">ID: </label>
            <input class="form-control" type="text" id="updateID" name="updateID" placeholder="id here">
          </div>
          <!-- Password text input field -->
          <div class="mb-1 col">
            <label for="pass_up" class="form-label">Password:</label>
            <input class="form-control" type="password" id="pass_up" name="pass_up" placeholder="password here">
          </div>
          <div class="col">
            <input class="btn btn-success" type="submit" name="submit_3" value="更新">
          </div>
        </form>
      </div>
    <!-- END Update Form -->

    <?php
      if ($error_msg != "") { 
        echo $error_msg; 
      } 
    ?>

  
    <?php 
      // Show error msg if there is one (エラーメッセージの表示)
      if ($error_msg != "") {
        echo $error_msg;
      }

      // TODO: Show database contents (データの表示)
      $sql = 'SELECT * FROM mission5';
      $stmt = $pdo->query($sql);
      $results = $stmt->fetchAll();
      echo "<hr>";
      foreach($results as $row) {
        echo $row['id'] . " " . $row['name'] . " " . $row['comment'] . " " . $row['date'] . "<br>";
      }
      echo "<hr>";
    ?>
</body>
</html>