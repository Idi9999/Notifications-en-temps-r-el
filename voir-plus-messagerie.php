<?php
    session_start();
    include_once("bd/connexionbd.php");

    if(!isset($_SESSION['id'])){
        exit;
    }

    $limit=(int) trim($_POST['limit']);

    if($limit <= 0){
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
            LIMIT ".$limit.','.$nb_affiche_max);

    $req->execute(array('id'=> $_SESSION['id']));

    $afficher_conversation = $req->fetchAll();

    if(($limit +$nb_affiche_max) >= $nb_total_amis){
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
    <div>
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


