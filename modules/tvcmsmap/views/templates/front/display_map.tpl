{**
* 2007-2022 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2022 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{strip}
{if !empty($show_map)}
<div class='tvcmsmap-show container-fluid'>
  <div id="tvmap-show" style="width:100%; height:500px;"></div>
</div>
<script type="text/javascript">

  var tvcms_lat = {Configuration::get('TVCMSMAP_LETITUDE')};
  var tvcms_lng = {Configuration::get('TVCMSMAP_LONGITUDE')};
  var tvcms_zoom = {Configuration::get('TVCMSMAP_ZOOM')};
  var tvcms_map_type ="{Configuration::get('TVCMSMAP_MAP_TYPE')}";
  var tvcms_map_key = "{Configuration::get('TVCMSMAP_API_KEY')}";

  function initMap() {

        var uluru = { lat: tvcms_lat, lng: tvcms_lng };
        var map = new google.maps.Map(document.getElementById('tvmap-show'), {
          zoom: tvcms_zoom,
          center: uluru,
          mapTypeId: tvcms_map_type
        });
        var marker = new google.maps.Marker({
          position: uluru,
          map: map
        });

        var title = "{Configuration::get('TVCMSMAP_TITLE', $id_lang)}";
        var shortDesc = "{Configuration::get('TVCMSMAP_SUB_DESCRIPTION', $id_lang)}";
        var contentString = '<div class=\'tvmap-info-box\'><div class=\'tvmap-info-title\'>'+title+'</div><div class=\'tvmap-description\'>'+shortDesc+'</div></div>';

        var infowindow = new google.maps.InfoWindow({
          content: contentString
        });

        var marker = new google.maps.Marker({
          position: uluru,
          map: map,
          title: title,
        });
        marker.addListener('click', function() {
          infowindow.open(map, marker);
        });
      }
    var mapLoad = function(){
       setTimeout(function(){
        var script = document.createElement('script');
        script.type = 'text/javascript';
        script.setAttribute('async', true);
        script.setAttribute('defer', true);
        script.src = "https://maps.googleapis.com/maps/api/js?key={$show_map}&callback=initMap";
        document.getElementsByTagName('head')[0].appendChild(script);  
       },2000);
      
      };
    window.onload=mapLoad;
</script>
{/if}
{/strip}