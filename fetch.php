<?php

    session_start();
    include_once("bd/connexionbd.php");
    if(isset($_GET['view'])){
        $output=(String)"";
        if(!empty($_GET['view'])){
            $update_query = $BDD->prepare("UPDATE comments SET comment_status = ? WHERE comment_status = ?");
            $update_query->execute(array(1, 0));
        }
        $query = $BDD->prepare("SELECT * 
                FROM comments 
                WHERE id_user = ?
                ORDER BY comment_id DESC 
                LIMIT 5");
        $query->execute(array($_SESSION['id']));
        $query = $query->fetchAll();
        if(count($query)>0){
            foreach($query as $r){
                $output  .=  '
                        <li>
                            <a href="#">
                                <strong>'.$r["comment_subject"].'</strong><br />
                                <small><em>'.$r["comment_text"].'</em></small>
                            </a>
                        </li>
                ';
            }
        }else{
            $output .= '<li><a href="#"><b>pas de notification</a></li>';
        }
        $status_query = $BDD->prepare("SELECT * 
                FROM comments 
                WHERE comment_status = ? AND id_user = ?");
        $status_query->execute(array(0, $_SESSION['id']));
        $status_query=$status_query->fetchAll();

        $count = count($status_query);

        $data = array(
            'notification'=>$output,
            'unseen_notification'=>$count
        );
        echo json_encode($data);
    }
?>


