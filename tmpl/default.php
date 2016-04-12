<?php
/**
* @version 0.5 stable $Id: default.php yannick berges
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
$document->addStyleSheet("./modules/mod_flexigooglemap/assets/css/style.css",'text/css',"screen");



require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.'com_flexicontent'.DS.'defineconstants.php');
require_once (JPATH_SITE.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php');
require_once (JPATH_SITE.DS.'components'.DS.'com_flexicontent'.DS.'helpers'.DS.'route.php');

JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_flexicontent'.DS.'tables');
require_once (JPATH_SITE.DS."components".DS."com_flexicontent".DS."classes".DS."flexicontent.fields.php");
require_once (JPATH_SITE.DS."components".DS."com_flexicontent".DS."classes".DS."flexicontent.helper.php");
require_once (JPATH_SITE.DS."components".DS."com_flexicontent".DS."helpers".DS."permission.php");
require_once (JPATH_SITE.DS."components".DS."com_flexicontent".DS."models".DS.FLEXI_ITEMVIEW.".php");



$itemmodel_name = FLEXI_J16GE ? 'FlexicontentModelItem' : 'FlexicontentModelItems';
$itemmodel = new $itemmodel_name();


//module config
$height    = $params->get('height', '300px' );
$width    = $params->get('width', '200px' );
$mapcenter    = $params->get('mapcenter', '48.8566667, 2.3509871' );
$apikey    = $params->get('apikey', '' );
$maptype    = $params->get('maptype', '' );

$clustermode = $params->get('clustermode', '' );
$gridsize = $params->get('gridsize', '' );
$maxzoom = $params->get('maxzoom', '' );

$uselink = $params->get('uselink', '' );
$useadress = $params->get('useadress', '' );

$animationmarker = $params->get('animationmarker', '' );
$linkmode = $params->get('linkmode', '' );

$readmore = $params->get('readmore', '' );

$usedirection = $params->get('usedirection','');
$directionname = $params->get('directionname','');

$catidmode = $params->get('catidmode');
$fieldaddressid = $params->get('fieldaddressid');
$forced_itemid = $params->get('forced_itemid','');

$infotextmode = $params->get('infotextmode','');
$relitem_html = $params->get('relitem_html','');



jimport( 'joomla.application.component.controller' );
// Check if component is installed
if ( !JComponentHelper::isEnabled( 'com_flexicontent', true) ) {
   echo 'This modules requires component FLEXIcontent!';
   return;
}
?>
<?php 
global $fc_list_items;
?>
<div id="mod_fleximap_default<?php echo $module->id;?>" class="mod_fleximap map<?php echo $moduleclass_sfx ?>" style="width:<?php echo $width; ?>;height:<?php echo $height; ?>;">
    <div id="map" style="position: absolute;width:<?php echo $width; ?>;height:<?php echo $height; ?>;"></div>
        
        <script type="text/javascript" src="http://maps.google.com/maps/api/js?v=3&sensor=false<?php if ($apikey) echo '?key='.$apikey; ?>"></script>
<script type="text/javascript" src="modules/mod_flexigooglemap/assets/js/markerclusterer_compiled.js"></script>
<script type="text/javascript">

<?php
    $tMapTips = array();
    //Recuperation de point de ma bdd
 
        if ($catidmode ==0){// mode categorie fixe
    foreach ($itemsLoc as $itemLoc ){
        $coord = unserialize ($itemLoc->value);
        $lat = $coord['lat'];
        $lon = $coord['lon'];
        if (!empty($lat) || !empty($lon) ) {
            if ($useadress){
               $addre = '<p>'.$coord['addr_display'].'</p>'; 
               $addre = addslashes($addre);
            }
            $coordo = $lat.",".$lon;

            $title = addslashes($itemLoc->title);
            if ($uselink){
                    $link = $itemLoc->link;
                    $link = '<p class="link"><a href="'.$link.'" target="'.$linkmode.'">'.JText::_($readmore).'</a></p>';
                    $link = addslashes($link);
            }
            if ($usedirection){
                if (!empty($lat) || !empty($lon) ) {
                $adressdirection = addslashes($coord['addr_display']);
                }
                $linkdirection= '<div class="directions"><a href="http://maps.google.com/maps?q='.$adressdirection.'" target="_blank" class="direction">'.JText::_($directionname).'</a></div>';
            }
        if ($infotextmode){
            $contentwindows = $relitem_html;
        }
            else{
            $contentwindows = $addre.' '.$link ;
        }
        
            
            $tMapTips[] = "['<h4 class=\"fleximaptitle\">$title</h4>$contentwindows $linkdirection',".$coordo."]\r\n";
        }
    }
}else { //mode catégorie courante
    //var_dump ($fc_list_items[2]->fieldvalues[$fieldaddressid][0]);
foreach ($fc_list_items as $adress){
    //var_dump ($adress->fieldvalues);
    $coord = $adress->fieldvalues[$fieldaddressid][0];
    $coord = unserialize ($coord);
    $lat = $coord['lat'];
    $lon = $coord['lon'];
    if (!empty($lat) || !empty($lon) ) {
    $coordo = $lat .",". $lon;
        // }
        $title = addslashes($adress->title);
            if ($uselink){
                    $link = JRoute::_(FlexicontentHelperRoute::getItemRoute($adress->id, $adress->catid, $forced_itemid, $adress));;
                    $link = '<p class="link"><a href="'.$link.'" target="'.$linkmode.'">'.JText::_($readmore).'</a></p>';
                    $link = addslashes($link);
            }
         if ($useadress){
              $addre = '<p>'.$coord['addr_display'].'</p>'; 
               $addre = addslashes($addre);
            }
         if ($usedirection){
                if (!empty($lat) || !empty($lon) ) {
                $adressdirection = addslashes($coord['addr_display']);
                }
                $linkdirection= '<div class="directions"><a href="http://maps.google.com/maps?q='.$adressdirection.'" target="_blank" class="direction">'.JText::_($directionname).'</a></div>';
            }
        if ($infotextmode){
            $contentwindows = $relitem_html;
        }
            else{
            $contentwindows = $addre.' '.$link ;
        }
        
            
            $tMapTips[] = "['<h4 class=\"fleximaptitle\">$title</h4>$contentwindows $linkdirection',".$coordo."]\r\n";
    }
       
}
        }
    
    $tabMapTipsJS = implode(",",  $tMapTips);
?>
    
    // nouveau script
    // Define your locations: HTML content for the info window, latitude, longitude
    var locations = [ <?php echo $tabMapTipsJS; ?>  ];
    
    var icons = [<?php echo $markerdisplay; ?>]
    var iconsLength = icons.length;
    

    var map = new google.maps.Map(document.getElementById('map'), {
      zoom: 10,
      center: new google.maps.LatLng(-37.92, 151.25),
      mapTypeId: google.maps.MapTypeId.<?php echo $maptype;?>,
      mapTypeControl: false,
      streetViewControl: false,
      panControl: false,
      zoomControlOptions: {
         position: google.maps.ControlPosition.LEFT_BOTTOM
      }
    });
    var infowindow = new google.maps.InfoWindow({
      maxWidth: 160
    });

    var markers = new Array();
    
    var iconCounter = 0;
    
    // Add the markers and infowindows to the map
    for (var i = 0; i < locations.length; i++) {  
      var marker = new google.maps.Marker({
        position: new google.maps.LatLng(locations[i][1], locations[i][2]),
        map: map,
          <?php if($animationmarker){
          //animation option
          echo 'animation: google.maps.Animation.DROP,';
}
          ?>
        icon: icons[iconCounter]
      });

      markers.push(marker);
        
      google.maps.event.addListener(marker, 'click', (function(marker, i) {
        return function() {
          infowindow.setContent(locations[i][0]);
          infowindow.open(map, marker);
        }
      })(marker, i));
      
      iconCounter++;
      // We only have a limited number of possible icon colors, so we may have to restart the counter
      if(iconCounter >= iconsLength) {
      	iconCounter = 0;
      }

    }
    function autoCenter() {
      //  Create a new viewpoint bound
      var bounds = new google.maps.LatLngBounds();
      //  Go through each...
      for (var i = 0; i < markers.length; i++) {  
				bounds.extend(markers[i].position);
      }
      //  Fit these bounds to the map
      map.fitBounds(bounds);
    }
    <?php if ($clustermode) {
    
    echo "var mcOptions = {gridSize:$gridsize, maxZoom:$maxzoom};\r\n";
    echo "var marker = new MarkerClusterer(map, markers, mcOptions);\r\n";
}
    ?>
    autoCenter();
    
  </script> 

</script>
</div>
