jQuery(document).ready(function(){

	/*********************************************************************************************************** Superfish Menu *********************************************************************/
	/* toggle nav */
	jQuery("#menu-icon").on("click", function(){
		jQuery(".sf-menu").slideToggle();
		jQuery(this).toggleClass("active");
	});

	if (jQuery('.container_24').width() < 450) {
		jQuery('.sf-menu').removeClass('sf-js-enabled').find('li.parent').append('<strong></strong>');
		jQuery('.sf-menu li.parent strong').on("click", function(){
			if (jQuery(this).attr('class') == 'opened') { jQuery(this).removeClass().parent('li.parent').find('> ul').slideToggle(); } 
				else {
					jQuery(this).addClass('opened').parent('li.parent').find('> ul').slideToggle();
				}
		});
	};

	/*********************************************************************************************************** Cart Truncated *********************************************************************/
	
	if (jQuery('.container_24').width() < 450) {
		jQuery('.truncated span').click(function(){
				jQuery(this).parent().find('.truncated_full_value').stop().slideToggle();
			}
		)
	}
	else {
		jQuery('.truncated span').hover(function(){
				jQuery(this).parent().find('.truncated_full_value').stop().slideToggle();
			}
		)
	};

	/*********************************************************************************************************** Product View Accordion *********************************************************************/
	if (jQuery('.container_24').width() < 450) {
		jQuery('.box-collateral').not('.box-up-sell').find('h2').append('<span class="toggle"></span>');
		
		jQuery('.review-product-list .box-collateral h2 > span').addClass('opened').parents('.box-collateral').find(' > .box-collateral-content').css({'display':'block'});
		
		jQuery('.box-collateral h2').on("click", function(){
			if (jQuery(this).find('> span').attr('class') == 'toggle opened') { jQuery(this).find('> span').removeClass('opened').parent('h2').parent('.box-collateral').find(' > .box-collateral-content').slideToggle(); }
			else {
				jQuery(this).find('> span').addClass('opened').parent('h2').parent('.box-collateral').find(' > .box-collateral-content').slideToggle();
			}
		});
	};

	/*********************************************************************************************************** Sidebar Accordion *********************************************************************/
	if (jQuery('.container_24').width() < 450) {
		jQuery('.sidebar .block .block-title').append('<span class="toggle"></span>');
		jQuery('.sidebar .block .block-title').on("click", function(){
			if (jQuery(this).find('> span').attr('class') == 'toggle opened') { jQuery(this).find('> span').removeClass('opened').parents('.block').find('.block-content').slideToggle(); }
			else {
				jQuery(this).find('> span').addClass('opened').parents('.block').find('.block-content').slideToggle();
			}
		});
	};

	/*********************************************************************************************************** Footer Accordion *********************************************************************/
	if (jQuery('.container_24').width() < 450) {
		jQuery('.footer h4').append('<span class="toggle"></span>');
		jQuery('.footer h4').on("click", function(){
			if (jQuery(this).find('span').attr('class') == 'toggle opened') { jQuery(this).find('span').removeClass('opened').parents('.footer-col').find('.footer-col-content').slideToggle(); }
			else {
				jQuery(this).find('span').addClass('opened').parents('.footer-col').find('.footer-col-content').slideToggle();
			}
		});
	};

	/*********************************************************************************************************** Header Buttons *********************************************************************/
	if (jQuery('.container_24').width() > 800) {
		jQuery('.header-button').not('.top-login').hover(
			function(){
				/*ListHeight = jQuery(this).find('ul').height();*/
				jQuery(this).find('a').css({'background-color': '#024e92'}).parent().find('ul').toggle()/*css({'display':'block','height':'0'}).stop().animate({height:ListHeight, opacity: 1}, 200)*/
			},
			function(){jQuery(this).find('a').css({'background-color': 'transparent'}).parent().find('ul').toggle()/*stop().animate({height:'0', opacity: 0}, 200, 
				function() {
					jQuery(this).css({'display':'none', 'height':ListHeight})
				});*/
			}
		);
	}
	else {
		jQuery('.header-button').not('.top-login').click(
			function(){
				Ulheight = jQuery(this).find('ul').css('display');
				if (Ulheight == 'none') {
					jQuery('.header-button').find('ul').hide(0);
					jQuery(this).find('ul').show(0);
				}
				else {
					jQuery(this).find('ul').hide(0);
				}
			}
		)
	};
	
	qwe = jQuery('.lang-list ul li span').text();
	jQuery('.lang-list > a').text(qwe);
});

/*************************************************************************************************************back-top*****************************************************************************/
jQuery(function () {
 jQuery(window).scroll(function () {
  if (jQuery(this).scrollTop() > 100) {
   jQuery('#back-top').fadeIn();
  } else {
   jQuery('#back-top').fadeOut();
  }
 });

 // scroll body to 0px on click
 jQuery('#back-top a').click(function () {
  jQuery('body,html').stop(false, false).animate({
   scrollTop: 0
  }, 800);
  return false;
 });
});

/***************************************************************************************************** Magento class **************************************************************************/
jQuery(document).ready(function() {
	jQuery('.sidebar .block').last().addClass('last_block');
	jQuery('.sidebar .block').first().addClass('first');
	jQuery('.box-up-sell li').eq(2).addClass('last');
	jQuery('.product-collateral #customer-reviews dl dd, #cart-sidebar .item').last().addClass('last');
    jQuery('.product-view .product-img-box .more-views li:nth-child(4)').last().addClass('item-4');
    jQuery('.header .row-2 .links').first().addClass('LoginLink');
	jQuery('#checkout-progress-state li:odd').addClass('odd');
	jQuery('.product-view .product-img-box .product-image').append('<span></span>');
  
	if (jQuery('.container_24').width() < 450) {
		jQuery('.my-account table td.order-id').prepend('<strong>Order #:</strong>');
		jQuery('.my-account table td.order-date').prepend('<strong>Date: </strong>');
		jQuery('.my-account table td.order-ship').prepend('<strong>Ship To: </strong>');
		jQuery('.my-account table td.order-total').prepend('<strong>Order Total: </strong>');
		jQuery('.my-account table td.order-status').prepend('<strong>Status: </strong>');
		jQuery('.my-account table td.order-sku').prepend('<strong>SKU: </strong>');
		jQuery('.my-account table td.order-price').prepend('<strong>Price: </strong>');
		jQuery('.my-account table td.order-subtotal').prepend('<strong>Subtotal: </strong>');
		
		jQuery('.multiple-checkout td.order-qty, .multiple-checkout th.order-qty').prepend('<strong>Qty: </strong>');
		jQuery('.multiple-checkout td.order-shipping, .multiple-checkout th.order-shipping, ').prepend('<strong>Send To: </strong>');
		jQuery('.multiple-checkout td.order-subtotal, .multiple-checkout th.order-subtotal').prepend('<strong>Subtotal: </strong>');
		jQuery('.multiple-checkout td.order-price, .multiple-checkout th.order-price').prepend('<strong>Price: </strong>');
	}
});

jQuery(document).ready(function() {
	if (jQuery('.container_24').width() < 450) {
		jQuery('.related-carousel').jcarousel({
			vertical: false,
			visible:1,
			scroll: 1
		});
	}
	else {
		jQuery('.related-carousel').jcarousel({
			vertical: false,
			visible:3,
			scroll: 1
		});
	}
});
