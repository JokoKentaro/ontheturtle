<html>
 <meta charset = "utf-8">
 <body>
    
<?php
  echo "色々いじってみてください！"."<br>";
  echo "エラーが出たり、変な動きをしたら教えてください！"."<br>";

  //データベースへの接続
  $dsn = "データベース名";
  $user = "ユーザー名";
  $password = "パスワード";
  $pdo = new PDO($dsn,$user,$password,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
                                      //データベース操作で発生したエラーを表示

  //データベース内にテーブルを作成
  $sql = "CREATE TABLE IF NOT EXISTS first"
	." ("
	. "id INT AUTO_INCREMENT PRIMARY KEY,"
	. "name char(32),"
	. "comment TEXT,"
	. "date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,"
	. "pass TEXT"
	.");";
	$stmt = $pdo->query($sql);

  //送信ボタンが押されたとき
  if(isset($_POST["submit1"])){
    date_default_timezone_set("Asia/Tokyo");  
    if(empty($_POST["name"])){
      echo "名前を入力してください。";
    }
    elseif(empty($_POST["comment"])){
      echo "コメントを入力してください。";
    }
    elseif(empty($_POST["sendpass"])){
      echo "パスワードを入力してください。";
    }

    //ふつうの送信
    elseif(!empty($_POST["name"])&&!empty($_POST["comment"])&&empty($_POST["edit_num"])&&!empty($_POST["sendpass"])){
      $name = $_POST["name"]; //名前
      $comment = $_POST["comment"]; //コメント
      $pass = $_POST["sendpass"]; //パスワード
      $sql = $pdo -> prepare("INSERT INTO first (name,comment,pass) VALUES (:name,:comment,:pass)");
      $sql -> bindParam(":name",$name,PDO::PARAM_STR);
      $sql -> bindParam(":comment",$comment,PDO::PARAM_STR);
      $sql -> bindParam(":pass",$pass,PDO::PARAM_STR);
      $sql -> execute();
    } 

    //編集の送信
    elseif(!empty($_POST["name"])&&!empty($_POST["comment"])&&!empty($_POST["edit_num"])&&!empty($_POST["sendpass"])){
      $id = $_POST["edit_num"]; //編集する投稿番号
      $name = $_POST["name"]; //編集する名前
      $comment = $_POST["comment"]; //編集するコメント
      $pass = $_POST["sendpass"];
      $date = date("Y/m/d H:i:s"); //日付を更新するため
      $sql = "update first set name=:name,comment=:comment,pass=:pass,date=:date where id=:id";
      $stmt = $pdo->prepare($sql);
      $stmt -> bindParam(":name",$name,PDO::PARAM_STR);
      $stmt -> bindParam(":comment",$comment,PDO::PARAM_STR);
      $stmt -> bindParam(":pass",$pass,PDO::PARAM_STR);
      $stmt -> bindParam(":date",$date,PDO::PARAM_STR);
      $stmt -> bindParam(":id",$id,PDO::PARAM_INT);
      $stmt -> execute();
    }
  }

  //削除ボタンが押されたとき
  if(isset($_POST["submit2"])){
    if(empty($_POST["delete"])){
      echo "削除対象番号を入力してください。";
    }
    elseif(empty($_POST["depass"])){
      echo "パスワードを入力してください。";
    }
    elseif(!empty($_POST["delete"])&&!empty($_POST["depass"])){
      $deid = $_POST["delete"]; //削除したいコメントの投稿番号
      $depass = $_POST["depass"]; //削除したいコメントのパスワード
      $sql = "SELECT * FROM first";
      $stmt = $pdo->query($sql);
      $results = $stmt->fetchAll();
      foreach($results as $row){
        if($row["id"] == $deid){    
          $wantpass = $row["pass"]; //削除したいコメントのパスワードを取得
        }
      }
      $stmt -> execute();
      if($wantpass != $depass){
        echo "パスワードが違います。";
      }else{
        $sql = "delete from first where id=:id";
        $stmt = $pdo->prepare($sql);
        $stmt -> bindParam(":id",$deid,PDO::PARAM_INT);
        $stmt -> execute();
      }
    }
  }

  //編集ボタンが押されたとき
  if(isset($_POST["submit3"])){
    if(empty($_POST["edit"])){
      echo "編集対象番号を入力してください。";
    }
    elseif(empty($_POST["editpass"])){
      echo "パスワードを入力してください。";
    }
    if(!empty($_POST["edit"])&&!empty($_POST["editpass"])){
      $editid = $_POST["edit"];//編集したいコメントの投稿番号
      $editpass = $_POST["editpass"]; //編集したいコメントのパスワード
      $sql = "SELECT * FROM first";
      $stmt = $pdo->query($sql);
      $results = $stmt->fetchAll();
      foreach($results as $row){
        if($row["id"] == $editid){    
          $wantpass = $row["pass"]; //編集したいコメントのパスワードを取得
        }
      }
      $stmt -> execute();
      if($wantpass != $editpass){
        echo "パスワードが違います。";
      }else{
        $sql = "SELECT * FROM first";
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        foreach($results as $row){
          if($row["id"] == $editid){
            $editname = $row["name"];
            $editcomme = $row["comment"];
          }
        }
        $stmt -> execute();
      }
    }
  }
?>

  <form action = "mission_5-1.php" method = "post">
    <!-- 送信 -->
    <input type = "text" name = "name" placeholder = "名前" 
           value = "<?php if(!empty($editname)){echo $editname;}?>"><br>
           <!-- 編集ボタンを押したらその名前を入れる -->

    <input type = "text" name = "comment" placeholder = "コメント" 
           value = "<?php if(!empty($editcomme)){echo $editcomme;}?>"><br>
           <!-- 編集ボタンを押したらそのコメントを入れる -->

    <input type = "text" name = "sendpass" placeholder = "パスワード">
 
    <input type = "hidden" name = "edit_num"
           value = "<?php if(!empty($editid)){echo $editid;}?>">
           <!-- 編集ボタンを押したらその番号を入れる -->

    <input type = "submit" name = "submit1" value = "送信" ><br>
    <br>
    <!-- 削除 -->
    <input type = "text" name = "delete" placeholder = "削除対象番号"><br>
    <input type = "text" name = "depass" placeholder = "パスワード">
    <input type = "submit" name = "submit2" value = "削除"><br>
    <br>
    <!-- 編集 -->
    <input type = "text" name = "edit" placeholder = "編集対象番号"><br>
    <input type = "text" name = "editpass" placeholder = "パスワード">
    <input type = "submit" name = "submit3" value = "編集"><br>
  </form>
  
<?php
  //ブラウザへの書き込み
  $sql = "SELECT * FROM first";
  $stmt = $pdo->query($sql);
  $results = $stmt->fetchAll();
  foreach($results as $row){
    echo $row["id"]."  ";
    echo $row["name"]."  ";
    echo $row["comment"]."  ";
    echo $row["date"]."<br>";
    echo "<hr>";
  }
?>
  
  </body>
</html>