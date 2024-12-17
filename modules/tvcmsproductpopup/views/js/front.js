/**
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
*
* Don't forget to prefix your containers with your own identifier
* to avoid any conflicts with others containers.
*/
$(document).ready(function(){

// var storage;
// storage = $.localStorage;
// var hours = 1; // to clear the localStorage after 1 hour(if someone want to clear after 8hrs simply change hours=8)
// var now = new Date().getTime();
// var setupTime = storage.get('setupTime');
// if (setupTime == null) {
//     storage.set('setupTime', now);
// } else {
//     if(now-setupTime > hours*60*60*1000) {
//         storage.clear();
//         storage.set('setupTime', now);
//     }
// }

	var StatusPopup = true;
	function tvcmspopup() {
		if(StatusPopup){
			StatusPopup = false;
			var $anchors = $('.tvcms-prod-popup');
			(function _tvcms_loop(idx) {
				$anchors.removeClass('show').eq(idx).addClass('show');
				setTimeout(function () {
				  $anchors.removeClass('show');
				}, 6000);
				
				setTimeout(function () {
				  _tvcms_loop((idx + 1) % $anchors.length);
				}, 10000);
			}(0));
		}
	}
	$('.close-prod-popup i').click(function() {
		$('.tvcms-prod-popup').removeClass('show');
	});
	// Remove Popup
	$('.tvprodpopup-close').click(function(){
		$('.tvcms-prod-popup').remove();
	});
	
	$(window).scroll(function(){
      var height = $(window).scrollTop();
      if(height > 100){
        tvcmspopup();
      }
    });
	var height = $(window).scrollTop();
    if(height > 100){
		tvcmspopup();
    }		
 });