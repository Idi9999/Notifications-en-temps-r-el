<?php
    session_start();
    include_once("bd/connexionbd.php");

    if(!isset($_SESSION['id'])){
        header('location: ');
        exit;
    }

    $nb_affiche_max=(int) 2;
    $nb_total_amis=(int) 0;

    $req = $BDD->prepare("SELECT COUNT(id) AS nb_amis
        FROM relation
        WHERE ( id_demandeur =:id OR id_receveur = :id) AND statut = 2");
    $req->execute(array('id'=> $_SESSION['id']));

    $nb_conversation = $req->fetch();
    $nb_total_amis= $nb_conversation['nb_amis'];

    $req = $BDD->prepare("SELECT u.pseudo, u.id, m.message, m.date_message, m.id_from, m.lu
            FROM(
                SELECT IF(r.id_demandeur = :id,  r.id_receveur, r.id_demandeur) id_utilisateur, MAX(m.id) max_id
                    FROM relation r
                    LEFT JOIN messagerie m ON ((m.id_from, m.id_to)=(r.id_demandeur, r.id_receveur) OR (m.id_from, m.id_to)=(r.id_receveur, r.id_demandeur))
                    WHERE (r.id_demandeur = :id OR r.id_receveur= :id) AND r.statut=2
                    GROUP BY IF(m.id_from=:id, m.id_to, m.id_from), r.id) AS DM 
            LEFT JOIN messagerie m ON m.id=DM.max_id
            LEFT JOIN utilisateurs u ON u.id = DM.id_utilisateur
            ORDER BY m.date_message DESC 
            LIMIT ".$nb_affiche_max);

    $req->execute(array('id'=> $_SESSION['id']));

    $afficher_conversation = $req->fetchAll();
    

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

    <title>Messagerie</title>
  </head>
  <body>
  <?php
        require_once('menu.php');
    ?>
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div >
                    <?php
                        foreach($afficher_conversation as $ac){

                    ?>
                    <div style="background: white; border:2px solid #ccc; padding: 10px; margin-top: 10px">      
                        <div>
                            <a href="message.php?id=<?= $ac['id'] ?>">
                               <?= $ac['pseudo'] ?>
                            </a>
                            
                        </div>
                        <div>
                            <?php
                            if($ac['id_from']<> $_SESSION['id'] && $ac['lu']==1){
                            ?>
                            Nouveau
                            <?php
                            }
                            ?>
                        </div>
                        <div style="word-break: break-all">
                            <?php
                                if(isset($ac['message'])){
                                    echo $ac['message'];
                                }else{
                                    echo '<b>Dites lui bonjour! </b>';
                                }
                            ?>
                        </div>
                        <div>
                            <?php
                                if(isset($ac['date_message'])){
                                    echo date('d-m-Y à H:i:s', strtotime($ac['date_message'])); 
                                }
                            ?>
                        </div>
                    </div>
                     <?php
                        }
                    ?>
                    <div id="afficher-liste"></div>
                    <?php
                        if($nb_total_amis > $nb_affiche_max){
                    ?>
                    <button id = "voir-plus" class="btn-voir-plus-message">Voir plus</button>
                    <?php
                        }
                    ?>
                </div>
                
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

        <?php
            if($nb_total_amis > $nb_affiche_max){
        ?>
            var req=0;
            
            $('#voir-plus').click(function(){
                var id;
                var el;
                req +=<?= $nb_affiche_max?>;

                $.ajax({
                        url : 'voir-plus-messagerie.php',
                        method : 'POST',
                        dataType : 'html',
                        data : {limit: req},

                        success : function(data){   
                            $(data).hide().appendTo('#afficher-liste').fadeIn(2000);
                            document.getElementById('afficher-liste').removeAttribute('id');
                        
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