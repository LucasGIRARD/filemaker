<?php
include 'vendors/FM/16.3/_conf.php';

$nbrAffichage = 25;
if (isset($_GET['page'])) {
	echo $page = $_GET['page'];
} else {
	$page = 1;
}

function print_r_tree($data)
{
    // capture the output of print_r
    $out = print_r($data, true);

    // replace something like '[element] => <newline> (' with <a href="javascript:toggleDisplay('...');">...</a><div id="..." style="display: none;">
    $out = preg_replace('/([ \t]*)(\[[^\]]+\][ \t]*\=\>[ \t]*[a-z0-9 \t_]+)\n[ \t]*\(/iUe',"'\\1<a href=\"javascript:toggleDisplay(\''.(\$id = substr(md5(rand().'\\0'), 0, 7)).'\');\">\\2</a><div id=\"'.\$id.'\" style=\"display: none;\">'", $out);

    // replace ')' on its own on a new line (surrounded by whitespace is ok) with '</div>
    $out = preg_replace('/^\s*\)\s*$/m', '</div>', $out);

    // print the javascript function toggleDisplay() and then the transformed output
    echo '<script language="Javascript">function toggleDisplay(id) { document.getElementById(id).style.display = (document.getElementById(id).style.display == "block") ? "none" : "block"; }</script>'."\n<pre>$out</pre>";
}

$nbrDebut = $nbrAffichage * ($page - 1);

$fmFind = & $fm->newFindCommand("web_STAGIAIRES");
$fmFind->addFindCriterion('nom', '*');
$fmFind->addFindCriterion('prenom', '*');
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

    $records = $resultFmFind->getRecords();
    print_r_tree($records);
    echo '<table><tr><th>nom</th><th>prenom</th><th>code postal</th></tr>';
    foreach ($records as $key => $value) {
    	$nom = $value->getField("nom");
    	$prenom = $value->getField("prenom");
    	$cp = $value->getField("stagiaires_ADRESSES::cp");
    	echo '<tr><td>'.$nom.'</td><td>'.$prenom.'</td><td>'.$cp.'</td></tr>';
    }
    echo '</table>';
}

?>