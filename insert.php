<?php
    session_start();
	include_once("bd/connexionbd.php");

    if(isset($_GET['subject']) && isset($_GET['comment'])){
        $subject=(String)trim($_GET['subject']);
        $comment=(String)trim($_GET['comment']);

        if(!empty($subject) && !empty($comment)){
            $req=$BDD->prepare("INSERT INTO comments(comment_subject, comment_text, id_user) VALUES (?, ?, ?)");
            $req->execute(array($subject, $comment, $_SESSION['id']));

            exit;
        }
    }
?>