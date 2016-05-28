function initAjaxCart() {
    observeProductFormSubmit();
    observeItemClick();
    $$('body')[0].insert({top: getAjaxWrap()});
}

function observeProductFormSubmit() {
    if (typeof packageProductAddToCartForm != 'undefined') {
        packageProductAddToCartForm.submit = function(args) {
            if (this.validator && this.validator.validate())
                callAjax('product_addtocart_form', 'form');
            return false;
        };
    }
}

function observeItemClick() {
    $$('.package-items .item-link, .reset-link').each(function(link) {
        link.observe('click', function(event) { 
            event.stop();
            callAjax(this.href, 'url');
            return false;
        });
    });
}

window.setLocation = function(url) {
    var is18X = (mageVersion[0] === "1" && mageVersion[1] === "8");
    if ((url.indexOf('.html') !== -1 && url.indexOf('.html?') === -1 && (is18X)) ||
        (url.indexOf('catalog/product/view/id') !== -1 && (is18X)) ||
        (url.indexOf('options=cart') !== -1) ||
        (url.indexOf('checkout/cart/add') !== -1) ||
        (url.indexOf('bcp/package/add') !== -1)
       ) {
            callAjax(url, 'url');
       } else {
            window.location.href = url;
    }
}

function getAjaxWrap() {
    str = '		<div id="ajaxwrap">';
    str = str + '               <div id="ajaxbox">';
    str = str + '                   <div id="ajax_process" style="display:block;"></div>';
    str = str + '                   <div id="ajax_content">';
    str = str + '                   <div id="confirmbox"></div>';
    str = str + '                   <a href="#" title="Close" class="close-popup" alt="Close" onclick="closeAjaxPopup();return false;"><em>Close</em></a></div>';
    str = str + '		</div>';
    str = str + '           <div id="ajax_overlay"></div>';
    str = str + '	</div>';
    str = str + '	';
    return str;
}

function fixWrapPosition() {
    var fixTop = (parseInt(window.innerHeight / 2) - parseInt($('ajaxbox').getHeight() / 2)) + 'px';
    $('ajaxbox').setStyle({top: fixTop});
}

function closeAjaxPopup() {
    $('confirmbox').innerHTML = '';
    $('ajaxwrap').setStyle({display: 'none'});
    $('ajax_overlay').setStyle({display: 'none'});
}

function callAjax(data, action) {
    var ajaxData = {
        encoding: 'UTF-8',
        method: 'post',
        parameters: {
            isAjax: true
        },
        onSuccess: function(result) {
            setResult(result.responseText.evalJSON());
        },
        onLoading: function() {
            updateAjaxBlock(true, true);
        },
        onFailure: function(msg) {
            Element.setInnerHTML(display, msg.responseText);
        }
    };

    if (action == 'url') {
        new Ajax.Request(data, ajaxData);
    } else {
        $(data).request(ajaxData);
    }
}

function setResult(result) {
    if(typeof(result.redirect) !== 'undefined') {
        callAjax(result.redirect, 'url');
        return;
    }
    if(typeof(result.update) !== 'undefined') {
        result.update.each(function(update){
            if($(update.element)) {
                $(update.element).update(update.content); 
            }
        });
        packageProductAddToCartForm = new VarienForm('product_addtocart_form');
        observeProductFormSubmit();
        observeItemClick();
        updateAjaxBlock(false);
        return;
    }
    if(typeof(result.error) !== 'undefined') {
        updateAjaxBlock(true, false, result.error);
        //location.reload();
        return;
    }
    if (!result.has_options) {
        setCartLink(result);
        getConfirm(result);
    } else {
        getProductOptions(result);
    }
}

function setCartLink(result) {
    var mycart_link = ($$('.top-link-cart') != '') ? '.top-link-cart' : '';
    $$(mycart_link).each(function(el) {
        el.innerHTML = result.link;
    });
}

function getProductOptions(result) {
    var scripts = result.content.extractScripts();
    for (i = 0; i < scripts.length; i++) {
        if (typeof(scripts[i]) != 'undefined' && i < 2) {
            try {
                eval(scripts[i]);
            }
            catch (e) {
                console.log(scripts[i]);
                console.debug(e);
            }
        }
        else {
            break;
        }
    }

    updateAjaxBlock(true, false, result.content.stripScripts());

    for (i; i < scripts.length; i++) {
        if (typeof(scripts[i]) != 'undefined') {
            try {
                eval(scripts[i]);
            } catch (e) {
                console.log(scripts[i]);
                console.debug(e);
            }
        }
    }
    ajaxProductAddToCartForm = new VarienForm('ajax_product_addtocart_form');
    decorateGeneric($$('#product-options-wrapper dl'), ['last']);
    if (typeof ajaxProductAddToCartForm != 'undefined') {
        ajaxProductAddToCartForm.submit = function(url) {
            if (this.validator && this.validator.validate()) {
                callAjax('ajax_product_addtocart_form', 'form');
            }
            return false;
        }
    }
}

function updateAjaxBlock(visible, show_process, content) {
    if (show_process) {
        $("ajax_process").innerHTML = loader;
        $("ajax_process").setStyle({display: "block"});
        $("ajax_overlay").onclick = null;
    } else {
        $("ajax_process").setStyle({display: "none"});
    }
    if (content) {
        $("ajax_content").setStyle({display: "block"});
        $("confirmbox").innerHTML = content;
    } else {
        $("ajax_content").setStyle({display: "none"});
    }
    if (visible) {
        if (!show_process) {
            $("ajax_overlay").onclick = function(e) {
                $("confirmbox").innerHTML = '';
                $("ajax_content").setStyle({display: "none"});
                $("ajaxwrap").setStyle({display: "none"});
                $("ajax_overlay").setStyle({display: "none"});
            };
        }
        $("ajax_overlay").setStyle({display: "block"});
        $("ajaxwrap").setStyle({display: "block"});
        fixWrapPosition();
    } else {
        $("confirmbox").innerHTML = '';
        $("ajax_content").setStyle({display: "none"});
        $("ajaxwrap").setStyle({display: "none"});
        $("ajax_overlay").setStyle({display: "none"});
    }
}

Product.Downloadable = Class.create();
Product.Downloadable.prototype = {
    config: {},
    initialize: function(config) {
        this.config = config;
        this.reloadPrice();
    },
    reloadPrice: function() {
        var price = 0;
        config = this.config;
        $$('.product-downloadable-link').each(function(elm) {
            if (config[elm.value] && elm.checked) {
                price += parseFloat(config[elm.value]);
            }
        });
        try {
            var _displayZeroPrice = optionsPrice.displayZeroPrice;
            optionsPrice.displayZeroPrice = false;
            optionsPrice.changePrice('downloadable', price);
            optionsPrice.reload();
            optionsPrice.displayZeroPrice = _displayZeroPrice;
        } catch (e) {

        }
    }
};

function validateDownloadableCallback(elmId, result) {
    var container = $('downloadable-links-list');
    if (result == 'failed') {
        container.removeClassName('validation-passed');
        container.addClassName('validation-failed');
    } else {
        container.removeClassName('validation-failed');
        container.addClassName('validation-passed');
    }
}

Product.Options = Class.create();
Product.Options.prototype = {
    initialize: function(config) {
        this.config = config;
        this.reloadPrice();
    },
    reloadPrice: function() {
        price = new Number();
        config = this.config;
        skipIds = [];
        $$('.product-custom-option').each(function(element) {
            var optionId = 0;
            element.name.sub(/[0-9]+/, function(match) {
                optionId = match[0];
            });
            if (this.config[optionId]) {
                if (element.type == 'checkbox' || element.type == 'radio') {
                    if (element.checked) {
                        if (config[optionId][element.getValue()]) {
                            price += parseFloat(config[optionId][element.getValue()]);
                        }
                    }
                } else if (element.hasClassName('datetime-picker') && !skipIds.include(optionId)) {
                    dateSelected = true;
                    $$('.product-custom-option[id^="options_' + optionId + '"]').each(function(dt) {
                        if (dt.getValue() == '') {
                            dateSelected = false;
                        }
                    });
                    if (dateSelected) {
                        price += parseFloat(this.config[optionId]);
                        skipIds[optionId] = optionId;
                    }
                } else if (element.type == 'select-one' || element.type == 'select-multiple') {
                    if (element.options) {
                        $A(element.options).each(function(selectOption) {
                            if (selectOption.selected) {
                                if (this.config[optionId][selectOption.value]) {
                                    price += parseFloat(this.config[optionId][selectOption.value]);
                                }
                            }
                        });
                    }
                } else {
                    if (element.getValue().strip() != '') {
                        price += parseFloat(this.config[optionId]);
                    }
                }
            }
        });
        try {
            optionsPrice.changePrice('options', price);
            optionsPrice.reload();
        } catch (e) {

        }
    }
}
function validateOptionsCallback(elmId, result) {
    var container = $(elmId).up('ul.options-list');
    if (result == 'failed') {
        container.removeClassName('validation-passed');
        container.addClassName('validation-failed');
    } else {
        container.removeClassName('validation-failed');
        container.addClassName('validation-passed');
    }
}

(function() {
    document.observe('dom:loaded', initAjaxCart);
})();
