<?php
    session_start();
    include_once("bd/connexionbd.php");

    $utilisateur_id = (int)trim($_GET['id']);

    if(empty($utilisateur_id)){
        header('location: membres.php');
        exit;
    }

    if(isset($_SESSION['id'])){

        $req = $BDD->prepare("SELECT u.*, r.id_demandeur, r.id_receveur, r.statut,  r.id_bloqueur 
            FROM utilisateurs u
            LEFT JOIN relation r ON (id_receveur = u.id AND id_demandeur =:id2 )OR (id_receveur = :id2 AND id_demandeur = u.id)
            WHERE u.id =:id1");

        $req->execute(array('id1' => $utilisateur_id, 'id2' =>$_SESSION['id']));
    }else{
        $req = $BDD->prepare("SELECT u.* 
            FROM utilisateurs u
            WHERE u.id =:id1");

        $req->execute(array('id1' => $utilisateur_id));
    }
   

    $voir_utilisateur = $req->fetch();

    if(!isset($voir_utilisateur['id'])){
        header('location: membres.php');
        exit;
    }
    if(!empty($_POST)){
        extract($_POST);
        $valid =(boolean) true;

        if(isset($_POST['user-ajouter'])){
            
            $req = $BDD->prepare("SELECT id
                FROM relation 
                WHERE (id_receveur = ? AND id_demandeur =? )OR (id_receveur = ? AND id_demandeur =?)");
            $req->execute(array($voir_utilisateur['id'], $_SESSION['id'], $_SESSION['id'], $voir_utilisateur['id']));
            $verif_relation = $req->fetch();

            if(isset($verif_relation['id'])){
                $valid = false;
            }

            if($valid){
                $req=$BDD->prepare("INSERT INTO relation (id_demandeur, id_receveur,  statut) VALUES(?, ?, ?)");
                $req->execute(array($_SESSION['id'], $voir_utilisateur['id'], 1));
        
            }
            header('Location: voir-profil.php?id='.$voir_utilisateur['id']);
            exit;

            
        }elseif(isset($_POST['user-supprimer'])){
            $req=$BDD->prepare("DELETE FROM relation WHERE (id_receveur = ? AND id_demandeur =? )OR (id_receveur = ? AND id_demandeur =?)");
            $req->execute(array($voir_utilisateur['id'], $_SESSION['id'], $_SESSION['id'], $voir_utilisateur['id']));

            header('Location: voir-profil.php?id='.$voir_utilisateur['id']);
            exit;

        }elseif(isset($_POST['user-bloquer'])){
            $req=$BDD->prepare("SELECT id
                FROM relation 
                WHERE (id_receveur = :id1 AND id_demandeur = :id2)OR (id_receveur = :id2 AND id_demandeur =:id1)");             
            $req->execute(array('id1' => $voir_utilisateur['id'], 'id2' => $_SESSION['id']));

            $verif_relation = $req->fetch();
            
            if(isset($verif_relation['id'])){
                $req = $BDD->prepare("UPDATE relation SET id_bloqueur = ? WHERE id=?");
                $req->execute(array($voir_utilisateur['id'], $verif_relation['id']));
            }else{
                $req=$BDD->prepare("INSERT INTO relation (id_demandeur, id_receveur,  statut, id_bloqueur) VALUES(?, ?, ?, ?)");
                $req->execute(array($_SESSION['id'], $voir_utilisateur['id'], 3, $voir_utilisateur['id']));
            }

            header('Location: voir-profil.php?id='.$voir_utilisateur['id']);
            exit;

        }elseif(isset($_POST['user-debloquer'])){
             $req=$BDD->prepare("SELECT id, statut
                FROM relation 
                WHERE (id_receveur = :id1 AND id_demandeur = :id2)OR (id_receveur = :id2 AND id_demandeur =:id1)");             
            $req->execute(array('id1' => $voir_utilisateur['id'], 'id2' => $_SESSION['id']));

            $verif_relation = $req->fetch();

            if(isset($verif_relation['id'])){
                if($verif_relation['statut']==3){
                    $req=$BDD->prepare("DELETE FROM relation WHERE id=?");
                    $req->execute(array($verif_relation['id']));
                }else{
                    $req = $BDD->prepare("UPDATE relation SET id_bloqueur = ? WHERE id=?");
                    $req->execute(array(NULL, $verif_relation['id']));

                }
            }
            header('Location: voir-profil.php?id='.$voir_utilisateur['id']);
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

    <title>Profil de <?= $voir_utilisateur['pseudo'] ?></title>
  </head>
  <body>
  <?php
        require_once('menu.php');
    ?>
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="membre">
                    <div>
                        Pseudo: <?= $voir_utilisateur['pseudo']?>
                    </div> 
                    <div>
                        Email: <?= $voir_utilisateur['email']?>
                    </div>                            
                </div>
                <?php
                    if(isset($_SESSION['id'])){  
                ?>
                <div>
                    <form method="post">
                        <?php
                            if(!isset($voir_utilisateur['statut'])){
                        ?>
                        <input type="submit" name = "user-ajouter" value = "Ajouter">
                        <?php
                            }elseif(isset($voir_utilisateur['statut']) && $voir_utilisateur['id_demandeur']==$_SESSION['id'] && !isset($voir_utilisateur['id_bloqueur']) && $voir_utilisateur['statut'] <> 2){
                        ?>
                        <div>Demande en attente</div>
                        <?php
                            }
                            elseif(isset($voir_utilisateur['statut']) && $voir_utilisateur['id_receveur']==$_SESSION['id'] && !isset($voir_utilisateur['id_bloqueur']) && $voir_utilisateur['statut'] <> 2){
                        ?>
                        <div>Vous avez une demande à accepter</div>
                        <?php
                            }
                            elseif(isset($voir_utilisateur['statut']) && $voir_utilisateur['statut'] ==2 && !isset($voir_utilisateur['id_bloqueur'])){
                        ?>
                        <div>Vous etes amis</div>
                        <?php
                            }
                            if(isset($voir_utilisateur['statut'])  && $voir_utilisateur['id_demandeur']==$_SESSION['id'] && !isset($voir_utilisateur['id_bloqueur']) && $voir_utilisateur['statut'] <> 2){
                        ?>
                        <input type="submit" name = "user-supprimer" value = "Supprimer">
                        <?php
                            }
                            if((isset($voir_utilisateur['statut']) || $voir_utilisateur['statut']==NULL) && !isset($voir_utilisateur['id_bloqueur'])){
                        ?>
                        <input type="submit" name = "user-bloquer" value = "Bloquer">
                        <?php
                            }elseif($voir_utilisateur['id_bloqueur'] <> $_SESSION['id']){
                        ?>
                        <input type="submit" name = "user-debloquer" value = "Débloquer">
                        <?php
                            }else{
                        ?>
                        <div>Vous etes bloqué par cet utilisateur</div>
                        <?php     
                            }
                        ?>
                    </form>
                </div>
                <?php
                    }
                ?>
            </div>
        </div>
    </div>
    
   
    <!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: jQuery and Bootstrap Bundle (includes Popper) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous"></script>

    <!-- Option 2: Separate Popper and Bootstrap JS -->
    <!--
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js" integrity="sha384-+YQ4JLhjyBLPDQt//I+STsc9iw4uQqACwlvpslubQzn4u2UU2UFM80nGisd026JF" crossorigin="anonymous"></script>
    -->
  </body>
</html>