<?php
    session_start();
    include_once("bd/connexionbd.php");

    if(isset($_GET['subject']) && isset($_GET['comment'])){
        $subject = (String) trim($_GET['subject']);
        $comment = (String) trim($_GET['comment']);

        if(!empty($subject) && !empty($comment)){
            $req = $BDD->prepare("INSERT INTO comments(comment_subject, comment_text, id_user) VALUES (?, ?, ?)");
            $req->execute(array($subject, $comment, $_SESSION['id']));

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

    <title>ACCUEIL</title>
  </head>
  <body>
  <?php
        require_once('menu.php');
    ?>
    <?php
        if(isset($_SESSION['id'])){

          echo'Bonjour ' . $_SESSION['pseudo'];
        }else{
    ?>
          <h1>Bienvenu sur mon site </h1>
          <br/>
          <br/>
          <br/>
          <p style="text-align: center;">
              <strong>Veuillez vous connecter d'abord ou vous inscrire si vous n'etes pas encore inscrit</strong>
            </p>
    <?php
        }
    ?>
    <div class="container">
    
      <form method="post" id="comment_form">
            	<div class="form-group">
            		<label>Objet</label>
            		<input type="text" name="subject" id="subject" class="form-control">
            	</div>
              <div class="form-group">
                	<label>Commentaire</label>
                	<textarea name="comment" id="comment" class="form-control" rows="5"></textarea>
              </div>
              <div class="form-group">
                	<input type="submit" name="post" id="post" class="btn btn-primary" value="Envoyer"/>
              </div>
        </form>
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script>
        $(document).ready(function(){
            function load_unseen_notification(view = ''){
                $.ajax({
                    url:"fetch.php",
                    type:"GET",
                    data:{view: view},
                    dataType:"json",
                    success:function(data){
                        $('.dropdown-menu').html(data.notification);
                        if(data.unseen_notification > 0){
                            $('.count').html(data.unseen_notification);
                        }
                    }
                });
            }
            load_unseen_notification();
            $('#comment_form').on('submit',  function(event){
                event.preventDefault();
                if($('#subject').val()  !=  ''  &&  $('#comment').val()  !=  ''){
                    var  form_data  =  $(this).serialize();
                    $.ajax({
                        url:"insert.php",
                        type:"GET",
                        data:form_data,
                        success:function(data){
                            $('#comment_form')[0].reset();
                            load_unseen_notification();
                        }
                    });
                }else{
                    alert("Les deux champs sont obligatoires");
                }
            });

            $(document).on('click',  '.dropdown-toggle',  function(){
            $('.count').html('');
            load_unseen_notification('yes');
            });
            setInterval(function(){
            load_unseen_notification();
            },  5000);
        });
    </script>
  </body>
</html>