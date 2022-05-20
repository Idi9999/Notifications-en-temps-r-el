
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <a class="navbar-brand" href="#">ACCUEIL</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav">
      <li class="nav-item">
            <a class="nav-link" href="membres.php">Membres</a>
      </li>
    </ul>
    <ul class="navbar-nav mr-auto"> 
        <?php
            if(isset($_SESSION['id'])){
        ?>
        <li class="nav-item">
            <a class="nav-link" href="messagerie.php">Messagerie</a>
        </li>

        <li class="nav-item">
          <a class="nav-link" href="#">Mon profil</a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role= "button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            notifications <span class="count"></span>
          </a>
          <div class="dropdown-menu" aria-labelledby="navbardropdownMenuLink">
            <a class="dropdown-item count" href="#"></a>
          </div>

        </li>
        <?php 
        }else{
        ?>

        <?php
          }
        ?>

    </ul>
    <ul class="navbar-nav ml-md-auto">
        <?php
          if(isset($_SESSION['id'])){
        ?>
        <li class="nav-item">
          <a class="nav-link" href ="" data-toggle="modal" data-target="#exampleModal">Mon profil</a>
        </li>
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Paramètres</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
                </div>
                <div class="modal-body">
                <li>
                  <a href="profil.php">Mon profil</a>
                </li>
                <li>
                  <a href="demandes.php">Demandes d'amis</a>
                </li>
                <li>
                  <a href="editer-profil.php">Editer le profil</a>
                </li>  
                <li>
                  <a href="deconnexion.php">Déconnexion</a>
                </li> 
                </div>
              </div>
            </div>
          </div>
        
        <?php 
        }else{
        ?>
        <li class="nav-item">
            <a class="nav-link" href="inscription.php">S'inscrire</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="connexion.php">Se connecter</a>
        </li>
        <?php
          }
        ?>
      
    </ul>
  </div>
</nav>