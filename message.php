<?php
    session_start();
    include_once("bd/connexionbd.php");

    if(!isset($_SESSION['id'])){
        header('location: index.php');
        exit;
    }
  
    $get_id = (int) trim($_GET['id']);

    if($get_id <= 0){
        header('location: messagerie.php');
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


    ///C'est le nombre des messages à afficher

    $nombre_total_message = 10;
    $req = $BDD->prepare("SELECT COUNT(id) as NbMessage
        FROM messagerie
        WHERE (( id_from, id_to)=(:id1, :id2 ) OR ( id_from, id_to)=(:id2, :id1 ))");

    $req->execute(array('id1'=> $_SESSION['id'], 'id2'=>$get_id));

    $nombre_message = $req->fetch();

    $verifier_nb_message = 0;

    if($nombre_message['NbMessage']-$nombre_total_message > 0){
        $verifier_nb_message = ($nombre_message['NbMessage']-$nombre_total_message);
    }

    $req = $BDD->prepare("SELECT *
        FROM messagerie
        WHERE (( id_from, id_to)=(:id1, :id2 ) OR ( id_from, id_to)=(:id2, :id1 ))
        ORDER BY date_message 
        LIMIT $verifier_nb_message, $nombre_total_message");

    $req->execute(array('id1'=> $_SESSION['id'], 'id2'=>$get_id));

    $afficher_message = $req->fetchAll();

    $req = $BDD->prepare("UPDATE messagerie SET lu = ? WHERE id_to = ? AND id_from=?");
    $req->execute(array(0, $_SESSION['id'], $get_id));

    if(!empty($_POST)){
        extract($_POST);
        $valid =(boolean) true;

        if(isset($_POST['envoyer'])){
            $message=(String)trim($message);
            $lu=(int)trim($lu);

            if(empty($message)){
                $valid=false;
                $er_message="Il faut mettre un message";
            }

            if($valid){

                $date_message = date("Y-m-d H:i:s");
                $req=$BDD->prepare("INSERT INTO messagerie (id_from, id_to, message, date_message, lu) VALUES(?, ?, ?, ?, ?)");
                $req->execute(array($_SESSION['id'], $get_id, $message, $date_message, 1));
        
            }
            header('Location: message.php?id='.$get_id);
            exit;

            
        }
    }

?>
<!doctype html>
<html lang="fr">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">

    <title>Message</title>
  </head>
  <body>
  <?php
        require_once('menu.php');
    ?>
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="corps-des-messages" id="msg">
                    <?php
                        if($nombre_message['NbMessage'] > $nombre_total_message){
                    ?>
                    <button id = "voir-plus" class="btn-voir-plus-message">Voir plus</button>
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
                    <div id="afficher-message"></div>                
                </div>                
            </div>
            
            <div class="col-sm-12" style="margin-top: 20px" >
                <?php
                    if(isset($er_message)){
                        echo $er_message;
                    }
                ?>
                <form method="post" id="envoyer">
                    <textarea name="message" placeholder="Votre message..." id="message"></textarea>
                    <input type="submit" name="envoyer" value="Envoyer" >
                </form>
            </div>
        </div>
    </div>
    
   
    <!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: jQuery and Bootstrap Bundle (includes Popper) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous"></script>

    <!-- Option 2: Separate Popper and Bootstrap JS -->
    <!--
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js" integrity="sha384-+YQ4JLhjyBLPDQt//I+STsc9iw4uQqACwlvpslubQzn4u2UU2UFM80nGisd026JF" crossorigin="anonymous"></script>
    -->
    <script>
        $(document).ready(function(){

            document.getElementById('msg').scrollTop = document.getElementById('msg').scrollHeight;

            $('#envoyer').on("submit", function(e){
                e.preventDefault();
                
                var id;
                var message;
                id = <?=json_encode($get_id, JSON_UNESCAPED_UNICODE); ?>;
                message = document.getElementById('message').value;
                document.getElementById('message').value='';
                
                if(id > 0 && message != ""){
                    $.ajax({
                        url : 'envoyer-message.php',
                        method : 'POST',
                        dataType : 'html',
                        data : {id: id, message: message},

                        success : function(data){
                            $('#afficher-message').append(data);
                            document.getElementById('msg').scrollTop = document.getElementById('msg').scrollHeight;
                        },

                        error : function(e, xhr, s){
                            let error = e.responseJSON;
                            if(e.status==403 && typeof error !=='undefined'){
                                alert('Erreur 403');
                            }else if(e.status == 404){
                                alert('Erreur 404');
                            }else if(e.status == 401){
                                alert('Erreur 401');
                            }else{
                                alert('Erreur Ajax');
                            }
                        }
                    });

                }
            });

            var chargement_message_auto =0;

            chargement_message_auto = clearInterval(chargement_message_auto);
            chargement_message_auto = setInterval(chargerMessageAuto, 2000);
            function chargerMessageAuto(){
                var id = <?=json_encode($get_id, JSON_UNESCAPED_UNICODE); ?>;

                if(id > 0){
                    $.ajax({
                        url : 'charger-message.php',
                        method : 'POST',
                        dataType : 'html',
                        data : {id: id},

                        success : function(data){
                            if(data.trim() != ""){
                                $('#afficher-message').append(data);
                                document.getElementById('msg').scrollTop = document.getElementById('msg').scrollHeight;
                            } 
                        },

                        error : function(e, xhr, s){
                            let error = e.responseJSON;
                            if(e.status==403 && typeof error !=='undefined'){
                                alert('Erreur 403');
                            }else if(e.status == 404){
                                alert('Erreur 404');
                            }else if(e.status == 401){
                                alert('Erreur 401');
                            }else{
                                alert('Erreur Ajax');
                            }
                        }
                    });
                }
            }
            <?php
                if($nombre_message['NbMessage'] > $nombre_total_message){
             ?>
            var req=0;
            
            $('#voir-plus').click(function(){
                var id;
                var el;
                req +=<?= $nombre_total_message ?>;
                id = <?=json_encode($get_id, JSON_UNESCAPED_UNICODE); ?>;

                $.ajax({
                        url : 'voir-plus-message.php',
                        method : 'POST',
                        dataType : 'html',
                        data : {limit: req, id: id},

                        success : function(data){   
                            $(data).hide().appendTo('#voir-plus-message').fadeIn(2000);
                            document.getElementById('voir-plus-message').removeAttribute('id');
                        
                        },

                        error : function(e, xhr, s){
                            let error = e.responseJSON;
                            if(e.status==403 && typeof error !=='undefined'){
                                alert('Erreur 403');
                            }else if(e.status == 404){
                                alert('Erreur 404');
                            }else if(e.status == 401){
                                alert('Erreur 401');
                            }else{
                                alert('Erreur Ajax');
                            }
                        }
                    });
            });
            <?php
                }
            ?>
        });
    </script>
  </body>
</html>