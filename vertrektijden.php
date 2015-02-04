<!DOCTYPE html>
<html dir="ltr" lang="nl">
<head>
<meta name="viewport" content="width=device-width,initial-scale=1.0;">
<style type="text/css">
body {
	padding: 1em;
	font-family: Georgia, Times, serif;
	background-color: #fff;
	color:#333;
	font-size:1em;
}

.hoelaat {
	padding-bottom:1em;
}

.schema {
	padding:2% 0;
}

@media (min-width: 50em) {
.schema {
	width: 48%;
	float:left;
	padding:0 2% 0 0;
}
}

h1 {
	font:bold 120%/1.3 Arial, Helvetica, sans-serif;
	text-transform:uppercase;
	letter-spacing:.5px;
}

h1 em {
	font-style:normal;
	color:red;
}

h2 {
	font-size:150%;
	font-weight:normal;
}

h2 strong {
	font-weight:normal;
	background-color:#FF3;
}

.bijzaak {
	color:#999;
}
</style>
<title>Komt de tram al? Of de bus?</title>
</head>
<body>
<?php 
// Haal huidige datum en tijd op
$nu2 = date('H:i');
echo("<div class=\"hoelaat\"><em>Het is nu " . $nu2 . " uur</em></div>\n");
?>
<div class="schema">
<?php	
// In principe hoef je dit script niet te veranderen!
// Zoek je haltenummers op www.ovradar.nl
// Plak de haltenummers als volgt aan de url(inclusief het vraagteken):?quay1=32002649&quay2=32002650
// Pompiedom!

$quay1 = htmlspecialchars($_GET["quay1"]);
$quay2 = htmlspecialchars($_GET["quay2"]);
// Als je niks invult pakken we halte Oostinje in Den Haag
if ($quay1 == "") {
	$quay1 = "32002647";
};
if ($quay2 == "") {
	$quay2 = "32002648";
};
// De lijninformatie wordt binnengehaald met curl
function ophalen($perron) {
	$service_url = 'http://v0.ovapi.nl/tpc/' . $perron . '/departures';
	$curl = curl_init($service_url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	$curl_response = curl_exec($curl);
	if ($curl_response === false) {
		$info = curl_getinfo($curl);
		curl_close($curl);
		die('error occured during curl exec. Additional info: ' . var_export($info));
	}
	curl_close($curl);
	// Het JSON-bestand wordt omgezet naar een PHP array
	$decoded = json_decode($curl_response,true);
	
	if (isset($decoded->response->status) && $decoded->response->status == 'ERROR') {
		die('Er ging iets fout: ' . $decoded->response->errormessage);
	}
	// Hierin zit DE informatie
	$alleinfo = $decoded[$perron];
	// Uncomment de volgende regel als je de ruwe data wilt bekijken:
	// var_export($decoded[$perron]);
	return $alleinfo;
};


function toonZe($alleinfo, $richting) {
	echo("<h1>Halte " . $alleinfo['Stop']['TimingPointName'] . " <em>" . $richting . "</em></h1>");
	$allevoertuigen = $alleinfo["Passes"];
	// De array opnieuw sorteren op ExpectedArrivalTime
	$tijdstippen = array();
	foreach ($allevoertuigen as $voertuig) {
		$tijdstippen[] = $voertuig['ExpectedArrivalTime'];
	}
	array_multisort($tijdstippen, SORT_ASC, $allevoertuigen); 
	foreach ($allevoertuigen as $row) {
		// Deze gebruik je om de aankomsttijd weer te geven (nu niet in gebruik)
		$tijd = date_parse($row['ExpectedArrivalTime']);
		// Hiermee bereken je het aantal minuten
		$rekentijd = strtotime($row['ExpectedArrivalTime']);
		$nu = strtotime(date("Y-m-d\TH:i:s"));
		if ($nu<$rekentijd) {
		$verschil = round(abs($nu - $rekentijd) / 60,0);
		};
		// Toon alle voertuigen die binnen 3 kwartier komen
		if($verschil <= 45) {
			if ($verschil == 0) {
			echo("<li>Lijn " .$row[LinePublicNumber] . " naar " . $row[DestinationName50] . " komt <strong>NU!</strong></li>");
			} else if ($verschil == 1) {
			echo("<li>Lijn " .$row[LinePublicNumber] . " naar " . $row[DestinationName50] . " komt over ongeveer 1 minuut. Rennen!</em></li>\n");
			} else {
				echo("<li>Lijn " .$row[LinePublicNumber] . " naar " . $row[DestinationName50] . " komt over " . $verschil . "</strong> minuten</em></li>\n");
			};
		};
};
echo("</ul>\n");
};
$alles = ophalen($quay1);
toonZe($alles, "heen");
?>
</div>
<div class="schema">
<?php	
$alles = ophalen($quay2);
toonZe($alles, "terug");
?>
</div>
</body>
</html>