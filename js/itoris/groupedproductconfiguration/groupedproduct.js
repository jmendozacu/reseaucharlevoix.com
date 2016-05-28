if (!Itoris) {
	var Itoris = {};
}

Itoris.GroupedProduct = Class.create({
	initialize : function(addUrl, imgArray, subProductUrl, currentUrl, showQtyAsCheck) {
		this.addEvents();
        this.showQtyAsCheck = showQtyAsCheck;
        this.displayCheckbox();
        var inputTextQty = $('super-product-table').select('.input-text.qty');
        for (var i = 0; i < inputTextQty.length; i++) {
            var acenter = this.acenterBox(inputTextQty[i]);
            this.displayOption(i, acenter);
        }
		$('product_addtocart_form').action = addUrl;
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'back_url';
        input.value = currentUrl;
        $('product_addtocart_form').appendChild(input);
        var priceFrom = $$('.price-from');
        for (var i = 0; i < priceFrom.length; i++) {
            if ($$('.itoris_grouped_product_bundle')[i]) {
                var idElement = $$('.itoris_grouped_product_bundle')[i].id;
                var part = idElement.split('_');
                var productId = part[part.length - 1];
                if (priceFrom[i].up()) {
                    priceFrom[i].up().update('<span id="product-price-'+productId+'" class="price"></span>');
                }
            }
        }
		for (var i = 0; i < $$('#super-product-table .price-box').length; i++) {
            var priceBox = $$('#super-product-table .price-box')[i];
			if (!priceBox.up('tr') || !priceBox.up('tr').select('td')[2] || !priceBox.up('tr').select('td')[2].select('input[class~=qty]')[0]) {
                var idElement = '';
                if (priceBox.select('.old-price')[0]) {
                    if (priceBox.select('.old-price')[0].select('.price')[0]) {
                        idElement = priceBox.select('.old-price')[0].select('.price')[0].id;
                    }
                } else if (priceBox.select('span')[0]) {
                    idElement = priceBox.select('span')[0].id;
                }
                var part = idElement.split('-');
                var productId = part[part.length - 1];
            } else {
                var inputQty = priceBox.up('tr').select('td')[2].select('input[class~=qty]')[0];
                var matches = inputQty.name.match(/[[0-9]+]/);
                if (matches.length) {
                    var productId = matches[0].substr(1, matches[0].length - 2);
                }
            }
            if (!priceBox.up('tr') || !priceBox.up('tr').select('td')[0]) continue;
            var tdName = priceBox.up('tr').select('td')[0];
            if (!tdName.hasClassName('image')) {
                tdName.setStyle({textAlign:'center'});
                var a = document.createElement('a');
                Element.extend(a);
                if (imgArray[productId]) {
                    var image = document.createElement('img');
                    Element.extend(image);
                    image.addClassName('itoris_grouped_product_image');
                    image.src = imgArray[productId];
                    a.appendChild(image);
                }
                if (subProductUrl[productId]) {
                    a.href = subProductUrl[productId];
                    if (imgArray[productId]) {
                        tdName.innerHTML = '<br/>' + '<a href="'+ subProductUrl[productId] +'">' + tdName.innerHTML + '</a>';
                    } else {
                        tdName.innerHTML = '<a href="'+ subProductUrl[productId] +'">' + tdName.innerHTML;
                    }
                } else if (imgArray[productId]) {
                    tdName.innerHTML = '<br/>' + tdName.innerHTML;
                }
                tdName.insert({top:a});
            }
		}
		this.checkFileUploads();
	},
	addEvents : function() {
		for (var i = 0; i < $('super-product-table').select('.input-text.qty').length; i++) {
			$('super-product-table').select('.input-text.qty')[i].addClassName('itoris_input_qty');
			$('super-product-table').select('.input-text.qty')[i].alreadyCreate = false;
            var acenter = this.acenterBox($('super-product-table').select('.input-text.qty')[i]);
			Event.observe($('super-product-table').select('.input-text.qty')[i], 'keyup', this.displayOption.bind(this, i, acenter));
		}
	},
    acenterBox : function(qtyBox) {
        if ($('super-product-table').select('.a-center').length) {
            for (var j = 0; j < $('super-product-table').select('.a-center').length; j++) {
                if ($('super-product-table').select('.a-center')[j] == qtyBox.up('td')) {
                    return j;
                }
            }
        } else if ($('super-product-table').select('.name').length) {
            for (var j = 0; j < $('super-product-table').select('.name').length; j++) {
                if ($('super-product-table').select('.name')[j] == qtyBox.up('td')) {
                    return j;
                }
            }
        }

        return 0;
    },
    displayCheckbox : function() {
        var currentQtyBox = $('super-product-table').select('.itoris_input_qty');
        if (this.showQtyAsCheck) {
            if ($('super-product-table').select('thead')[0] && $('super-product-table').select('thead')[0].select('.a-center')[0]) {
                $('super-product-table').select('thead')[0].select('.a-center')[0].update('');
            }
        }
        for (var i = 0; i < currentQtyBox.length; i++) {
            if (this.showQtyAsCheck) {
                currentQtyBox[i].hide();
                var checkbox = document.createElement('input');
                Element.extend(checkbox);
                Event.observe(checkbox, 'click', this.eventCheckbox.bind(this, checkbox, currentQtyBox[i], i));

                checkbox.type = 'checkbox';
                if (currentQtyBox[i].value > 0) {
                    checkbox.checked = 'checked';
                }
                var tr = currentQtyBox[i].up();
                if (tr) {
                    tr.appendChild(checkbox);
                }

                // for magento 1.9
                if (currentQtyBox[i].up('td') && currentQtyBox[i].up('td').hasClassName('name')) {
                    if (currentQtyBox[i].up('td').select('.qty-label')[0]) {
                        currentQtyBox[i].up('td').select('.qty-label')[0].hide();
                    }
                }
            } else {
                currentQtyBox[i].show();
            }
        }
    },
    eventCheckbox : function(checkbox, qtyInput, numberQtyBox) {
        if (checkbox.checked) {
            qtyInput.value = 1;
        } else {
            qtyInput.value = 0;
        }
        var acenter = this.acenterBox(qtyInput);
        this.displayOption(numberQtyBox, acenter)
    },
	displayOption : function(numberQtyBox, acenterBox) {
		var currentQtyBox = $('super-product-table').select('.itoris_input_qty')[numberQtyBox];
        if (currentQtyBox) {
            var nameSuperGroup = currentQtyBox.name;
            var part = nameSuperGroup.split('[')[1];
            var idProduct = part.split(']')[0];
            if ($('itoris_grouped_product_' + idProduct)) {
                var tr = null;
                var td = null;
                if (!currentQtyBox.alreadyCreate) {
                    tr = document.createElement('tr');
                    Element.extend(tr);
                    tr.addClassName('itoris_grouped_product_box_'+idProduct);
                    td = document.createElement('td');
                    Element.extend(tr);
                    tr.appendChild(td);
                    td.colSpan = 3;
                    tr.hide();
                    currentQtyBox.alreadyCreate = true;
                } else {
                    tr = $$('.itoris_grouped_product_box_'+idProduct)[0];
                    td = $$('.itoris_grouped_product_box_'+idProduct + ' td')[0];
                }
                tr.style.position = 'relative';
                var centralBlock = $('super-product-table').select('.a-center').length
                    ? $('super-product-table').select('.a-center').length
                    : ($('super-product-table').select('.name').length ? $('super-product-table').select('.name').length : 0);
                if (centralBlock != acenterBox+1) {
                    if ($('super-product-table').select('.a-center').length) {
                        var qtyBox = $('super-product-table').select('.a-center')[acenterBox+1];
                    } else if ($('super-product-table').select('.name').length) {
                        var qtyBox = $('super-product-table').select('.name')[acenterBox+1];
                    }
                    if (qtyBox) {
                        var productLine = qtyBox.up();
                        qtyBox.up().up().insertBefore(tr, productLine);
                    }
                    td.appendChild($('itoris_grouped_product_' + idProduct));
                } else {
                    if ($('super-product-table').select('.a-center').length) {
                        var tbody = currentQtyBox.up().up().up();
                    } else {
                        var tbody = currentQtyBox.up('tbody');
                    }
                    if (currentQtyBox.up().up().hasClassName('last')) {
                        currentQtyBox.up().up().removeClassName('last');
                    }
                    tbody.appendChild(tr);
                    td.appendChild($('itoris_grouped_product_' + idProduct));
                }
                var hasInputBox = $('itoris_grouped_product_' + idProduct).select('.input-box, .product-options');
                for (var i = 0; i < hasInputBox.length; i++) {
                    var inputBox = hasInputBox[i];
                    for (var j = 0; j < inputBox.select('.product-custom-option').length; j++) {
                        Event.observe(inputBox.select('.product-custom-option')[j], 'change', function(e) {
                            e.stop();
                            itorisGroupedOptions[idProduct].reloadPrice();
                        });
                    }
                    for (var j = 0; j < inputBox.select('.super-attribute-select').length; j++) {
                        Event.observe(inputBox.select('.super-attribute-select')[j], 'change', function(e) {
                            e.stop();
                            itorisGroupedOptions[idProduct].reloadPrice();
                        });
                    }
                    for (var j = 0; j < inputBox.select('.change-container-classname').length; j++) {
                        Event.observe(inputBox.select('.change-container-classname')[j], 'change', function(e) {
                            e.stop();
                            itorisGroupedOptions[idProduct].reloadPrice();
                        });
                    }
                }
                var slideBlock = $('itoris_grouped_product_' + idProduct);
                if (parseInt(currentQtyBox.value) >= 1) {
                    if (slideBlock.getStyle('display') == 'none') {
                        slideBlock.style.display = 'block';
                        tr.style.display = '';
                        tr.style.overflow = 'hidden';
                        var origHeight = slideBlock.getHeight();
                        slideBlock.style.height = 0;
                        new Effect.Morph(slideBlock, {
                            style: 'height:' + origHeight + 'px;',
                            duration: 1,
                            afterFinishInternal : function(effect) {
                                slideBlock.style.height = '';
                                if (!(parseInt(currentQtyBox.value) >= 1)) {
                                    slideBlock.style.display = 'none';
                                    tr.style.display = 'none';
                                }
                            }
                        });
                        //new Effect.BlindDown(tr, { duration: 3.0 });
                        //new Effect.BlindDown($('itoris_grouped_product_' + idProduct));
                    }
                } else {
                    new Effect.Morph(slideBlock, {
                        style: 'height:0;',
                        duration: 1,
                        afterFinishInternal : function(effect) {
                            slideBlock.style.height = '';
                            if (!(parseInt(currentQtyBox.value) >= 1)) {
                                slideBlock.style.display = 'none';
                                tr.style.display = 'none';
                            }
                        }
                    });
                    //new Effect.BlindUp(tr);
                    //new Effect.BlindUp($('itoris_grouped_product_' + idProduct));
                }

            }
        }
	},
	checkFileUploads : function() {
		var form = $('product_addtocart_form');
		if (form) {
			if (form.select('input[type=file]').length) {
				form.enctype = 'multipart/form-data';
			}
		}
	}
});
