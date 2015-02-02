<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="nl" lang="nl">

<head>

<style type="text/css">

body {
	padding: 30px;
	font-family: Georgia, Times, serif;
	background-color: #fff;
	color:#333;
	width: 70%;
	font-size:1em;
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
<title>Komt de tram al?</title>
</head>
<body>
<h1>Lijn <em>6</em> van de HTM</h1>
<h2>Waar rijden de trams richting <strong>Leidschendam?</strong></h2>
<?php	
// De lijninformatie wordt binnengehaald met curl
$service_url = 'http://v0.ovapi.nl/line/HTM_6_1';
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
// Als je de regel hieronder gebruikt, zie je de output van de API-call in je HTML source.
var_export($decoded);

$alletrams = $decoded["HTM_6_1"]["Actuals"];
$bijgemaal = 0;

echo("<p>De trams bevinden zich nu op: </p><ul>");
   
foreach ($alletrams as $row) {
	if ($row['TimingPointName'] != "Gemaal") {
		echo("<li>" . $row['TimingPointName'] . "</li>");
	} else {
			$bijgemaal = $bijgemaal+1;
		}
}
echo("</ul>");
if ($bijgemaal == 1) {
	echo("<p class='bijzaak'>(En er staat er eentje klaar bij keerpunt het Gemaal.)</p>");
} else {
	echo("<p class='bijzaak'>(En er staan " . $bijgemaal . " trams te wachten bij keerpunt het Gemaal.)</p>");
}
?>
<h2>En richting <strong>Leyenburg</strong>?</h2>
<?php	
// Nu doen we het allemaal nog een keer
$service_url = 'http://v0.ovapi.nl/line/HTM_6_2';
$curl = curl_init($service_url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$curl_response = curl_exec($curl);
if ($curl_response === false) {
    $info = curl_getinfo($curl);
    curl_close($curl);
    die('error occured during curl exec. Additional info: ' . var_export($info));
}
curl_close($curl);
$decoded = json_decode($curl_response,true);
if (isset($decoded->response->status) && $decoded->response->status == 'ERROR') {
    die('Er ging iets fout: ' . $decoded->response->errormessage);
}

$alletrams = $decoded["HTM_6_2"]["Actuals"];
$bijdillen = 0;

echo("<p>De trams bevinden zich nu op: </p><ul>");
   
foreach ($alletrams as $row) {
	if ($row['TimingPointName'] != "Dillenburgsingel") {
		echo("<li>" . $row['TimingPointName'] . "</li>");
	} else {
			$bijdillen = $bijdillen+1;
		}
}
echo("</ul>");
if ($bijdillen == 1) {
	echo("<p class='bijzaak'>(En er staat er eentje klaar bij keerpunt Dillenburgsingel.)</p>");
} else {
	echo("<p class='bijzaak'>(En er staan " . $bijgemaal . " trams te wachten bij keerpunt Dillenburgsingel.)</p>");
}
?>
</body>
</html>