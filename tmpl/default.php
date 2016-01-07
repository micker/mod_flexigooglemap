<?php
/**
* @version 0.6.0 stable $Id: default.php yannick berges
* @package Joomla
* @subpackage FLEXIcontent
* @copyright (C) 2015 Berges Yannick - www.com3elles.com
* @license GNU/GPL v2

* special thanks to ggppdk and emmanuel dannan for flexicontent
* special thanks to my master Marc Studer

* FLEXIadmin module is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details. 
**/

//blocage des accés directs sur ce script
defined('_JEXEC') or die('Accés interdit');

JHtml::_('bootstrap.tooltip');
JHTML::_('behavior.modal');
$document = JFactory::getDocument();
//$document->addStyleSheet("./modules/mod_flexiadmin/assets/css/style.css",'text/css',"screen");

//extrafield
require_once (JPATH_ADMINISTRATOR.DS.'components/com_flexicontent/defineconstants.php');
JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_flexicontent'.DS.'tables');
require_once("./components/com_flexicontent/classes/flexicontent.fields.php");
require_once("./components/com_flexicontent/classes/flexicontent.helper.php");
require_once("./components/com_flexicontent/helpers/permission.php");
require_once("./components/com_flexicontent/models/".FLEXI_ITEMVIEW.".php");

$itemmodel_name = FLEXI_J16GE ? 'FlexicontentModelItem' : 'FlexicontentModelItems';
$itemmodel = new $itemmodel_name();


//module config
$height    = $params->get('height', '300px' );
$width    = $params->get('width', '200px' );




jimport( 'joomla.application.component.controller' );
// Check if component is installed
if ( !JComponentHelper::isEnabled( 'com_flexicontent', true) ) {
   echo 'This modules requires component FLEXIcontent!';
   return;
}
?>


<div class="row-fluid">
    <div id="map" style="position: absolute;width:<?php echo $width; ?>;height:<?php echo $height; ?>;"></div>
        
        <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script type="text/javascript">

   /* Déclaration du centre de la map */ 
   var latlng = new google.maps.LatLng(48.8566667, 2.3509871); // Paris

   /* Déclaration de l'objet qui définira les limites de la map */ 
   var bounds = new google.maps.LatLngBounds();

   /* Déclaration et remplissage du tableau qui contiendra nos points, objets LatLng. */
   var myPoints = [];
    <?php 
    $points = array('48.8566667, 2.3509871', '48.8123155, 2.2381535', '48.3906042, -4.4869013', '50.6371834, 3.0630174');
    foreach ($points as $point){
        echo "myPoints.push( new google.maps.LatLng(". $point .")); \r\n";
    }
    ?>

   /* Déclaration des options de la map */ 
   var options = {
    /*zoom : 7,
    center: latlng, */
    //  ici, ces 2 valeurs ne sont plus utiles car calculées automatiquement
    mapTypeId: google.maps.MapTypeId.ROADMAP
   }

   /* Ici, nous déclarons l'élément html ayant pour id "map" comme conteneur de la map */
   var myDiv = document.getElementById('map');

   /* Chargement de la carte avec un type ROADMAP */
   var map = new google.maps.Map(myDiv,options);

   /* Boucle sur les points afin d'ajouter les markers à la map
   et aussi d'étendre ses limites (bounds) grâce à la méthode extend */ 
   for(var i = 0; i < myPoints.length; i++){
    bounds.extend(myPoints[i]);
    var thisMarker = addThisMarker(myPoints[i],i);
    thisMarker.setMap(map);
   }

   /* Ici, on ajuste le zoom de la map en fonction des limites  */ 
   map.fitBounds(bounds);

   /* Fonction qui affiche un marker sur la carte */ 
   function addThisMarker(point,m){
    var marker = new google.maps.Marker({position: point});
    return marker;
   }         

</script>
</div>
