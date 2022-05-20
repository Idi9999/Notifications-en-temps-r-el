<?php
    session_start();

    include_once("bd/connexionbd.php");

    if(isset($_SESSION['id'])){
        header('Location:/');
        exit;
    }
    if(!empty($_POST)){
        extract($_POST);
        $valid =(boolean) true;

        if(isset($_POST['inscription'])){
            $pseudo = (String) trim($pseudo);
            $mail = (String) strtolower(trim($mail));
            $password = (String) trim($password);
            

            if(empty($pseudo)){
                $valid=false;
                $err_pseudo="Veuillez renseigner ce champs !";
            }else{
                $req = $BDD->prepare("SELECT id FROM utilisateurs WHERE pseudo = ?");
                $req->execute(array($pseudo));
                
                $utilisateurs = $req->fetch();
                if(isset($utilisateurs['id'])){
                    $valid = false;
                    $err_pseudo = "Ce pseudo existe déja";
                }
            }
            if(empty($mail)){
                $valid=false; 
                $err_mail="Veuillez renseigner ce champs !";
            }else{
                $req = $BDD->prepare("SELECT id FROM utilisateurs WHERE email = ?");
                $req->execute(array($mail));
                
                $utilisateurs = $req->fetch();
                if(isset($utilisateurs['id'])){
                    $valid = false;
                    $err_mail = "Ce mail existe déja";
                }
            }
            if(empty($password)){
                $valid=false;
                $err_password="Veuillez renseigner ce champs !";
            }

            if($valid){
                $password = crypt($password, '$6$rounds=5000$uhsyUII12ZVYWCDGDuzfstfA1y242617HSvscyJGDQT$');
                $req=$BDD->prepare("INSERT INTO utilisateurs( pseudo, email, code) VALUES (?,?,?)");
                $req->execute(array($pseudo, $mail, $password));

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

    <title>incription</title>
  </head>
  <body>
    <?php
        require_once('menu.php');
    ?>
    <h1>Inscription</h1>
    <form method="POST">

        <section>
            <div>
                <?php
                    if(isset($err_pseudo)){
                        echo $err_pseudo;
                    }
                ?>
                <input type="text" name="pseudo" placeholder="Pseudo">
            </div>
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

        <input type="submit" value="s'inscrire" name="inscription">
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