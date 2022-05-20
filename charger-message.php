<?php
    session_start();
    include_once("bd/connexionbd.php");

    if(!isset($_SESSION['id'])){
        exit;
    }
  
    $get_id = (int) trim($_POST['id']);

    if($get_id <= 0){
        exit;
    }
    $req = $BDD->prepare("SELECT id
        FROM relation
        WHERE (( id_demandeur, id_receveur)=(:id1, :id2 ) OR ( id_demandeur, id_receveur)=(:id2, :id1 )) AND statut=:statut");

    $req->execute(array('id1'=> $_SESSION['id'], 'id2'=>$get_id, 'statut'=>2));

    $verifier_relation = $req->fetch();

    if(!isset($verifier_relation['id'])){
        header('location: messagerie.php');
        exit;
    }

    $req = $BDD->prepare("SELECT *
        FROM messagerie
        WHERE id_to = ? AND id_from=? AND lu=?");
    $req->execute(array($_SESSION['id'], $get_id, 1));

    $afficher_message= $req->fetchAll();

    $req = $BDD->prepare("UPDATE messagerie SET lu = ? WHERE id_to = ? AND id_from=?");
    $req->execute(array(0, $_SESSION['id'], $get_id));

   

    foreach($afficher_message as $am){
    ?>
        <div class= "message-droit">
            <?=nl2br($am['message']) ?>
        </div>
    <?php
    }

?>
