<?php require_once(__DIR__ . '/../../../db.php') ?>
<?php require_once(__DIR__ . '/../../../fonctions.php') ?>

<?php require_once(__DIR__ . '/../../../fonctions-sql.php') ?>

<?php
if ($_GET) {
    $id_projet = $_GET['projet'];
    $query = "SELECT * FROM projet, promoteur, secteur, personne WHERE projet.id_promoteur_fk_projet = promoteur.id_promoteur AND projet.id_secteur_fk_projet = secteur.id_secteur AND promoteur.id_personne_fk_promoteur = personne.id_personne AND id_projet = $id_projet AND etat_projet = 'en_cour'";

    $statement = $db->prepare($query);
    $statement->execute();
    $rowStatement = $statement->rowCount();
    if ($rowStatement <= 0) {
        header("Location:/realproject/pages/profile/projet/en-cour");
        exit();
    }

    $result = $statement->fetch();
}

?>

<?php require_once(__DIR__ . '/../../../include/header.php'); ?>

<?php require_once(__DIR__ . '/../../../include/left-sidebar.php'); ?>




<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Projets</h1>
            <div class="section-header-breadcrumb">

                <div class="breadcrumb-item active"><a href="/realproject/pages/profile/tb/tb-promoteur.php">Tableau de bord</a></div>
                <div class="breadcrumb-item active"><a href="/realproject/pages/profile/projet/en-cour/">Projets en cour</a></div>

                <div class="breadcrumb-item">Consulter</div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4><?= $result['nom_projet'] ?></h4>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item">
                                <a style="color: black; font-weight: bold;" class="nav-link active" id="projet-tab" data-toggle="tab" href="#projet" role="tab" aria-controls="projet" aria-selected="true">PROJET</a>
                            </li>
                            <li class="nav-item">
                                <a style="color: black; font-weight: bold;" class="nav-link" id="detail-tab" data-toggle="tab" href="#detail" role="tab" aria-controls="detail" aria-selected="false">DETAILS</a>
                            </li>
                            <li class="nav-item">
                                <a style="color: black; font-weight: bold;" class="nav-link" id="contributeur-tab" data-toggle="tab" href="#contributeur" role="tab" aria-controls="contributeur" aria-selected="false">CONTRIBUTEURS</a>
                            </li>
                        </ul>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade active show" id="projet" role="tabpanel" aria-labelledby="projet-tab">
                                <?= $result['description_projet'] ?>
                            </div>
                            <div class="tab-pane fade" id="detail" role="tabpanel" aria-labelledby="detail-tab">
                                <div class="card-header px-0">
                                    <h3>Document</h3>
                                </div>
                                <div class="card-body">
                                    <?php
                                    $query1 = "SELECT nom_document FROM projet, document WHERE document.id_projet_fk_document = projet.id_projet AND id_projet = $id_projet";

                                    $statement1 = $db->prepare($query1);
                                    $statement1->execute();

                                    $result1 = $statement1->fetchAll();
                                    ?>
                                    <?php if ($result1) : ?>
                                        <div class="row">
                                            <?php foreach ($result1 as $row1) : ?>
                                                <a href="/realproject/assets/upload/document-projet/<?= $row1['nom_document'] ?>" download class="badge badge-light mx-2 mb-3" title="Télecharger"><?= $row1['nom_document'] ?></a>
                                            <?php endforeach ?>
                                        </div>
                                        <div class="text-right m-2"><a class="" href="#">Tout télécharger</a></div>
                                    <?php else : ?>
                                        <i class="bi bi-info-circle-fill mr-1" style="font-size: 25px; color: #5bc0de;"></i>
                                        <span style="font-size: 20px;">Les documents officielles ne sont pas disponible pour ce projet</span>
                                    <?php endif ?>
                                </div>

                                <div class="card-header px-0">
                                    <h3>Opération d'investissement</h3>
                                </div>
                                <div class="card-body">
                                    <h4 class="">Contribution à partir de : <span class="text-muted"><?= $result['min_contribution_projet'] ?>€</span></h4>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="contributeur" role="tabpanel" aria-labelledby="contributeur-tab">
                                <div class="card-body">
                                    <?php
                                    $query2 = "SELECT * FROM contributeur, personne, projet, assoc_projet_and_contributeur WHERE assoc_projet_and_contributeur.id_projet_fk_assoc_projet_and_contributeur = projet.id_projet AND assoc_projet_and_contributeur.id_contributeur_fk_assoc_projet_and_contributeur = contributeur.id_contributeur AND contributeur.id_personne_fk_contributeur = personne.id_personne AND id_projet = $id_projet";

                                    $statement2 = $db->prepare($query2);
                                    $statement2->execute();

                                    $result2 = $statement2->fetchAll();
                                    $contrib_row = $statement2->rowCount();
                                    ?>
                                    <table class="table">
                                        <?php $iContrib = 1;
                                        $totalContrib = 0; ?>
                                        <thead>
                                            <tr>
                                                <th scope="col">N°</th>
                                                <th scope="col">Contributeur</th>
                                                <th scope="col">Mode de contribution</th>
                                                <th scope="col">Montant</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($result2 as $row2) : ?>
                                                <tr>
                                                    <th scope="row"><?= $iContrib ?></th>
                                                    <td><?= $row2['nom_personne'] . ' ' . $row2['prenom_personne'] ?></td>
                                                    <td>
                                                        <?= si_funct($row2['mode_contribution_assoc_projet_and_contributeur'], 'dac', 'Don avec contrepartie', '') ?>
                                                        <?= si_funct($row2['mode_contribution_assoc_projet_and_contributeur'], 'dsc', 'Don sans contrepartie', '') ?>
                                                        <?= si_funct($row2['mode_contribution_assoc_projet_and_contributeur'], 'ps', 'Prêt solidaire', '') ?>
                                                        <?= si_funct($row2['mode_contribution_assoc_projet_and_contributeur'], 'pr', 'Prêt rémunéré', '') ?>
                                                    </td>
                                                    <td><?= $row2['montant_assoc_projet_and_contributeur'] ?>€</td>
                                                </tr>
                                                <?php $iContrib++;
                                                $totalContrib += $row2['montant_assoc_projet_and_contributeur'] ?>
                                            <?php endforeach ?>
                                            <tr>
                                                <td colspan="2"></td>
                                                <td class="text-right px-0"><b>Total =</b></td>
                                                <td><?= $totalContrib ?>€</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card"><img class="card-img-top" src="/realproject/pages/profile/assets/img/<?= si_funct($result['img_cover_projet'], '', 'image-vide/img1.png', 'img-projet/' . $result['img_cover_projet']) ?>" alt="Placeholder">
                    <div class="card-header row">
                        <span>Description du projet</span>
                        <div class="dropdown d-inline" style="margin-left: auto;">
                            <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                Action
                            </button>
                            <div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 27px, 0px); top: 0px; left: 0px; will-change: transform;">
                                <a href="#" class="dropdown-item" id="update_projet">Modifier projet</a>
                                <a href="#" class="dropdown-item text-danger delete_projet" id="<?= $result['id_projet'] ?>">Supprimer projet</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        Projet soumis le <b><?= date("d-m-Y", strtotime($result['date_soumission_projet'])) . ' à ' . date("H:i:s", strtotime($result['date_soumission_projet'])) ?></b>
                        <p class="mb-0"></p>
                        <div class="m-2 text-dark" style="font-size: 20px; font-weight: bold;"><?= $result['montant_total_projet'] ?>€ Recherché</div>
                        <div class=""><span><?= $contrib_row ?></span> Personnes sont intéressés par ce projet</div> <br>
                        <div class="" style="text-decoration: underline; cursor: pointer;" data-toggle="tooltip" data-placement="bottom" title="Cette date peut être modifier à tout moment">Cloture du projet le <?= date("d-m-Y", strtotime($result['date_fin_projet'])) ?> à <?= date("H:i:s", strtotime($result['date_fin_projet'])) ?> </div>
                    </div>
                    <div class="accordion" id="accordion">
                        <div class="info_projet">
                            <div class="card-header" id="headingOne">
                                <button class="btn collapsed" data-toggle="collapse" data-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne"><i class="icon bi bi-chevron-right" style="margin-right: 10px;"></i>Info Projet</button>
                            </div>
                            <div class="collapse" id="collapseOne" aria-labelledby="headingOne" data-parent="#accordion">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-boredered">

                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <h6>Secteur du projet</h6>
                                                    </td>
                                                    <td><?= $result['nom_secteur'] ?></td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <h6>Titre du projet</h6>
                                                    </td>
                                                    <td><?= $result['nom_projet'] ?></td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <h6>Montant recherché</h6>
                                                    </td>
                                                    <td><?= $result['montant_total_projet'] ?>€</td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <h6>Montant collecté</h6>
                                                    </td>
                                                    <td><?= $result['montant_collecte_projet'] ?>€</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2">
                                                        <div class="buttons mb-1">
                                                            <span style="cursor: pointer;" data-toggle="tooltip" data-placement="top" title="Don sans contrepartie" style="font-weight: bold;" class="modeContrib">DSC</span>
                                                            <span style="cursor: pointer;" data-toggle="tooltip" data-placement="top" title="Don avec contrepartie" style="font-weight: bold;" class="modeContrib">DAC</span>
                                                            <span style="cursor: pointer;" data-toggle="tooltip" data-placement="top" title="Prêt Solidaire" style="font-weight: bold;" class="modeContrib px-1">PS</span>
                                                            <span style="cursor: pointer;" data-toggle="tooltip" data-placement="top" title="Prêt rémunéré" style="font-weight: bold;" class="modeContrib px-1">PR</span>
                                                        </div>
                                                        <div class="buttons ms-1">
                                                            <span style="font-weight: bold;" class="modeContrib"><i class="fas fa-<?= si_funct($result['dsc_projet'], 'oui', 'check', 'times') ?>" style="font-size: 25px; color: <?= si_funct($result['dsc_projet'], 'oui', 'green', 'red') ?>;"></i></span>
                                                            <span style="font-weight: bold;" class="modeContrib"><i class="fas fa-<?= si_funct($result['dac_projet'], 'oui', 'check', 'times') ?>" style="font-size: 25px; color: <?= si_funct($result['dac_projet'], 'oui', 'green', 'red') ?>;"></i></span>
                                                            <span style="font-weight: bold;" class="modeContrib"><i class="fas fa-<?= si_funct($result['ps_projet'], 'oui', 'check', 'times') ?>" style="font-size: 25px; color: <?= si_funct($result['ps_projet'], 'oui', 'green', 'red') ?>;"></i></span>
                                                            <span style="font-weight: bold;" class="modeContrib"><i class="fas fa-<?= si_funct($result['pr_projet'], 'oui', 'check', 'times') ?>" style="font-size: 25px; color: <?= si_funct($result['pr_projet'], 'oui', 'green', 'red') ?>;"></i></span>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="info_promoteur">
                            <div class="card-header" id="headingTwo">
                                <button class="btn collapsed" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapsetwo"><i class="icon bi bi-chevron-right" style="margin-right: 10px;"></i>Info Promoteur</button>
                            </div>
                            <div class="collapse" id="collapseTwo" aria-labelledby="headingTwo" data-parent="#accordion">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-boredered">

                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <h6>Promoteur</h6>
                                                    </td>
                                                    <td><?= $result['nom_personne'] . ' ' . $result['prenom_personne'] ?></td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <h6>Téléphone</h6>
                                                    </td>
                                                    <td><?= $result['tel_personne'] ?></td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <h6>Email</h6>
                                                    </td>
                                                    <td><?= $result['email_personne'] ?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Formulaire de modification d'un projet -->
    <div id="id_update_projet_modal" class="modal fade">
        <div class="modal-dialog">
            <form method="post" id="id_update_projet_form" enctype="multipart/form-data">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="form-label">Titre du projet*</label>
                            <input value="<?= $result['nom_projet'] ?>" type="text" class="form-control" name="nom_projet" required>
                        </div>
                        <div class="form-group">
                            <div class="form-line">
                                <label class="form-label">Description du projet* <i class="bi bi-info-circle-fill mr-1" data-toggle="tooltip" data-placement="bottom" title="Ecrivez l'essentiel à savoir sur votre projet(maximun 250 caractères)" style="font-size: 13px; color: gray;"></i></label>
                                <textarea type="text" class="form-control" name="description_projet" required><?= $result['description_projet'] ?></textarea>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group pretty p-icon p-round p-pulse col-6 col-md-4 mx-0">
                                <input id="prix1" type="radio" name="montant_total_projet" value="50000" <?= si_funct($result['montant_total_projet'], 50000, 'checked', '') ?>>
                                <label for="prix1"> 50000 Fcfa</label>
                            </div>
                            <div class="form-group pretty p-icon p-round p-pulse col-6 col-md-4 mx-0">
                                <input id="prix2" type="radio" name="montant_total_projet" value="100000" <?= si_funct($result['montant_total_projet'], 100000, 'checked', '') ?>>
                                <label for="prix2"> 100000 Fcfa</label>
                            </div>
                            <div class="form-group pretty p-icon p-round p-pulse col-6 col-md-4 mx-0">
                                <input id="prix3" type="radio" name="montant_total_projet" value="200000" <?= si_funct($result['montant_total_projet'], 200000, 'checked', '') ?>>
                                <label for="prix3"> 200000 Fcfa</label>
                            </div>
                            <div class="form-group pretty p-icon p-round p-pulse col-6 col-md-4 mx-0">
                                <input id="prix4" type="radio" name="montant_total_projet" value="300000" <?= si_funct($result['montant_total_projet'], 300000, 'checked', '') ?>>
                                <label for="prix4"> 300000 Fcfa</label>
                            </div>
                            <div class="form-group pretty p-icon p-round p-pulse col-6 col-md-4 mx-0">
                                <input id="prix5" type="radio" name="montant_total_projet" value="400000" <?= si_funct($result['montant_total_projet'], 400000, 'checked', '') ?>>
                                <label for="prix5"> 400000 Fcfa</label>
                            </div>
                            <div class="form-group pretty p-icon p-round p-pulse col-6 col-md-4 mx-0">
                                <input id="prix6" type="radio" name="montant_total_projet" value="500000" <?= si_funct($result['montant_total_projet'], 500000, 'checked', '') ?>>
                                <label for="prix6"> 500000 Fcfa</label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" style="font-size: 14px; cursor:default;">Mode de contribution</label>
                            <div class="mode">
                                <div class="">
                                    <input type="checkbox" name="modeContrib[]" value="dac" id="dac" <?= si_funct($result['dac_projet'], 'oui', 'checked', '') ?>>
                                    <label for="dac">
                                        Don avec contrepartie
                                        <i class="bi bi-info-circle-fill mr-1" data-toggle="tooltip" data-placement="bottom" title="Don avec contrepartie" style="margin-left: 10px; font-size: 15px; color: gray;"></i>
                                    </label>
                                </div>
                                <div class="">
                                    <input type="checkbox" name="modeContrib[]" value="dsc" id="dsc" <?= si_funct($result['dsc_projet'], 'oui', 'checked', '') ?>>
                                    <label for="dsc">
                                        Don sans contrepartie
                                        <i class="bi bi-info-circle-fill mr-1" data-toggle="tooltip" data-placement="bottom" title="Don sans contrepartie" style="margin-left: 10px; font-size: 15px; color: gray;"></i>
                                    </label>
                                </div>
                                <div class="">
                                    <input type="checkbox" name="modeContrib[]" value="ps" id="ps" <?= si_funct($result['ps_projet'], 'oui', 'checked', '') ?>>
                                    <label for="ps">
                                        Prêt sans intérêts
                                        <i class="bi bi-info-circle-fill mr-1" data-toggle="tooltip" data-placement="bottom" title="Prêt solidaire" style="margin-left: 10px; font-size: 15px; color: gray;"></i>
                                    </label>
                                </div>
                                <div class="">
                                    <input type="checkbox" name="modeContrib[]" value="pr" id="pr" <?= si_funct($result['pr_projet'], 'oui', 'checked', '') ?>>
                                    <label for="pr">
                                        Prêt avec intérêts
                                        <i class="bi bi-info-circle-fill mr-1" data-toggle="tooltip" data-placement="bottom" title="Prêt rémunéré" style="margin-left: 10px; font-size: 15px; color: gray;"></i>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label class="form-label"> Fichier 1</label>
                                <input type="file" name="fichier1" class="form-control">
                            </div>
                            <div class="form-group col-md-6">
                                <label class="form-label"> Fichier 2</label>
                                <input type="file" name="fichier2" class="form-control">
                            </div>
                            <div class="form-group col-md-6">
                                <label class="form-label"> Fichier 3</label>
                                <input type="file" name="fichier3" class="form-control">
                            </div>
                            <div class="form-group col-md-6">
                                <label class="form-label"> Fichier 4</label>
                                <input type="file" name="fichier4" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <input style="display: none;" type="number" name="id_projet_update" value="<?= $result['id_projet'] ?>">
                    </div>
                    <div class="modal-footer bg-whitesmoke">
                        <button type="submit" class="btn btn-primary btn-shadow save-edit-bouton" name="action" id="action">Modifer</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php require_once(__DIR__ . '/../../../include/right-sidebar.php') ?>
</div>

<script src="/realproject/pages/profile/assets/modules/jquery.min.js"></script>

<script type="text/javascript">
    // Affichage de la fenêtre pour modifier une publication

    $('#update_projet').click(function() {
        $('#id_update_projet_modal').modal('show');
        $('#id_update_projet_modal')[0].reset();
    });

    // Modifier projet 

    $(document).on('submit', '#id_update_projet_form', function(event) {
        event.preventDefault();
        var form_data = new FormData(this);
        $.ajax({
            url: "projet-en-cour-view-action.php",
            type: "POST",
            enctype: 'multipart/form-data',
            data: form_data,
            processData: false,
            contentType: false,
            cache: false,
            dataType: "json",
            success: function(data) {
                console.log(data);
                if (data == 'Projet modifié') {
                    $('#id_update_projet_modal').modal('hide');
                    swal.fire({
                        title: 'Modifié',
                        text: 'Le projet à été modifié avec success',
                        icon: 'success',
                        showCancelButton: false,
                        buttons: true,
                        dangerMode: true,
                    }).then((willDelete) => {
                        if (willDelete) {
                            window.location.reload();
                        } else {}
                    });
                }
            }
        })
    });

    // Supprimer projet

    $(document).on('click', '.delete_projet', function(event) {
        event.preventDefault();

        var id_projet = $(this).attr("id");

        Swal.fire({
            title: 'Supression du projet !',
            text: "Souhaitez-vous vraiment supprimer ce projet ?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Supprimer',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "projet-en-cour-view-action.php",
                    method: "POST",
                    data: {
                        id_projet_delete: id_projet,
                    },
                    dataType: "json",
                    success: function(data) {
                        console.log(data);
                        if (data == 'Projet supprimé') {
                            swal.fire({
                                title: 'Effectué',
                                text: 'Le projet à été supprimé avec success',
                                icon: 'success',
                                showCancelButton: false,
                                buttons: true,
                                dangerMode: true,
                            }).then((willDelete) => {
                                if (willDelete) {
                                    window.location.reload();
                                } else {}
                            });
                        }
                    }
                });
            }
        });
    });
</script>


<?php require_once(__DIR__ . '/../../../include/footer.php') ?>