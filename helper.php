<?php
/**
* @version 0.0.5 stable $Id: default.php yannick berges
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
abstract class modFlexigooglemapHelper
{
	public static function getLoc(&$params)
	{
        $catidmode = $params->get('catidmode');
        if ($catidmode ==1)
            {
        $app    = JFactory::getApplication();
        $jinput = $app->input;
        $cid = $jinput->get('cid', 0, 'int');
        $catid = $cid;
                }else{
                $catid = $params->get('catid');
        }
        $fieldaddressid = $params->get('fieldaddressid');
        
        //var_dump ($catid);
        global $globalcats;
        //var_dump ($globalcats);
        $catlist = !empty($globalcats[$catid]->descendants) ? $globalcats[$catid]->descendants : $catid;
        $catids_join = 'JOIN #__flexicontent_cats_item_relations AS rel ON rel.itemid = a.id ';
        //var_dump ($catlist);

        $catids_where = ' rel.catid IN ('.$catlist.') ';
        //var_dump ($catids_where);
		// recupere la connexion à la BD
        if (!empty($fieldaddressid)){
        $catidmode = $params->get('count');
		$db = JFactory::getDbo();
		$queryLoc = 'SELECT a.id, a.title, b.field_id, b.value , a.catid FROM #__content  AS a LEFT JOIN #__flexicontent_fields_item_relations AS b ON a.id = b.item_id '.$catids_join.' WHERE b.field_id = '.$fieldaddressid.' AND '.  $catids_where.' AND state = 1 ORDER BY title '.$count;
        //var_dump ($queryLoc);
		$db->setQuery( $queryLoc );
		$itemsLoc = $db->loadObjectList();
        //var_dump ($itemsLoc);
		foreach ($itemsLoc as &$itemLoc) {
            $id = $itemLoc->id;
			$itemLoc->link = JRoute::_( FlexicontentHelperRoute::getItemRoute($id, $catid ) );
		}
		return $itemsLoc;
            }else{
            echo JText::_('FLEXI_GOOGLEMAP_ADRESSFORGOT');
        }
	}
    public static function getMarkercolor(&$params)
	{
            
    /*texte  http://mt.google.com/vt/icon/name=icons/spotlight/spotlight-waypoint-b.png&psize=16&font=fonts/arialuni_t.ttf&color=ff330000&ax=44&ay=48&scale=1&text=A
    rougept ='http://mt.googleapis.com/vt/icon/name=icons/spotlight/spotlight-poi.png&scale=1',
      bluept = 'http://mt.google.com/vt/icon?color=ff004C13&name=icons/spotlight/spotlight-waypoint-blue.png',      
      violetpt = 'http://mt.google.com/vt/icon/name=icons/spotlight/spotlight-ad.png',
      vertpt = 'http://mt.google.com/vt/icon?psize=30&font=fonts/arialuni_t.ttf&color=ff304C13&name=icons/spotlight/spotlight-waypoint-a.png&ax=43&ay=48&text=%E2%80%A2' 
      
      
      <option	value="redpt">FLEXI_GOOGLEMAP_MARKERSCOLOR_REDPT</option>	
                <option	value="greenpt">FLEXI_GOOGLEMAP_MARKERSCOLOR_GREENPT</option>
				<option	value="bluept">FLEXI_GOOGLEMAP_MARKERSCOLOR_BLUEPT</option>						
                <option	value="violetpt">FLEXI_GOOGLEMAP_MARKERSCOLOR_VIOLETPT</option>	
      */
    $markerimage = $params->get('markerimage');
      $markercolor = $params->get('markercolor');
      $lettermarker = $params->get('lettermarker');
      $lettermarkermode = $params->get('lettermarkermode');
        if($lettermarkermode){
            $letter="&text=".$lettermarker."&psize=16&font=fonts/arialuni_t.ttf&color=ff330000&scale=1&ax=44&ay=48";
        }else{
            $letter="";
        }
        $color ="spotlight-waypoint-b.png";
        switch ($markercolor){
            case "red":
                $color ="spotlight-waypoint-b.png";
                break;
            case "green":
                $color ="spotlight-waypoint-a.png";
                break;
                default :
  $color ="spotlight-waypoint-b.png";
  break;
        }
    $url="http://mt.google.com/vt/icon/name=icons/spotlight/";
    if($markerimage){
    $icon="'$markerimage'";
        return $icon;
    }else{
    $icon="'$url$color$letter'";
    return $icon;
    }
            }

}