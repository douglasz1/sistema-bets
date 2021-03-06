<?php
function dateToDatabase($dateInput)
{
	$dateObj = DateTime::createFromFormat('d/m/Y H:i', $dateInput);

	if($dateObj === false){
        $dateObj = DateTime::createFromFormat('d/m/Y', $dateInput);

        if ($dateObj === false) {
            throw new Exception('Invalid date:' . $dateInput);
        }

        return $dateObj->format('Y-m-d');
    }

	return $dateObj->format('Y-m-d H:i:s');
}

function dateToBrazil($dateInput)
{
	$date = date('d/m/Y H:i', strtotime($dateInput));

	return $date;
}