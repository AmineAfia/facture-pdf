<?php

$num_facture = "743";
$date_facture = date("d.m.Y");
$date_livraison = date("d.m.Y");
$auteur_pdf = "soufiane.com";

$facture_header = '
<img src="logo.png">
Soufiane Afia
charika lmoubarika
BP 15 Casablanca';

$destinataire_facture = 'Amine Afia
Musterstraße 17
12345 Heidelberg';

$facture_footer = "Merci pour votre visite

<b>ISGAEVENT</b>:";

//list des articles sous la forme: [Article, nombre, prix]
$elements_de_facture = array(
	array("Produit 1", 1, 42.50),
	array("Produit 2", 5, 5.20),
	array("Produit 3", 3, 10.00));

//Höhe eurer taxe. 0.19 für 19% taxe
$taxe = 0.0; 

$pdfName = "Facture_".$num_facture.".pdf";


//////////////////////////// Contenue de PDf en HTML \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\


// Definir le theme de la facture en code HTML
// tcpdf support la majority du syntax HTML (le sopport CSS est limiter)

$html = '
<table cellpadding="5" cellspacing="0" style="width: 100%; ">
	<tr>
		<td>'.nl2br(trim($facture_header)).'</td>
		<td style="text-align: right">
			Num. Facture '.$num_facture.'<br>
			Date: '.$date_facture.'<br>
			Date de livraison: '.$date_livraison.'<br>
		</td>
	</tr>
	<tr>
		 <td style="font-size:1.3em; font-weight: bold;">
			<br><br>
			Facture
			<br>
		 </td>
	</tr>
	<tr>
		<td colspan="2">'.nl2br(trim($destinataire_facture)).'</td>
	</tr>
</table>

<br><br><br>

<table cellpadding="5" cellspacing="0" style="width: 100%;" border="0">
	<tr style="background-color: #cccccc; padding:5px;">
		<td style="padding:5px;"><b>Artice</b></td>
		<td style="text-align: center;"><b>Nombre</b></td>
		<td style="text-align: center;"><b>Prix de l\'unite</b></td>
		<td style="text-align: center;"><b>Prix</b></td>
	</tr>';

$prix_total = 0;

foreach($elements_de_facture as $element) {
	$nombre = $element[1];
	$prix_unitaire = $element[2];
	$prix = $nombre*$prix_unitaire;
	$prix_total += $prix;
	$html .= '<tr>
                <td>'.$element[0].'</td>
				<td style="text-align: center;">'.$element[1].'</td>		
				<td style="text-align: center;">'.number_format($element[2], 2, ',', '').' MAD</td>	
                <td style="text-align: center;">'.number_format($prix, 2, ',', '').' MAD</td>
              </tr>';
}
$html .="</table>";


$html .= '
<hr>
<table cellpadding="5" cellspacing="0" style="width: 100%;" border="0">';
if($taxe > 0) {
	$prix_nett = $prix_total / (1+$taxe);
	$prix_taxer = $prix_total - $prix_nett;
	
	$html .= '
			<tr>
				<td colspan="3">Somme (prix_nett)</td>
				<td style="text-align: center;">'.number_format($prix_nett , 2, ',', '').' MAD</td>
			</tr>
			<tr>
				<td colspan="3">Taxe ('.intaxel($taxe*100).'%)</td>
				<td style="text-align: center;">'.number_format($prix_taxer, 2, ',', '').' MAD</td>
			</tr>';
}

$html .='
            <tr>
                <td colspan="3"><b>Prix Total: </b></td>
                <td style="text-align: center;"><b>'.number_format($prix_total, 2, ',', '').' MAD</b></td>
            </tr>
        </table>
<br><br><br>';

if($taxe == 0) {
	$html .= 'Vente effectuer par Soufiane.<br><br>';
}

$html .= nl2br($facture_footer);



//////////////////////////// Erzeugung eures PDF Dokuments \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

// importer tcpdf
require_once('tcpdf/tcpdf.php');

// Constriure document PDF
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Infos du document
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor($auteur_pdf);
$pdf->SetTitle('Facture '.$num_facture);
$pdf->SetSubject('Facture '.$num_facture);


// Information du Header et Footer
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// Choix du Font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// Choix du Mrgins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// retour au page automatique
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Image Scale 
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// Font d'ecriture
$pdf->SetFont('dejavusans', '', 10);

// Nouvelle page
$pdf->AddPage();

// Inserer le code HTML dans le PDF
$pdf->writeHTML($html, true, false, true, false, '');

//Generation du PDF

//Variante 1: Envoyer PDF aux utilisateur:
$pdf->Output($pdfName, 'I');

//Variante 2: Enregister PDF au serveur:
//$pdf->Output(dirname(__FILE__).'/'.$pdfName, 'F');
//echo 'PDF herunterladen: <a href="'.$pdfName.'">'.$pdfName.'</a>';

?>