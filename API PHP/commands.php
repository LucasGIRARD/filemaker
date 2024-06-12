<<?php
exit();
//SELECT
$fmFindRegister = & $fm->newFindCommand("(mail_web)");
$fmFindRegister->addFindCriterion("mail::email", "==" . $_POST['email']);
$resultFmFindRegister = $fmFindRegister->execute();


if (FileMaker::isError($resultFmFindRegister)) {
    if ($resultFmFindRegister->code != 401) {

        $step = 1;
        $message = "Une erreur s'est produite sur le serveur : " . $resultFmFindRegister->getMessage() . ".<br />Merci d'en informer le <a href='http://www.srlf.org/contacts/ContactForm.phtml'>support</a>.";
    } else {
        $emailExist = FALSE;
        $emailExistSameContact = FALSE;
    }
} else {

    $module = "member";
    $page = '';

    if ($resultFmFindRegister->getFirstRecord()->getField('id_contact') == $_SESSION['membre']['id']) {
        $emailExistSameContact = FALSE;
        $step = 1;
        $messageUpdate = 'Cet email vous est déjà attribué.';
    } else {
        $step = 1;
        $messageUpdate = 'Cet email à déjà etait attribué à un autre membre.';
    }
}



//INSERT

$valeur = array(
	'id_contact' => $_SESSION['membre']['id'],
	'tel' => $mobile,
	'type_tel' => 'Portable',
	'tel_perso' => '1',
	'tel_perso_web' => '1'
);

$fmStep2Mobile = & $fm->newAddCommand('(tel_web)', $valeur);
$resultFmStep2Mobile = $fmStep2Mobile->execute();

if (FileMaker::isError($resultFmStep2Mobile)) {
	$message .= "<br />Une erreur s'est produite sur le serveur : " . $resultFmStep2Mobile->getMessage() . ".<br />Merci d'en informer le <a href=''>support</a>.";
	$insert = FALSE;
}

//UPDATE

$valeur = array(
    'adresses_perso_web::adresse_perso' => '1',
    'civilite' => $_POST['civilite'],
    'titre' => $titre,
    'nom_jeuneFille' => $nomJF,
    'date_naissance' => $dateDeNaissance,
    'adresses_perso_web::adresse1' => $_POST['adresse'],
    'adresses_perso_web::adresse2' => $adresse2,
    'adresses_perso_web::adresse3' => $adresse3,
    'adresses_perso_web::pays' => $_POST['pays'],
    'adresses_perso_web::ville' => $ville,
    'adresses_perso_web::cp' => $cp,
    'adresses_perso_web::bp' => $bp
);



$EditStep = & $fm->newEditCommand('(contacts_web)', $_SESSION['membre']['fmid'], $valeur);
$resultEditStep = $EditStep->execute();

if (FileMaker::isError($resultEditStep)) {
    $message .= "Une erreur s'est produite sur le serveur : " . $resultEditStep->getMessage() . ".<br />Merci d'en informer le <a href='http://www.srlf.org/contacts/ContactForm.phtml'>support</a>.";
    $insert = FALSE;
}


//DELETE
$newFind = & $fm->newFindCommand("(pro_web)");
$newFind->addFindCriterion("id_contact", "==" . $_SESSION['membre']['id']);
$resultFind = $newFind->execute();

if (FileMaker::isError($resultFind)) {
    if ($resultFind->code != 401) {
        $message .= "Une erreur s'est produite sur le serveur : " . $resultFind->getMessage() . "<br />Merci d'en informer le <a href='http://www.srlf.org/contacts/ContactForm.phtml'>support</a>.";
    }
} else {
    $recordsFind = $resultFind->getRecords();

    foreach ($recordsFind as $value) {
        $fonction = $value->getField("fonction");
        $idFonctionFM = $value->getRecordId();
        $fonctionsFM[$idFonctionFM] = $fonction;
    }
}

foreach ($fonctionsFM as $key => $value) {
    if (!in_array($value, $_POST['fonction'])) {
                //delete
        $newDelete = & $fm->newDeleteCommand('(pro_web)', $key);
        $delete = $newDelete->execute();

        $fonctionsFM[$key] = "";
    }
}


?>