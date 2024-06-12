
<?php

include 'confFILEMAKER.php';

$contacts = array();

$findRecord = & $fm->newFindCommand('(liste_contact_php)');
$findRecord->addFindCriterion('IDFM', "*");

$findRecordResult = $findRecord->execute();

if (FileMaker::isError($findRecordResult)) {
    exit('Error code : 1-' . $findRecordResult->getMessage());
} else {
    $record1 = $findRecordResult->getFirstRecord();
    $fields = $record1->getFields();

    $records = $findRecordResult->getRecords();

    foreach ($records AS $record) {

        foreach ($fields AS $field) {
            $contact[$field] = $record->getField($field);
        }

        array_push($contacts, $contact);
    }
}
$i = 1;
$output = '<div style="display:inline-block;">';
foreach ($fields AS $field) {
    $contact[$field] = $record->getField($field);
	$output .= '<label for="'.$field.'">'.$field.'</label><input type="text" name="'.$field.'" value="'.$contact[$field].'" id="'.$field.'" />';
	if ($i % 3 == 0) {
		$output .= '</div><div style="display:inline-block;">';
	} else {
		$output .= '<br />';
	}
	$i++;
}
$output .= '</div>';

echo $output;
?>