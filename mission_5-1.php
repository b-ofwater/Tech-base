<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>mission_5-1</title>
    </head>

    <body>
    <?php    


    //4-1　データベース接続設定
    $dsn = 'mysql:dbname=tb******db;host=localhost';
    $user = 'tb-******';
    $password = '******';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

    //4-2　CREATE文：データベース内にテーブルを作成
    $tablename="tb_5_1_b";
    $sql = "CREATE TABLE IF NOT EXISTS $tablename"
	    ." ("
	    . "id INT AUTO_INCREMENT PRIMARY KEY,"
	    . "name char(32),"
        . "comment TEXT,"
        . "password char(16),"
        . "date DATETIME"
	    .");";
    $stmt = $pdo->query($sql);

    //掲示板機能
    $date = date("Y/m/d H:i:s");
    //4-5 INSERT文：データを入力（データレコードの挿入）
    //コメントが空 error
    if((!isset($_POST["comment"]))||$_POST["comment"]==""){
        $action_txt="名前とコメントが正しく入力されていません。";

    //名前が空  error
    }elseif(empty($_POST["name"])){
        $action_txt="名前が正しく入力されていません。";   

    //パスワードが空　error

    }elseif(empty($_POST["pass"])){
        $action_txt="パスワードが入力されていません。";    


    //コメントと名前とパスワードが空でない。かつ、編集番号と削除番号が空の時
    }elseif(empty($_POST["delNo"] && $_POST["editNo"])){
    //新規投稿  
        $sql = $pdo -> prepare("INSERT INTO $tablename (name, comment, password, date) VALUES (:name, :comment, :password, :date)");
        $sql -> bindParam(':name', $name, PDO::PARAM_STR);
        $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
        $sql -> bindParam(':password', $pass, PDO::PARAM_STR);
        $sql -> bindParam(':date', $date, PDO::PARAM_STR);

        $name = $_POST["name"];
        $comment = $_POST["comment"];
        $pass = $_POST["pass"];
        $sql -> execute();

        $action_txt="投稿しました";


    }

    //編集   
    //編集番号が空でない時
    if(!empty($_POST["editNo"])){
        $editNo = $_POST["editNo"];
        $edit_pass = $_POST["edit_pass"];

        $sql = "SELECT * FROM $tablename";
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        foreach ($results as $row){

            //編集番号と一致する投稿について
            if($editNo==$row['id']){
                //パスワード正しければ
/*
                echo "edit_pass=".$edit_pass; //デバック用
                echo "<br>[pass]=".$row['password'];
                echo "<br>";
*/
                if($edit_pass==$row['password']){
                    //4-7 UPDATE：入力されているデータレコードの内容を編集
                    $id = $_POST["editNo"]; //変更する投稿番号
                    $name = $_POST["edit_name"];
                    $comment = $_POST["edit_comment"]; //変更したい名前、変更したいコメントは自分で決めること
                    $sql = "UPDATE $tablename SET name=:name,comment=:comment,password=:password,date=:date WHERE id=:id";
                    $stmt = $pdo->prepare($sql);

                    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                    $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                    $stmt->bindParam(':password', $row['password'], PDO::PARAM_STR);
                    $stmt->bindParam(':date', $date, PDO::PARAM_STR);
                    $stmt->execute();

                    $action_txt=$id."の投稿を編集しました";


                }else{
                    $action_txt = "パスワードが正しくありません";
                }

            }
        }

    }

    //削除
    //削除番号が空でない時
    if(!empty($_POST["delNo"])){
        $delNo=$_POST["delNo"];
        $del_pass=$_POST["del_pass"];

        $sql = "SELECT * FROM $tablename";
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        foreach ($results as $row){

            //削除番号と一致する投稿について
            if($delNo==$row['id']){
                //パスワード正しければ
/*
                echo "del_pass=".$del_pass; //デバック用
                echo "<br>[pass]=".$row['password'];
                echo "<br>";
*/
                if($del_pass==$row['password']){
                    //4-8 DELETE：入力したデータレコードを削除
                    $id = $_POST["delNo"];
                    $sql = "delete from $tablename where id=:id";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                    $stmt->execute();

                    $action_txt=$id."の投稿を削除しました";

                }else{
                    $action_txt = "パスワードが正しくありません";
                }
            }

        }

    }

    //デバック用
/*    
    echo "name=".$name;
    echo "<br>comment=".$comment;
    echo "<br>pass=".$pass;
    echo "<br>_POST[editNo]=".$_POST["editNo"];
    echo "<br>_POST[delNo]=".$_POST["delNo"];
    echo "<br><hr>";
*/    
    ?>

    <form action="" method="post">

        <!--投稿フォーム-->
        <input type="text" name="name" placeholder="Enter Name"><br>
        <input type="text" name="comment" placeholder="Enter Message"><br>
        <input type="text" name="pass" placeholder="Enter Password">
        <input type="submit" name="submit" value="Send"><br><br>

        <!--編集フォーム-->
        <input type="text" name="editNo" placeholder="Enter edit number"><br>
        <input type="text" name="edit_name" placeholder="Enter Name"><br>
        <input type="text" name="edit_comment" placeholder="Enter Message"><br>        
        <input type="text" name="edit_pass" placeholder="Enter Password">
        <input type="submit" name="submit" value="Edit"><br><br>  

        <!--削除フォーム-->
        <input type="textr" name="delNo" placeholder="Enter delete number"><br>
        <input type="text" name="del_pass" placeholder="Enter Password">
        <input type="submit" name="submit" value="Delete"><br><br>

    </form>

    <?php
    //動作の確認表示

    $sql = "SELECT * FROM $tablename";
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();

        $count=count($results);
        if(!empty($count)){
            echo "----------------------------------------------------<br>";
            echo $action_txt;
            echo "<br>----------------------------------------------------<br>";

        }else{
            echo "----------------------------------------------------<br>";
            echo "投稿がありません";
            echo "<br>----------------------------------------------------<br>";

        }


    //4-6 SERECT文：入力したデータレコードを抽出し、表示する
    $sql = "SELECT * FROM $tablename";
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();

    echo "【投稿一覧】<br>";
    foreach ($results as $row){
	    //$rowの中にはテーブルのカラム名が入る
	    echo $row['id'].',';
	    echo $row['name'].',';
        echo $row['comment'].',';
        echo $row['date']."<br>";
    echo "<hr>";
    }

    ?>

    </body>