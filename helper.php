<?php
/**
* @version 0.5 stable $Id: helper.php yannick berges
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
require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.'com_flexicontent'.DS.'defineconstants.php');
require_once (JPATH_SITE.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php');
require_once (JPATH_SITE.DS.'components'.DS.'com_flexicontent'.DS.'helpers'.DS.'route.php');

JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_flexicontent'.DS.'tables');
require_once (JPATH_SITE.DS."components".DS."com_flexicontent".DS."classes".DS."flexicontent.fields.php");
require_once (JPATH_SITE.DS."components".DS."com_flexicontent".DS."classes".DS."flexicontent.helper.php");
require_once (JPATH_SITE.DS."components".DS."com_flexicontent".DS."helpers".DS."permission.php");
require_once (JPATH_SITE.DS."components".DS."com_flexicontent".DS."models".DS.FLEXI_ITEMVIEW.".php");

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
        $count = $params->get('count');
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

     public static function remplaceField(&$params)
	{
    
    		// some parameter shortcuts
		$relitem_html = $params->get('relitem_html', '__display_text__' ) ;
		
		// Parse and identify custom fields
		$result = preg_match_all("/\{\{([a-zA-Z_0-9]+)(##)?([a-zA-Z_0-9]+)?\}\}/", $relitem_html, $field_matches);
		$custom_field_reps    = $result ? $field_matches[0] : array();
		$custom_field_names   = $result ? $field_matches[1] : array();
		$custom_field_methods = $result ? $field_matches[3] : array();
        
         //dump($custom_field_reps, 'reps');
         //dump($custom_field_names, 'Name');
         //dump($custom_field_methods, 'methode');
		
		/*foreach ($custom_field_names as $i => $custom_field_name)
			$parsed_fields[] = $custom_field_names[$i] . ($custom_field_methods[$i] ? "->". $custom_field_methods[$i] : "");
		echo "$relitem_html :: Fields for Related Items List: ". implode(", ", $parsed_fields ? $parsed_fields : array() ) ."<br/>\n";*/
		
		// Parse and identify language strings and then make language replacements
		$result = preg_match_all("/\%\%([^%]+)\%\%/", $relitem_html, $translate_matches);
		$translate_strings = $result ? $translate_matches[1] : array('FLEXI_READ_MORE_ABOUT');
		foreach ($translate_strings as $translate_string)
			$relitem_html = str_replace('%%'.$translate_string.'%%', JText::_($translate_string), $relitem_html);
		
        
         
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
         $db = JFactory::getDbo();
		$queryLoc = 'SELECT a.id, a.title, b.field_id, b.value , a.catid FROM #__content  AS a LEFT JOIN #__flexicontent_fields_item_relations AS b ON a.id = b.item_id '.$catids_join.' WHERE b.field_id = '.$fieldaddressid.' AND '.  $catids_where.' AND state = 1 ORDER BY title '.$count;
        //var_dump ($queryLoc);
		$db->setQuery( $queryLoc );
		$itemsLoc = $db->loadObjectList();
         
		foreach($itemsLoc as $result)
		{
			// Check if related item is published and skip if not published
			if ($result->state != 1 && $result->state != -5) continue;
			
			$itemslug = $result->id.":".$result->alias;
			$catslug = "";
			
			// Check if removed from category or inside a noRoute category or inside a non-published category
			// and use main category slug or other routable & published category slug
			$catid_arr = explode(",", $result->catidlist);
			$catalias_arr = explode(",", $result->cataliaslist);
			for($i=0; $i<count($catid_arr); $i++) {
				$itemcataliases[$catid_arr[$i]] = $catalias_arr[$i];
			}
			$rel_itemid = $result->id;
			$rel_catid = !empty($result->rel_catid) ? $result->rel_catid : $result->catid;
			if ( isset($itemcataliases[$rel_catid]) && !in_array($rel_catid, $globalnoroute) && $globalcats[$rel_catid]->published) {
				$catslug = $rel_catid.":".$itemcataliases[$rel_catid];
			} else if (!in_array($result->catid, $globalnoroute) && $globalcats[$result->catid]->published ) {
				$catslug = $globalcats[$result->catid]->slug;
			} else {
				foreach ($catid_arr as $catid) {
					if ( !in_array($catid, $globalnoroute) && $globalcats[$catid]->published) {
						$catslug = $globalcats[$catid]->slug;
						break;
					}
				}
			}
			$result->slug = $itemslug;
			$result->categoryslug = $catslug;
		}
		
		// Perform field's display replacements
		if ( $i_slave = $parentfield ? $parentitem->id."_".$parentfield->id : '' ) {
			$fc_run_times['render_subfields'][$i_slave] = 0;
		}
		foreach($custom_field_names as $i => $custom_field_name)
		{
			if ( isset($disallowed_fieldnames[$custom_field_name]) ) continue;
			if ( $custom_field_methods[$i] == 'label' ) continue;
			
			if ($i_slave) $start_microtime = microtime(true);
			
			$display_var = $custom_field_methods[$i] ? $custom_field_methods[$i] : 'display';
			FlexicontentFields::getFieldDisplay($item_list, $custom_field_name, $custom_field_values=null, $display_var);
			
			if ($i_slave) $fc_run_times['render_subfields'][$i_slave] += round(1000000 * 10 * (microtime(true) - $start_microtime)) / 10;
		}
		
		$tooltip_class = FLEXI_J30GE ? ' hasTooltip' : ' hasTip';
		$display = array();
		foreach($itemsLoc as $result)
		{
			$url_read_more = JText::_( isset($_item_data->url_read_more) ? $_item_data->url_read_more : 'FLEXI_READ_MORE_ABOUT' , 1);
			$url_class = (isset($_item_data->url_class) ? $_item_data->url_class : 'relateditem');
			
			// Check if related item is published and skip if not published
			if ($result->state != 1 && $result->state != -5) continue;
			
			// a. Replace some custom made strings
			$item_url = JRoute::_(FlexicontentHelperRoute::getItemRoute($result->slug, $result->categoryslug, 0, $result));
			$item_title_escaped = htmlspecialchars($result->title, ENT_COMPAT, 'UTF-8');
			
			$tooltip_title = flexicontent_html::getToolTip($url_read_more, $item_title_escaped, $translate=0, $escape=0);
			$item_tooltip = ' class="'.$url_class.$tooltip_class.'" title="'.$tooltip_title.'" ';
						
			$display_text = $displayway ? $result->title : $result->id;
			$display_text = !$addlink ? $display_text : '<a href="'.$item_url.'"'.($addtooltip ? $item_tooltip : '').' >' .$display_text. '</a>';
			
			$curr_relitem_html = $relitem_html;
			$curr_relitem_html = str_replace('__item_url__', $item_url, $curr_relitem_html);
			$curr_relitem_html = str_replace('__item_title_escaped__', $item_title_escaped, $curr_relitem_html);
			$curr_relitem_html = str_replace('__item_tooltip__', $item_tooltip, $curr_relitem_html);
			$curr_relitem_html = str_replace('__display_text__', $display_text, $curr_relitem_html);
			
			// b. Replace item properties, e.g. {item->id}, (item->title}, etc
			$null_field = null;
			FlexicontentFields::doQueryReplacements($curr_relitem_html, $null_field, $result);
			
			// c. Replace HTML display of various item fields
			$err_mssg = 'Cannot replace field: "%s" because it is of not allowed field type: "%s", which can cause loop or other problem';
			foreach($custom_field_names as $i => $custom_field_name) {
				$_field = @ $result->fields[$custom_field_name];
				$custom_field_display = '';
				if ($is_disallowed_field = isset($disallowed_fieldnames[$custom_field_name])) {
					$custom_field_display .= sprintf($err_mssg, $custom_field_name, @ $_field->field_type);
				} else {
					$display_var = $custom_field_methods[$i] ? $custom_field_methods[$i] : 'display';
					$custom_field_display .= @ $_field->{$display_var};
				}
				$curr_relitem_html = str_replace($custom_field_reps[$i], $custom_field_display, $curr_relitem_html);
			}
			$display[] = trim($pretext . $curr_relitem_html . $posttext);
		}
		
		$display = $opentag . implode($separatorf, $display) . $closetag;
		return $displayfield;
	}
    
    
    
    
}
