<?php 
function nosalle($fstationDepart,$fstationArrive) 
{ 

$trajet = 8;
  switch ($fstationDepart) {
		  case 'mmo':
		  case 'bsa':
		 case 'prs':
			  $trajet = 3;
			  break;
			  
		  case 'mal':
		  case 'sti':
		  case 'sjo':
			  $trajet = 4;
			  break;
 }
		if  ($trajet == 8)
		{
       	  switch ($fstationArrive) {
		  case 'mmo':
		  case 'bsa':
		 case 'prs':
			  $trajet = 3;
			  break;
			  
		  case 'mal':
		  case 'sti':
		  case 'sjo':
			  $trajet = 4;
			  break;
 }
      }

			if  ($trajet == 8)
		{
$trajet = 5;
      }
return $trajet;	
} 

//fonction changement d'heure

function changeheure($fdatededepart,$ftrajet,$stationDepart, $fdirection) 
{ 
		
//$heurecorige = DateTime::createFromFormat('H:i', $fdatededepart);
$heurecorige = $fdatededepart;
//$formattedweddingdate = $myDateTime->format('d-m-Y');


		//$heurecorige = $fdatededepart;
		//$heurecorige = date("h:i:s", $fdatededepart);

		//$heurecorige = strtotime($heurecorige);
		//echo "heure avant correction" . $heurecorige;
		//$heurecorige = date("H:i",strtotime('+90 minutes',$heurecorige));
		
		if  ($ftrajet == 3 && $fdirection == 'e')
		{
				  switch ($stationDepart) {
				  case 'bsa':
		
					  date_add($heurecorige, date_interval_create_from_date_string('40 minutes'));
					  break;
					  
				 case 'prs':
		
					date_add($heurecorige, date_interval_create_from_date_string('105 minutes'));
		
					  break;
					 }
      } elseif  ($ftrajet == 3 && $fdirection == 'o')
		{
       	  switch ($stationDepart) {
		  case 'prs':

			  date_add($heurecorige, date_interval_create_from_date_string('25 minutes'));
			  break;
			  
		 case 'bsa':

			date_add($heurecorige, date_interval_create_from_date_string('90 minutes'));

			  break;
 } } elseif  ($ftrajet == 4 && $fdirection == 'e')
		{
       	  switch ($stationDepart) {
		  case 'sjo':

			  date_add($heurecorige, date_interval_create_from_date_string('30 minutes'));
			  break;
			  
		 case 'sti':

			date_add($heurecorige, date_interval_create_from_date_string('65 minutes'));

			  break;
 } }elseif  ($ftrajet == 4 && $fdirection == 'o')
		{
       	  switch ($stationDepart) {
		  case 'sti':

			  date_add($heurecorige, date_interval_create_from_date_string('10 minutes'));
			  break;
			  
		 case 'sjo':

			date_add($heurecorige, date_interval_create_from_date_string('55 minutes'));

			  break;
 } }
return $heurecorige;	
} 
//fonction direction

function direction($stationDepart,$stationArrive) 
{ 

$direction = "o";
$trajdirection = "{$stationDepart}{$stationArrive}" ;
  switch ($trajdirection) {
				case 'mmobsa';
				case 'mmocap';
				case 'mmomas';
				case 'mmoprs';
				case 'mmobsp';
				case 'bsacap';
				case 'bsamas';
				case 'bsaprs';
				case 'bsabsp';
				case 'capmas';
				case 'capprs';
				case 'capbsp';
				case 'masprs';
				case 'masbsp';
				case 'prsbsp';
				case 'bspsjo';
				case 'bspsti';
				case 'bspmal';
				case 'sjosti';
				case 'sjomal';
				case 'stimal';

					$direction = "e";
					break;

				case 'malsti';
				case 'malsjo';
				case 'malbsp';
				case 'stisjo';
				case 'stibsp';
				case 'sjobsp';
				case 'bspprs';
				case 'bspmas';
				case 'bspcap';
				case 'bspbsa';
				case 'bspmmo';
				case 'prsmas';
				case 'prscap';
				case 'prsbsa';
				case 'prsmmo';
				case 'mascap';
				case 'masbsa';
				case 'masmmo';
				case 'capbsa';
				case 'capmmo';
				case 'bsammo';

					$direction = "o";
			  		break;
 }



		//$direction = "o";
		return $direction;	
} 


//fonction chercher heure et siège et id reservation

function getinforeserv($salleid,$idorder) 
{ 


$SQL  =  "SELECT reseauchx_reservationreseau_reservationsiege.entity_id, dateheuredebut, code ";
$SQL .=  "FROM  reseauchx_reservationreseau_reservationsiege ";
$SQL .=  "JOIN reseauchx_reservationreseau_siege ON reseauchx_reservationreseau_reservationsiege.siege_id = reseauchx_reservationreseau_siege.entity_id ";
$SQL .=  "WHERE idorder =" . $idorder . " AND confirme =1 AND (reseauchx_reservationreseau_reservationsiege.salle_id =" . $salleid . " OR reseauchx_reservationreseau_reservationsiege.salle_id =6) AND used IS NULL ORDER BY dateheuredebut LIMIT 1 ";



	$query = mysql_query($SQL, dbCon());
 	
	$out['a'] = 'rien';
    	$out['b'] = 'rien';
    	$out['c'] = 'rien';


while ($row = mysql_fetch_array($query, MYSQL_ASSOC)) {
//print_r($row);
$idreservation =  $row['entity_id']; 
$codesiege =  $row['code']; 
$datehdepart =  $row['dateheuredebut']; 

 	$out['a'] = $idreservation;
    	$out['b'] = $codesiege;
    	$out['c'] = $datehdepart;
} 		

return $out;
} 
 
// fonction retour
function getinforeservret($salleid,$idorder) 
{ 


$SQL  =  "SELECT reseauchx_reservationreseau_reservationsiege.entity_id, dateheuredebut, code ";
$SQL .=  "FROM  reseauchx_reservationreseau_reservationsiege ";
$SQL .=  "JOIN reseauchx_reservationreseau_siege ON reseauchx_reservationreseau_reservationsiege.siege_id = reseauchx_reservationreseau_siege.entity_id ";
$SQL .=  "WHERE idorder =" . $idorder . " AND confirme =1 AND (reseauchx_reservationreseau_reservationsiege.salle_id =" . $salleid . " OR reseauchx_reservationreseau_reservationsiege.salle_id =6) AND used IS NULL ORDER BY dateheuredebut DESC LIMIT 1 ";



	$query = mysql_query($SQL, dbCon());
 	
	$out['a'] = 'rien';
    	$out['b'] = 'rien';
    	$out['c'] = 'rien';


while ($row = mysql_fetch_array($query, MYSQL_ASSOC)) {
//print_r($row);
$idreservation =  $row['entity_id']; 
$codesiege =  $row['code']; 
$datehdepart =  $row['dateheuredebut']; 

 	$out['a'] = $idreservation;
    	$out['b'] = $codesiege;
    	$out['c'] = $datehdepart;
} 		

return $out;
} 

//fonction qui update la table sieges_reservation (champ used)

function updatereserv($idreserv) 
{ 


$SQL  =  "UPDATE  reseauchx_reservationreseau_reservationsiege SET  used =  1 WHERE  entity_id =" . $idreserv ;	
	$query = mysql_query($SQL, dbCon());
//return $nothing
} 

//fonction qui concatene l'info reservation et qui update la table ticket_order champ reservation

function updateticketreserv($idreserv,$txtreserv) 
{ 

$SQL  =  "UPDATE  ticket_orders SET  res_all =  '" . $txtreserv . "' WHERE  ticket_orders.ticket_id =" . $idreserv;

	$query = mysql_query($SQL, dbCon());

//return $nothing
} 

?> 