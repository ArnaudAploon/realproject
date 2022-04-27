<?php

//category_fetch.php

require_once(__DIR__ . '/../../db.php');

// noms des colonnes dans l'ordre
$colonne = array("date_publication_projet", "date_fin_projet", "info_projet", "nom_secteur", "nom_projet", "montant_collecte_projet", "montant_total_projet");

$query = '';

$output = array();

// Récupération de l'id du promoteur
$id_personne = $_SESSION['id_personne'];
$query1 = "SELECT id_promoteur FROM promoteur, personne WHERE promoteur.id_personne_fk_promoteur = personne.id_personne AND id_personne = $id_personne ";
$statement1 = $db->prepare($query1);
$statement1->execute();
$result1 = $statement1->fetch();

$id_promoteur = $result1['id_promoteur'];

$query .= "SELECT * FROM projet, promoteur, secteur, personne WHERE projet.id_promoteur_fk_projet = promoteur.id_promoteur AND projet.id_secteur_fk_projet = secteur.id_secteur AND promoteur.id_personne_fk_promoteur = personne.id_personne AND etat_projet = 'rejete' AND id_promoteur = $id_promoteur "; // changer

if (isset($_POST["search"]["value"])) {    // changer les colonnes à rechercher
    $query .= 'AND (nom_secteur LIKE "%' . $_POST["search"]["value"] . '%" ';
    // $query .= 'OR MessageEvenement LIKE "%'.$_POST["search"]["value"].'%" ';
    $query .= 'OR nom_projet LIKE "%' . $_POST["search"]["value"] . '%" ';
    $query .= 'OR prenom_personne LIKE "%' . $_POST["search"]["value"] . '%" ';
    $query .= 'OR nom_personne LIKE "%' . $_POST["search"]["value"] . '%" ) ';
}


// Filtrage dans le tableau
if (isset($_POST['order'])) {
    $query .= 'ORDER BY ' . $colonne[$_POST['order']['0']['column']] . ' ' . $_POST['order']['0']['dir'] . ' ';
} else {
    $query .= 'ORDER BY date_rejet_projet DESC ';
}

if ($_POST['length'] != -1) {
    $query .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
}

$statement = $db->prepare($query);

$statement->execute();

$result = $statement->fetchAll();

$data = array();

$filtered_rows = $statement->rowCount();


foreach ($result as $row) {
    $sub_array = array(); // tenir compte de l'ordre dansle tableau
    $sub_array[] = date("d-m-Y", strtotime($row['date_publication_projet']));
    $sub_array[] = date("d-m-Y", strtotime($row['date_fin_projet']));
    $sub_array[] = $row['info_projet'];
    $sub_array[] = $row['nom_secteur'];
    $sub_array[] = $row['nom_projet'];
    $sub_array[] = $row['montant_collecte_projet'];
    $sub_array[] = $row['montant_total_projet'];
    $id_projet = $row['id_projet'];
    $actionProjet = "<a href=\"consulter/projet-rejete-view.php?projet=$id_projet\" class=\"btn btn-primary\">Consulter</div>";
    $sub_array[] = $actionProjet;

    $data[] = $sub_array;
}

$output = array(
    "draw"            =>    intval($_POST["draw"]),
    "recordsTotal"      =>  $filtered_rows,
    "recordsFiltered"     =>     get_total_all_records($db),
    "data"                =>    $data
);

function get_total_all_records($db)
{
    // Récupération de l'id du promoteur
    $id_personne = $_SESSION['id_personne'];
    $query1 = "SELECT id_promoteur FROM promoteur, personne WHERE promoteur.id_personne_fk_promoteur = personne.id_personne AND id_personne = $id_personne ";
    $statement1 = $db->prepare($query1);
    $statement1->execute();
    $result1 = $statement1->fetch();

    $id_promoteur = $result1['id_promoteur'];

    $statement = $db->prepare("SELECT * FROM projet, promoteur, secteur, personne WHERE projet.id_promoteur_fk_projet = promoteur.id_promoteur AND projet.id_secteur_fk_projet = secteur.id_secteur AND promoteur.id_personne_fk_promoteur = personne.id_personne AND etat_projet = 'rejete' AND id_promoteur = $id_promoteur "); // same query as above
    $statement->execute();
    return $statement->rowCount();
}

echo json_encode($output);
