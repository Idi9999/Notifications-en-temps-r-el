<?php
    session_start();
    include_once("bd/connexionbd.php");

    if(!isset($_SESSION['id'])){
        exit;
    }

    $limit=(int) trim($_POST['limit']);
    $get_id = (int) trim($_POST['id']);

    if($limit <= 0 || $get_id <= 0){
        exit;
    }

    $req = $BDD->prepare("SELECT id
        FROM relation
        WHERE (( id_demandeur, id_receveur)=(:id1, :id2 ) OR ( id_demandeur, id_receveur)=(:id2, :id1 )) AND statut=:statut");

    $req->execute(array('id1'=> $_SESSION['id'], 'id2'=>$get_id, 'statut'=>2));

    $verifier_relation = $req->fetch();

    if(!isset($verifier_relation['id'])){
        exit;
    }

    ///C'est le nombre des messages à afficher
    $nombre_total_message = 10;
    $limit_mini =0;
    $limit_maxi =0;

    $req = $BDD->prepare("SELECT COUNT(id) as NbMessage
        FROM messagerie
        WHERE (( id_from, id_to)=(:id1, :id2 ) OR ( id_from, id_to)=(:id2, :id1 ))");

    $req->execute(array('id1'=> $_SESSION['id'], 'id2'=>$get_id));

    $nombre_message = $req->fetch();

    $limit_mini = $nombre_message['NbMessage']-$limit;

    if($limit_mini > $nombre_total_message){
        $limit_maxi=$nombre_total_message;
        $limit_mini=$limit_mini-$nombre_total_message;
    }else{
        if($limit_mini>0){
            $limit_maxi =$limit_mini;
        }else{
            $limit_maxi =0;
        }
        $limit_mini=0;
    }

    $req = $BDD->prepare("SELECT *
        FROM messagerie
        WHERE (( id_from, id_to)=(:id1, :id2 ) OR ( id_from, id_to)=(:id2, :id1 ))
        ORDER BY date_message 
        LIMIT $limit_mini, $limit_maxi");

    $req->execute(array('id1'=> $_SESSION['id'], 'id2'=>$get_id));

    $afficher_message = $req->fetchAll();

    if($limit_mini<=0){
?>
<div>
    <script>
        var el = document.getElementById('voir-plus');
        el.classList.add('btn-masquer-voir-plus-message');
    </script>
</div>

<?php 
    }
?>
<div id="voir-plus-message"></div>

<?php
    foreach($afficher_message as $am){
        if($am['id_from']== $_SESSION['id']){
?>
<div class="message-gauche">
    <?=nl2br($am['message']) ?>
</div>
<?php
    }else{
?>
<div class="message-droit"> 
    <?=nl2br($am['message']) ?>
</div>
<?php
        }
    }
?>
