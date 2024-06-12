<?php
include 'vendors/FM/16.3/_conf.php';


$nbrAffichage = 25;
if (isset($_GET['page'])) {
	$page = $_GET['page'];
} else {
	$page = 1;
}

if(!empty($_POST)) {
    //print_r($_POST);
    if(array_key_exists('upd', $_POST)) {
        $newvals = array(
            'civilite' => $_POST['civilite'],
            'nom' => $_POST['nom'],
            'prenom' => $_POST['prenom'],
            'stagiaires_ADRESSES::nom' => $_POST['nomAdresse'],
            'stagiaires_ADRESSES::rue1' => $_POST['rue1'],
            'stagiaires_ADRESSES::rue2' => $_POST['rue2'],
            'stagiaires_ADRESSES::rue3' => $_POST['rue3'],
            'stagiaires_ADRESSES::cp' => $_POST['cp'],
            'stagiaires_ADRESSES::pays' => $_POST['pays'],
            'stagiaires_ADRESSES::ville' => $_POST['ville'],
            'adresseEmail' => $_POST['email'],
        );
        $EditStep = & $fm->newEditCommand('web_STAGIAIRES', $_POST['idfm'], $newvals);
        $resultEditStep = $EditStep->execute();
        if (FileMaker::isError($resultEditStep)) {
            echo "<br>ERREUR UPDATE : " . $resultEditStep->getMessage();
        }

    } else if(array_key_exists('del', $_POST)) {
        $newDelete = & $fm->newDeleteCommand('web_STAGIAIRES', $_POST['idfm']);
        $delete = $newDelete->execute();
    } else if (array_key_exists('add', $_POST)) {
        $vals = array(
            'civilite' => $_POST['civilite'],
            'nom' => $_POST['nom'],
            'prenom' => $_POST['prenom'],
            'stagiaires_ADRESSES::nom' => $_POST['nomAdresse'],
            'stagiaires_ADRESSES::rue1' => $_POST['rue1'],
            'stagiaires_ADRESSES::rue2' => $_POST['rue2'],
            'stagiaires_ADRESSES::rue3' => $_POST['rue3'],
            'stagiaires_ADRESSES::cp' => $_POST['cp'],
            'stagiaires_ADRESSES::pays' => $_POST['pays'],
            'stagiaires_ADRESSES::ville' => $_POST['ville'],
            'adresseEmail' => $_POST['email'],
        );

        $InsertStep = & $fm->newAddCommand('web_STAGIAIRES', $vals);
        $resultInsertStep = $InsertStep->execute();
        if (FileMaker::isError($InsertStep)) {
            echo "<br> INSERT : " . $resultInsertStep->getMessage();
        }
    }
}

$nbrDebut = $nbrAffichage * ($page - 1);

$fmFind = & $fm->newFindCommand("web_STAGIAIRES");
$fmFind->addFindCriterion('nom', '*');
$fmFind->addFindCriterion('prenom', '*');
if(array_key_exists('nomSearch', $_POST)) {
    $fmFind->addFindCriterion("nom", "==" . $_POST['multipleSearch']);
}
if(array_key_exists('multipleSearch', $_POST)) {
    $fmFind = & $fm->newCompoundFindCommand("web_STAGIAIRES");
    $findreq =& $fm->newFindRequest('web_STAGIAIRES');
    $findreq2 =& $fm->newFindRequest('web_STAGIAIRES');
    $findreq3 =& $fm->newFindRequest('web_STAGIAIRES');
    $findreq->addFindCriterion("nom", "==" . $_POST['multipleSearch']);
    $findreq2->addFindCriterion("prenom", "==" . $_POST['multipleSearch']);
    $findreq3->addFindCriterion("stagiaires_ADRESSES::nom", "==" . $_POST['multipleSearch']);
    $fmFind->add(1, $findreq);
    $fmFind->add(2, $findreq2);
    $fmFind->add(3, $findreq3);
}

$fmFind->setRange($nbrDebut, $nbrAffichage);
$fmFind->addSortRule("nom", 1, FILEMAKER_SORT_ASCEND);
$fmFind->addSortRule("prenom", 2, FILEMAKER_SORT_ASCEND);
$resultFmFind = $fmFind->execute();
if (FileMaker::isError($resultFmFind)) {

    if ($resultFmFind->code != 401) {
       echo $message = "Une erreur s'est produite sur le serveur : " . $resultFmFind->getMessage() . ".<br />Merci d'en informer le <a href=''>support</a>.";
    } else {
       echo $message = "aucun contact.";
    }
} else {
    
}

if (FileMaker::isError($resultFmFind)) {

    if ($resultFmFind->code != 401) {
       echo $message = "Une erreur s'est produite sur le serveur : " . $resultFmFind->getMessage() . ".<br />Merci d'en informer le <a href=''>support</a>.";
   } else {
       echo $message = "aucun contact.";
   }
} else {

    $records = $resultFmFind->getRecords();
    //print_r_tree($records);
    ?>
    <form method="post">
        <input type="text" name="civilite" placeholder="civilite">
        <input type="text" name="nom" placeholder="nom">
        <input type="text" name="prenom" placeholder="prenom">
        <input type="text" name="nomAdresse" placeholder="adresse">
        <input type="text" name="rue1" placeholder="rue1">
        <input type="text" name="rue2" placeholder="rue2">
        <input type="text" name="rue3" placeholder="rue3">
        <input type="text" name="cp" placeholder="code postal">
        <input type="text" name="pays" placeholder="pays">
        <input type="text" name="ville" placeholder="ville">
        <input type="text" name="email" placeholder="email">
        <input type="submit" value="+" name="add">
    </form>
    <form method="post">
        <input type="text" name="nomSearch" placeholder="chercher nom">
        <input type="submit" name="search" value="chercher">
    </form>
    <form method="post">
        <input type="text" name="multipleSearch" placeholder="chercher nom">
        <input type="submit" name="search" value="chercher nom prenom adresse">
    </form>
    <table>
        <tr>
            <th>id</th>
            <th>civilite</th>
            <th>nom</th>
            <th>prenom</th>
            <th>nom adresse</th>
            <th>rue1</th>
            <th>rue2</th>
            <th>rue3</th>
            <th>code postal</th>
            <th>pays</th>
            <th>ville</th>
            <th>email</th>
            <th>actions</th>
        </tr>
        <?php

        foreach ($records as $key => $value) {
            $id = $value->getRecordId();
            $civilite = $value->getField("civilite");
            $nom = $value->getField("nom");
            $prenom = $value->getField("prenom");
            $nomAdresse = $value->getField("stagiaires_ADRESSES::nom");
            $rue1= $value->getField("stagiaires_ADRESSES::rue1");
            $rue2 = $value->getField("stagiaires_ADRESSES::rue2");
            $rue3 = $value->getField("stagiaires_ADRESSES::rue3");
            $cp = $value->getField("stagiaires_ADRESSES::cp");
            $pays = $value->getField("stagiaires_ADRESSES::pays");
            $ville = $value->getField("stagiaires_ADRESSES::ville");
            $email = $value->getField("adresseEmail");

            ?>
            <tr>
                <form method="post">
                    <td><?php echo $value->getRecordId() ?></td>
                    <td><input type="text" name="civilite" value="<?php echo $civilite ?>"></td>
                    <td><input type="text" name="nom" value="<?php echo $nom ?>"></td>
                    <td><input type="text" name="prenom" value="<?php echo $prenom ?>"></td>
                    <td><input type="text" name="nomAdresse" value="<?php echo $nomAdresse ?>"></td>
                    <td><input type="text" name="rue1" value="<?php echo $rue1 ?>"></td>
                    <td><input type="text" name="rue2" value="<?php echo $rue2 ?>"></td>
                    <td><input type="text" name="rue3" value="<?php echo $rue3 ?>"></td>
                    <td><input type="text" name="cp" value="<?php echo $cp ?>"></td>
                    <td><input type="text" name="pays" value="<?php echo $pays ?>"></td>
                    <td><input type="text" name="ville" value="<?php echo $ville ?>"></td>
                    <td><input type="text" name="email" value="<?php echo $email ?>"></td>
                    <input type="hidden" name="idfm" value="<?php echo $value->getRecordId() ?>">
                    <td>
                        <input type="submit" name="upd" value="maj">
                        <input type="submit" name="del" value="sup">
                    </td>
                </form>
            </tr>
            
        <?php
        }
        ?>
    </table>
    <form>
        <!--<button name="page" value="<?php //echo 1 ?>" type="submit"><<</button>-->
        <button name="page" value="<?php echo ($page == 1)?1:$page-1 ?>" type="submit"><</button>
        <?php echo $page ?>
        <button name="page" value="<?php echo $page+1 ?>" type="submit">></button>
        <!--<button name="page" value="<?php //echo $page+1 ?>" type="submit">>></button>-->
    </form>
            
        <?php
    }
    
    ?>
