function inject_button(e){if(redSHOP.postDanmark.useMap)0==jQuery("#sp_info").length&&(map_contents=get_map_contents(),jQuery(e).parent().after('<div id="postdanmark_html_inject"><input type="button" class="btn btn-small" onclick="showForm(\'showMap\')" value="'+Joomla.JText._("PLG_REDSHOP_SHIPPING_POSTDANMARK_CHOOSE_DELIVERY_POINT")+'"  alt="#TB_inline?width=790&amp;inlineId=showMap" id="showMap_input" />'+'<input type="hidden" name="shop_id" id="shop_id_pacsoft" value="" />'+'<div id="sp_info">'+'<span id="sp_name"></span>'+'<span id="sp_address"></span>'+"</div>"+'<div id="sp_inputs">'+'<input type="hidden" name="service_point_id" value="" />'+'<input type="hidden" name="service_point_id_name" value="" />'+'<input type="hidden" name="service_point_id_address" value="" />'+'<input type="hidden" name="service_point_id_city" value="" />'+'<input type="hidden" name="service_point_id_postcode" value="" />'+"</div>"+map_contents+"</div>"),getShippingZipcodeAjax());else if(0==jQuery("#postdanmark_html_inject").length){var t='<input name="shop_id" id="mapMobileSeachBox" type="hidden" placeholder="'+Joomla.JText._("PLG_REDSHOP_SHIPPING_POSTDANMARK_ENTER_POSTAL_CODE")+'" maxlength="4">';jQuery(e).parent().after('<div id="postdanmark_html_inject">'+t+"</div>"),redSHOP.postDanmark.loadLocationMobile(e)}}function refreshMap(e){e.name.length>0&&(initMap(e.addresses,e.name,e.number,e.opening,e.close,e.opening_sat,e.close_sat,e.lat,e.lng,e.servicePointId),jQuery("#postdanmark_list").html(e.radio_html))}function getShippingZipcodeAjax(){jQuery.post(redSHOP.RSConfig._("SITE_URL")+"index.php?option=com_redshop&view=account_shipto&task=addshipping&return=checkout&tmpl=component&for=true&infoid="+jQuery('input[name="users_info_id"]:checked').val()+"&Itemid=1",function(e){var t=jQuery("#zipcode_ST",e).val();getZipcodeAjax(t)})}function getZipcodeAjax(e){jQuery.post(redSHOP.RSConfig._("SITE_URL")+"index.php?option=com_redshop&view=checkout&task=getShippingInformation&tmpl=component&plugin=PostDanmark",{zipcode:e,countryCode:"DK"},function(e){e.length>0?(service_points=jQuery.parseJSON(e),startpoint&&calculateDistances(),typeof service_points=="object"?refreshMap(service_points):jQuery("#sog_loader").replaceWith('<div style="color: red; font-weight: normal;">'+Joomla.JText._("PLG_REDSHOP_SHIPPING_POSTDANMARK_ENTER_VALUD_ZIP_CODE")+"</div>")):jQuery("#sog_loader").replaceWith('<div style="color: red; font-weight: normal; ">'+Joomla.JText._("PLG_REDSHOP_SHIPPING_POSTDANMARK_ENTER_VALUD_ZIP_CODE")+"</div>")})}function get_map_contents(){var e='<meta name="viewport" content="initial-scale=1.0, user-scalable=no">';return e+='<div id="showMap" class="white-popup mfp-hide">',e+='    <span id="mapMessage"></span>',e+='    <input type="text" id="mapSeachBox" maxlength="4" placeholder="'+Joomla.JText._("PLG_REDSHOP_SHIPPING_POSTDANMARK_ENTER_POSTAL_CODE")+'" />',e+='    <img src="'+redSHOP.RSConfig._("SITE_URL")+'plugins/redshop_shipping/postdanmark/includes/images/postdanmark-logo.png" id="pd-logo"/>',e+='    <div id="map_canvas" style="height: 350px; width: 780px; position: relative; margin-top: 20px;"></div>',e+='    <div id="pickupLocations" class="pickupLocation-container">',e+='        <div class="map_buttons">',e+='        <div class="map-button-save">',e+="            <span>",e+="                <span>"+Joomla.JText._("PLG_REDSHOP_SHIPPING_POSTDANMARK_OK")+"</span>",e+="            </span>",e+="        </div>",e+='        <div class="map-button-close">',e+="            <span>",e+="                <span>"+Joomla.JText._("PLG_REDSHOP_SHIPPING_POSTDANMARK_CANCEL")+"</span>",e+="            </span>",e+="        </div>",e+="    </div>",e+='        <div id="postdanmark_list"></div>',e+='        <div class="clear"></div>',e+='    <div class="map_buttons">',e+='        <div class="map-button-save" style="margin-left: 10px;">',e+="            <span>",e+="                <span>"+Joomla.JText._("PLG_REDSHOP_SHIPPING_POSTDANMARK_OK")+"</span>",e+="            </span>",e+="        </div>",e+='        <div class="map-button-close">',e+="            <span>",e+="                <span>"+Joomla.JText._("PLG_REDSHOP_SHIPPING_POSTDANMARK_CANCEL")+"</span>",e+="            </span>",e+="        </div>",e+="    </div>",e+="    </div>",e+="</div>",e}function validate_postdanmark(){return typeof jQuery('input[name="service_point_id"]').val()!="undefined"&&jQuery('input[name="service_point_id"]').val()==""?!1:!0}function showForm(e){if(e==="showMap"){jQuery.magnificPopup.open({items:{src:jQuery("#showMap")},type:"inline",enableEscapeKey:!1,modal:!0,showCloseBtn:!1,callbacks:{open:function(){initMap(service_points.addresses,service_points.name,service_points.number,service_points.opening,service_points.close,service_points.opening_sat,service_points.close_sat,service_points.lat,service_points.lng,service_points.servicePointId)}}},0);var t=jQuery.magnificPopup.instance}}function checkPDinput(e){var t=jQuery(e).get(0).getAttribute("onclick");return t.length>1&&t.match(/'postdanmark'/)!=null?!0:!1}redSHOP=window.redSHOP||{},redSHOP.postDanmark={},redSHOP.postDanmark.isMobile=screen.width<=480;var google,service_points;jQuery(document).ready(function(){redSHOP.postDanmark.useMap=Boolean(parseInt(redSHOP.RSConfig._("USEMAP")))&&!redSHOP.postDanmark.isMobile;var e=jQuery('input[value="postdanmark_postdanmark"]');(e.attr("checked")==="checked"||e.attr("type")=="hidden")&&inject_button(jQuery('input[value="postdanmark_postdanmark"]').parent().parent()),jQuery('input[type="radio"]').each(function(e,t){checkPDinput(jQuery(t))&&jQuery(t).attr("checked")&&jQuery("#showMap_input").length===0&&inject_button(jQuery(t))}),jQuery('input[type="radio"][id^="shipping_rate_id"]').click(function(){checkPDinput(jQuery(this))?inject_button(jQuery(this)):jQuery("#showMap_input, #sp_info, #sp_inputs, #showMap, #postdanmark_html_inject").remove()});var t=jQuery("body");t.on("click",".moduleRowSelected",function(e){jQuery('input[value="postdanmark_postdanmark"]',jQuery(this)).length>0?jQuery("#showMap_input").length===0&&inject_button(jQuery(this)):jQuery("#showMap_input, #sp_info, #showMap, #sp_inputs, #thickbox-css, .pn_error").remove()}),jQuery('input[name="checkoutnext"], input[name="checkout_final"]').click(function(e){jQuery(".pn_error").remove(),validate_postdanmark()?jQuery("form#adminForm").submit():(jQuery("#sp_info").after('<div class="pn_error" style="color: red; font-weight: normal; ">'+Joomla.JText._("PLG_REDSHOP_SHIPPING_POSTDANMARK_PRESS_POINT_TO_DELIVERY")+"</div>"),e.preventDefault())}),t.on("click",".map-button-save",function(){jQuery(".pn_error").remove();if(!jQuery('input[name="postdanmark_pickupLocation"]').is(":checked"))jQuery("#error_checked_radio").length===0&&jQuery(".map_buttons").before('<span id="error_checked_radio" style="color: red; position: absolute; left: 200px;">'+Joomla.JText._("PLG_REDSHOP_SHIPPING_POSTDANMARK_SELECT_ONE_OPTION")+"</span>");else{jQuery("#error_checked_radio").length>0&&jQuery("#error_checked_radio").remove();var e=jQuery('input[name="postdanmark_pickupLocation"]:checked').val(),t=jQuery('input[id="'+e+'"]').parent().parent(),n=jQuery(".point_info > strong",t).html(),r=jQuery('input[id="'+e+'"]').val(),i=jQuery(".point_info > strong",t).html(),s=jQuery(".postdanmark_address > .street",t).html(),o=jQuery(".postdanmark_address > .city",t).html(),u=jQuery(".postdanmark_address > .service_postcode",t).val();jQuery("input[name='service_point_id']").val(r),jQuery("input[name='service_point_id_name']").val(i),jQuery("input[name='service_point_id_address']").val(s),jQuery("input[name='service_point_id_city']").val(o),jQuery("input[name='service_point_id_postcode']").val(u),jQuery("#sp_info #sp_name").html(n+", "),jQuery("#sp_info #sp_address").html(s+" "+o+" "+u),jQuery("#shop_id_pacsoft").val(r+"|"+i+"|"+s+"|"+u+"|"+o),jQuery.magnificPopup.close()}}),t.on("keyup","#mapSeachBox",function(){if(jQuery(this).val().length===4){var e=!1,t=jQuery(this).val();jQuery(this).attr("placeholder","Søger, Vent venligst..."),jQuery(this).val("").attr("disabled","disabled"),jQuery.post(redSHOP.RSConfig._("SITE_URL")+"index.php?option=com_redshop&view=checkout&task=getShippingInformation&tmpl=component&plugin=PostDanmark",{zipcode:t,countryCode:"DK"},function(e){jQuery("#mapSeachBox").attr("placeholder",Joomla.JText._("PLG_REDSHOP_SHIPPING_POSTDANMARK_ENTER_POSTAL_CODE")).removeAttr("disabled"),e.length>0?(service_points=jQuery.parseJSON(e),startpoint&&calculateDistances(),typeof service_points=="object"?refreshMap(service_points):jQuery("#sog_loader").replaceWith('<div style="color: red; font-weight: normal;">'+Joomla.JText._("PLG_REDSHOP_SHIPPING_POSTDANMARK_ENTER_VALID_ZIP")+"</div>")):jQuery("#sog_loader").replaceWith('<div style="color: red; font-weight: normal; ">'+Joomla.JText._("PLG_REDSHOP_SHIPPING_POSTDANMARK_ENTER_VALID_ZIP")+"</div>")})}}),t.on("click",".map-button-close",function(){jQuery.magnificPopup.close()})}),redSHOP.postDanmark.loadLocationMobile=function(e){jQuery("#mapMobileSeachBox").select2({ajax:{url:redSHOP.RSConfig._("SITE_URL")+"index.php?option=com_redshop&view=checkout&task=getShippingInformation&tmpl=component&plugin=PostDanmark",dataType:"json",delay:250,data:function(e,t){return{zipcode:e,countryCode:"DK"}},results:function(e,t){var n=[];for(i=0;i<e.addresses.length;i++){var r='<div class="row-fluid"><div class="span10">';r+="<div>"+e.name[i]+"</div>",r+="<div>"+e.city[i]+" "+e.postalCode[i]+"</div>",r+="<div>"+e.addresses[i]+"</div>",r+="</div></div>";var s={id:e.servicePointId[i]+"|"+e.name[i]+"|"+e.addresses[i]+"|"+e.postalCode[i]+"|"+e.city[i],text:r,name:e.name[i],poingId:e.servicePointId[i],addresses:e.addresses[i],postalCode:e.postalCode[i],city:e.city[i]};n.push(s)}return{results:n}},cache:!0},escapeMarkup:function(e){return e},containerCssClass:"span4",minimumInputLength:4})};