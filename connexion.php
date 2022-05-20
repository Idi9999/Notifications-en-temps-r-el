<?php

    session_start();
    include_once("bd/connexionbd.php");

    if(isset($_SESSION['id'])){
      header('Location: index.php');
      exit;
    }
    if(!empty($_POST)){
        extract($_POST);
        $valid =(boolean) true;

        if(isset($_POST['connexion'])){
            $mail = (String) strtolower(trim($mail));
            $password = (String) trim($password);

            if(empty($mail)){
              $valid=false; 
              $err_mail="Veuillez renseigner ce champs !";
            }else{
              $req = $BDD->prepare("SELECT id FROM utilisateurs WHERE email = ?");
              $req->execute(array($mail));
              
              $utilisateurs = $req->fetch();
                if(!isset($utilisateurs['id'])){
                  $valid = false;
                  $err_mail = "Veuillez rensigner ce champs !";
                }
            }
            if(empty($password)){
                $valid=false;
                $err_password="Veuillez renseigner ce champs !";
            }
            $req = $BDD->prepare("SELECT id FROM utilisateurs WHERE email = ? AND code = ?");
            $req->execute(array($mail, crypt($password, '$6$rounds=5000$uhsyUII12ZVYWCDGDuzfstfA1y242617HSvscyJGDQT$')));
            $verif_utilisateurs = $req->fetch();

            if(!isset($verif_utilisateurs['id'])){
              $valid = false;
              $err_mail = "mot de passe ou email incorrect !";
            }

            if ($valid){

              $req = $BDD->prepare("SELECT * FROM utilisateurs WHERE id = ?");

              $req->execute(array($verif_utilisateurs['id']));
              $verif_utilisateurs = $req->fetch();

              $_SESSION['id'] = $verif_utilisateurs['id'];
              $_SESSION['pseudo'] = $verif_utilisateurs['pseudo'];
              $_SESSION['email'] = $verif_utilisateurs['email'];

              header('Location: index.php');
              exit;
            }
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

    <title>Connexion</title>
  </head>
  <body>

    <?php
        require_once('menu.php');
    ?>
    <h1>Se connecter</h1>
    <form method="POST">

      <section>
          <div>
              <?php
                  if(isset($err_mail)){
                      echo $err_mail;
                  }
              ?>
              <input type="text" name="mail" placeholder="Mail">
          </div>
          <div>
              <?php
                  if(isset($err_password)){
                      echo $err_password;
                  }
              ?>
              <input type="password" name="password" placeholder="Mot de passe">
          </div>
      </section>

      <input type="submit" value="Se connecter" name="connexion">
    </form>

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