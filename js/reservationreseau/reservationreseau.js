
function selectionSiegeRender(myDate, controllerPath, productId){
	var siegeClass = ".siege" + productId;
	jQuery(siegeClass).css( 'cursor', 'pointer' );
	jQuery(siegeClass).css("background-color", "green");
	jQuery(siegeClass).click(selectSiege);
	jQuery(siegeClass).removeAttr('checked');
	var inputOptionSiegeId = "#options_"  + productId + "_selectionsiege";
	var inputOptionTempSiegeId = "#options_temp_"  + productId + "_selectionsiege";
	var inputQty = "#inputQty" + productId ;
	
	jQuery(inputOptionSiegeId).val("");
	jQuery(inputOptionTempSiegeId).val("");
	
	var seigesXpos = jQuery("#seigesXpos").val();
	var seigesAlone = jQuery("#seigesAlone").val();
	if(seigesXpos != 1 && seigesAlone != 1){
		masterQtyFieldId.forEach(function(entry) {
			var inputOptionMasterQty = "[name='" + entry + "']";
			jQuery(inputOptionMasterQty).change(function() {
				jQuery(siegeClass).removeAttr('checked');
				jQuery(siegeClass).text(" ");
				jQuery(inputOptionSiegeId).val("");
				jQuery(inputOptionTempSiegeId).val("");
				jQuery(inputQty).val(0);
			});
		});
	}
	
	
	
	
	var currentSalleIdFieldId = "#salle" + productId;
	var currentSalleId = jQuery(currentSalleIdFieldId).val();
	new Ajax.Request(controllerPath, {
	    method: 'Post',
	    parameters: {salleId:currentSalleId, heureDateDebut:encodeURIComponent(myDate), confirme:1 },
	    onComplete: function(transport) {
	    	//alert(transport.responseText);
	    	var reservations = JSON.parse(transport.responseText);
	    	for(index = 0; index < reservations.totalRecords; index++){
	    		var siege = reservations.items[index];
	    		var divId = "#p" + productId + "s" + siege.siege_id;
	    		jQuery(divId).css("background-color", "red");
	    		jQuery(divId).css( 'cursor', 'default' );
	    		jQuery(divId).unbind( "click" );
	    	}
	    }
	});
	new Ajax.Request(controllerPath, {
	    method: 'Post',
	    parameters: {salleId:currentSalleId, heureDateDebut:encodeURIComponent(myDate), confirme:0 },
	    onComplete: function(transport) {
	    	//alert(transport.responseText);
	    	var reservations = JSON.parse(transport.responseText);
	    	for(index = 0; index < reservations.totalRecords; index++){
	    		var siege = reservations.items[index];
	    		var divId = "#p" + productId + "s" + siege.siege_id;
	    		jQuery(divId).css("background-color", "yellow");
	    		jQuery(divId).css( 'cursor', 'default' );
	    		jQuery(divId).unbind( "click" );
	    	}
	    }
	});
}

function deselectionSiegeRender(productId){
	var siegeClass = ".siege" + productId;
	jQuery(siegeClass).css("background-color", "grey");
	jQuery(siegeClass).css( 'cursor', 'default' );
	jQuery(siegeClass).unbind( "click" );
	jQuery(siegeClass).text(" ");

	var inputQty = "#inputQty" + productId ;
	var inputOptionSiegeId = "#options_"  + productId + "_selectionsiege";
	var inputOptionTempSiegeId = "#options_temp_"  + productId + "_selectionsiege";
	
	jQuery(inputQty).val(0);
	jQuery(inputOptionSiegeId).val("");
	jQuery(inputOptionTempSiegeId).val("");
}

function selectSiege(event){
	var productId = event.target.id.split("s")[0].substring(1);
	var siegeId = "s" + event.target.id.split("s")[1];
	
	var inputOptionSiegeId = "#options_"  + productId + "_selectionsiege";
	var inputOptionTempSiegeId = "#options_temp_"  + productId + "_selectionsiege";
	
	var inputOptionQtyFieldId= "#inputQtyId" + productId;
	var inputOptionQty = jQuery(inputOptionQtyFieldId).val();
	var inputOptionQty = "[name='" + inputOptionQty + "']";
	
	var inputQty = "#inputQty" + productId ;
	
	//alert(inputOptionQty);
	var divId = "#"+ event.target.id;
	var inputText = jQuery(inputOptionTempSiegeId).val();
	if(jQuery(divId).attr( 'checked') === 'checked'){
		jQuery(divId).removeAttr('checked');
		jQuery(divId).text(" ");
		inputText = inputText.replace(siegeId, "");
		inputText = inputText.replace(",,",",");
		//if(inputText.substring(inputText.length-1, inputText.length) == ",") inputText = inputText.substring(0, inputText.length-1); 
		if(inputText.substring(0, 1) == ",") inputText = inputText.substring(1, inputText.length); 
		jQuery(inputOptionTempSiegeId).val(inputText);
		
		var qtySiege = jQuery(inputQty).val();
		qtySiege--;
		jQuery(inputQty).val(qtySiege);
		
	}
	else{
		jQuery(divId).text("✓");
		jQuery(divId).attr( 'checked','checked'  );
		if(inputText == "" || inputText == null){
			jQuery(inputOptionTempSiegeId).val(siegeId + ',');
		}
		else{
			jQuery(inputOptionTempSiegeId).val(inputText + siegeId + ',');
		}
		var qtySiege = jQuery(inputQty).val();
		qtySiege++;
		jQuery(inputQty).val(qtySiege);
	}
	var seigesXpos = jQuery("#seigesXpos").val();
	var seigesAlone = jQuery("#seigesAlone").val();
	if(seigesXpos == 1 || seigesAlone == 1){
		jQuery(inputOptionSiegeId).val(jQuery(inputOptionTempSiegeId).val());
	}
	else{
		
		var qtyTicket = 0;
		masterQtyFieldId.forEach(function(entry) {
			var inputOptionMasterQty = "[name='" + entry + "']";
			
			qtyTicket = qtyTicket + Number(jQuery(inputOptionMasterQty).val());
		});
		
		if(jQuery(inputQty).val() == qtyTicket){
			jQuery(inputOptionSiegeId).val(jQuery(inputOptionTempSiegeId).val());
		}
		else{
			jQuery(inputOptionSiegeId).val("");
	
		}
	}
}

function selectionSiege(inputOptionQty, qtyIdx, acenterIdx, itemId){
	inputOptionQty = "[name='" + inputOptionQty + "']";
	selectLinkId = "#selectionSiege" + itemId;
	deselectLinkId = "#deselectionSiege" + itemId;
	jQuery(selectLinkId).hide();
	jQuery(deselectLinkId).show();
	jQuery(inputOptionQty).val(1);
	itorisGroupedProduct.displayOption(qtyIdx,acenterIdx);
	
	var siegeClass = ".siege" + itemId;
	var inputOptionSiegeId = "#options_"  + itemId + "_selectionsiege";
	var inputOptionTempSiegeId = "#options_temp_"  + itemId + "_selectionsiege";
	var inputQty = "#inputQty" + itemId ;
	
	jQuery(siegeClass).removeAttr('checked');
	jQuery(siegeClass).text(" ");
	jQuery(inputOptionSiegeId).val("");
	jQuery(inputOptionTempSiegeId).val("");
	jQuery(inputQty).val(0);
	
}

function deselectionSiege(inputOptionQty, qtyIdx, acenterIdx, itemId){
	inputOptionQty = "[name='" + inputOptionQty + "']";
	jQuery(inputOptionQty).val(0);
	selectLinkId = "#selectionSiege" + itemId;
	deselectLinkId = "#deselectionSiege" + itemId;
	jQuery(selectLinkId).show();
	jQuery(deselectLinkId).hide();
	itorisGroupedProduct.displayOption(qtyIdx,acenterIdx);
	
}

