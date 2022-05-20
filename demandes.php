<?php
    session_start();
    include_once("bd/connexionbd.php");

    if(!isset($_SESSION['id'])){
        header('location :index.php');
        exit;
    }

    $req = $BDD->prepare("SELECT r.id, u.pseudo, u.id id_utilisateur 
        FROM relation r
        LEFT JOIN utilisateurs u ON u.id = r.id_demandeur
        WHERE r.id_receveur=? AND r.statut=?");

    $req->execute(array($_SESSION['id'], 1));
    $affiche_demandes = $req->fetchAll();
   
    if(!empty($_POST)){
        extract($_POST);
        $valid =(boolean) true;

        if(isset($_POST['accepter'])){

            $id_relation = (int) $id_relation;

            if($id_relation > 0){
                $req = $BDD->prepare("SELECT id
                    FROM relation 
                    WHERE id=? AND statut=1");
                $req->execute(array($id_relation));

                $verif_relation = $req->fetch();

                if(!isset($verif_relation['id'])){
                    $valid = false;
                }
                if($valid){
                    $req=$BDD->prepare("UPDATE  relation SET statut=2 WHERE id=? AND id_receveur=?");
                    $req->execute(array($id_relation, $_SESSION['id']));
            
                }
            }
            
            header('Location: demandes.php');
            exit;

            
        }elseif(isset($_POST['refuser'])){

            $id_relation = (int) $id_relation;

            if($id_relation > 0){
                $req=$BDD->prepare("DELETE FROM relation WHERE id=? AND id_receveur=?");
                $req->execute(array($id_relation, $_SESSION['id']));
            }

            header('Location: demandes.php');
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

    <title>Demandes d'amis ?></title>
  </head>
  <body>
  <?php
        require_once('menu.php');
    ?>
    <div class="container">
        <div class="row">
            <?php
                foreach($affiche_demandes as $ad){
            ?>
            <div class="col-sm-3">
                <div class="membre">
                    <div>
                    <?= $ad['pseudo']?>
                    </div>
                    <div class ="membre-btn">
                        <a href="voir-profil.php?id=<?=$ad['id_utilisateur'] ?>" class = "membre-btn-voir">Voir</a> 
                    </div>
                    <div>
                        <form method="post">
                            <input type="hidden" name="id_relation" value = "<?=$ad['id']?> "> 
                            <input type="submit" name="accepter" value = "Accepter">
                            <input type="submit" name="refuser" value = "Refuser">
                        </form> 
                    </div>                               
                </div>
            </div>
            <?php    
                }
            ?>
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