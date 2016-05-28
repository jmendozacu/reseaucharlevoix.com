var widthClassOptions = [];
var widthClassOptions = ({
		bestseller       : 'bestseller_default_width',
		newproduct       : 'newproduct_default_width',
		featured         : 'featured_default_width',
		special          : 'special_default_width',
		additional       : 'additional_default_width',
		related          : 'related_default_width',
		upsell	         : 'upsell_default_width',
		crosssell        : 'crosssell_default_width',
		brand			 : 'brand_default_width',
		 blog				  : 'blog_default_width'
});

var $k =jQuery.noConflict();
$k(document).ready(function(){
	
	
	
	
	$k('input[type="checkbox"]').tmMark(); 
	$k('input[type="radio"]').tmMark();
	$k(".form-language select").selectbox();	
	$k(".tm_top_currency select").selectbox();
	$k(".limiter select").selectbox();
	$k(".sort-by select").selectbox(); 
	$k(".block-brand-nav select").selectbox();
	
	$k('.cart-label').click(function() {
		
			if ($k(".tm_headerlinkmenu.active")[0]){
			$k(".tm_headerlinkmenu").removeClass("active");
			$k(".tm_headerlinks_inner").slideToggle('slow');	
			}
			
    	 $k('#panel').slideToggle();
    	 $k("#panel").parent().toggleClass('active').parent().find().slideToggle();
  	});  
	
	$k('.nav-responsive').click(function() {
		$k('#nav-mobile').slideToggle();
		$k('.nav-responsive div').toggleClass('active');		
    });
	
	$k("#category-treeview").treeview({
		animated: "slow",
		collapsed: true,
		unique: true		
	});
	
	$k(document).ready(function(){
	    $k(".headertoggle_img").click(function(){
		$k(".tm_headerlinks_inner").parent().toggleClass('active')					
		$k(".tm_headerlinks_inner").slideToggle('slow');
				if ($k(".block-cart.btn-slide.active")[0]){
					$k(".block-cart.btn-slide").removeClass("active");
						$k('#panel').slideToggle();
				}
				
					 });
	}); 
	
	$k(window).load(function() {	
  	$k("#spinner").fadeOut("slow");
	});	
	
	$k(document).ready(function(){
 		$k(".search_button").click(function(){
 			$k('.form_header_search').addClass("search-selected");
		});
	});

	
	
	
	 $k(".lightbox").colorbox({ 
		opacity:  0.5,
		speed:    400,
        width: 560,
        height: 560
      });	

$k('.megnor-advanced-menu-popup a').on('click touchend', function(e) {						
    var el = $k(this);
    var link = el.attr('href');
    window.location = link;
});

$k('.arrow').click(function(){
        var subflex = $k('.sub-flex');
        $k("html, body").animate({scrollTop:subflex.height()-80}, 1000, 'swing');
});
	
$k(".sub-flex .banner_inner1").hover(function(){
	if ($k(window).width() > 979){
	$k(".sub-flex .banner_inner2").addClass('sub-active2')	;																	  
	var fwidth=$k(window).width();
	var fwidth1=fwidth/2;
	var fwidth2=fwidth1+70;
	var fwidth3=fwidth1-70;
	var slider1=$k('.cms-home .sub-flex1');
	var slider2=$k('.cms-home .sub-flex2');
	slider1.css("width",fwidth2);
	slider2.css("width",fwidth3);
	}
}, function(){
	if ($k(window).width() > 979){
   $k(".sub-flex .banner_inner2").removeClass('sub-active2')	;							
	var fwidth=$k(window).width();
	var fwidth1=fwidth/2;
	var slider1=$k('.cms-home .sub-flex1');
	var slider2=$k('.cms-home .sub-flex2');
	slider1.css("width",fwidth1);
	slider2.css("width",fwidth1);
	}   
});	

$k(".sub-flex .banner_inner2").hover(function(){

if ($k(window).width() > 979){
	$k(".sub-flex .banner_inner1").addClass('sub-active1')	;									  
	var fwidth=$k(window).width();
	var fwidth1=fwidth/2;
	var fwidth2=fwidth1+70;
	var fwidth3=fwidth1-70;
	var slider1=$k('.cms-home .sub-flex1');
	var slider2=$k('.cms-home .sub-flex2');
	slider1.css("width",fwidth3);
	slider2.css("width",fwidth2);				  
}

}, function(){
	if ($k(window).width() > 979){
   $k(".sub-flex .banner_inner1").removeClass('sub-active1')			
   var fwidth=$k(window).width();
	var fwidth1=fwidth/2;
	var slider1=$k('.cms-home .sub-flex1');
	var slider2=$k('.cms-home .sub-flex2');
	slider1.css("width",fwidth1);
	slider2.css("width",fwidth1);
	}
   
});	


		$k(document).ready(function(){
	  			$k(".thme-toggle-arrow").click(function(){
														
						var themeheight=$k(window).height();  
					if ($k(window).width() > 767){
					$k(".theme-toggle-inner").css("height",themeheight);
					}
						$k(".thme-toggle-arrow").toggleClass("active")
						$k(".thme-toggle-arrow .menu-opener-inner").toggleClass("active")
						$k(".theme-toggle-inner").slideToggle('slow');
						$k(".theme-toggle-inner").toggleClass('link-active');
						$k(".header-container").toggleClass('header-menu');
						//alert(themeheight);
						
					});
		}); 
	 


$k(".form_button").click(function() {
			$k('.form_content').toggle('fast', function() {
				$k('.form_content').toggleClass('active');
			});
			if ($k(".tm_headerlinkmenu.active")[0]){
			$k(".tm_headerlinkmenu").removeClass("active");
			$k(".tm_headerlinks_inner").slideToggle('slow');	
			}
			if ($k(".block-cart.btn-slide.active")[0]){
					$k(".block-cart.btn-slide").removeClass("active");
						$k('#panel').slideToggle();
			}
			
			
			$k('.header .form-search input.input-text').attr('autofocus', 'autofocus').focus();
});	
	


	
	 
});

	 
 
function mobileToggleMenu(){
	
	if ($k(window).width() < 980)
	{
		$k("#footer .mobile_togglemenu,#footer .block .block-title .mobile_togglemenu").remove();
		$k("#footer h6 , #footer .block .block-title").append( "<a class='mobile_togglemenu'>&nbsp;</a>" );
		$k("#footer h6 , #footer .block .block-title").addClass('toggle');
		$k("#footer .mobile_togglemenu ,#footer .block .block-title .mobile_togglemenu ").click(function(){
			$k(this).parent().toggleClass('active').parent().find('ul').toggle('slow');
		});

	}else{
		$k("#footer h6 , #footer .block .block-title").parent().find('ul').removeAttr('style');
		$k("#footer h6 , #footer .block .block-title").removeClass('active');
		$k("#footer h6 , #footer .block .block-title").removeClass('toggle');
		$k("#footer .mobile_togglemenu ,#footer .block .block-title .mobile_togglemenu ").remove();
	}	
}
$k(document).ready(function(){mobileToggleMenu();});
$k(window).resize(function(){mobileToggleMenu();}); 

function mobileToggleColumn(){
	
	if ($k(window).width() < 768){	 
		$k('.sidebar .mobile_togglecolumn').remove();
		$k(".sidebar .block-title" ).append( "<span class='mobile_togglecolumn'>&nbsp;</span>" );		 
		$k(".sidebar .block-title" ).addClass('toggle');	 
		$k(".sidebar .mobile_togglecolumn").click(function(){
			$k(this).parent()	.toggleClass('active');	 												   
			$k(this).parent().toggleClass('active').parent().find('.block-content').toggle('slow');
		});
	}
	else{ 
		$k(".sidebar .block-title" ).parent().find('.block-content').removeAttr('style');		 
		$k(".sidebar .block-title" ).removeClass('toggle');		 
		$k(".sidebar .block-title" ).removeClass('active');
		$k('.sidebar .mobile_togglecolumn').remove();		 
	}
}
$k(document).ready(function(){mobileToggleColumn();});
$k(window).resize(function(){mobileToggleColumn();});


function menuResponsive(){
	 
	if ($k(window).width() < 980){ 
		$k('#advancedmenu').css('display','none');
		$k('.advanced_nav').css('display','block');
		$k('.nav-responsive').css('display','none');
		 var elem = document.getElementById("nav");
				 if(typeof elem !== 'undefined' && elem !== null) {
					document.getElementById("nav").id= "nav-mobile";
		
	
			$k(".nav-inner").addClass('responsive-menu');			 
			$k(".nav-inner #nav-mobile").treeview({
				animated: "slow",
				collapsed: true,
				unique: true		
			});
			$k('.nav-inner #nav-mobile a.active').parent().removeClass('expandable');
			$k('.nav-inner #nav-mobile a.active').parent().addClass('collapsable');
			$k('.nav-inner #nav-mobile .collapsable ul').css('display','block');	
			
		
	  } 	 
	}else{
		$k('#advancedmenu').css('display','block');
		$k('.advanced_nav').css('display','block');
		$k('.nav-responsive').css('display','none');	
		$k(".nav-inner .hitarea").remove();
		$k(".nav-inner").removeClass('responsive-menu');	 
		$k("#nav-mobile").removeClass('treeview');
		$k(".nav-inner ul").removeAttr('style');
		$k('.nav-inner li').removeClass('expandable');
		$k('.nav-inner li').removeClass('collapsable');		 
		 var elem = document.getElementById("nav-mobile");
			 if(typeof elem !== 'undefined' && elem !== null) {
				document.getElementById("nav-mobile").id= "nav";
		}
	}

}
$k(document).ready(function(){menuResponsive();});
$k(window).resize(function(){menuResponsive();});
 
 
function productCarouselAutoSet() { 
	$k(".main .product-carousel, .additional-carousel .product-carousel").each(function() {
		var objectID = $k(this).attr('id');
		var myObject = objectID.replace('-carousel','');
		if(myObject.indexOf("-") >= 0)
			myObject = myObject.substring(0,myObject.indexOf("-"));		
		if(widthClassOptions[myObject])
			var myDefClass = widthClassOptions[myObject];			
		else
			var myDefClass= 'grid_default_width';
		var slider = $k(".main #" + objectID + ", .additional-carousel #"+ objectID );		
		slider.sliderCarousel({
			defWidthClss : myDefClass,
			subElement   : '.slider-item',
			subClass     : 'product-block',
			firstClass   : 'first_item_tm',
			lastClass    : 'last_item_tm',
			slideSpeed : 200,
			paginationSpeed : 800,
			autoPlay : false,
			stopOnHover : false,
			goToFirst : true,
			goToFirstSpeed : 1000,
			goToFirstNav : true,
			pagination :true,
			paginationNumbers: false,
			responsive: true,
			responsiveRefreshRate : 200,
			baseClass : "slider-carousel",
			theme : "slider-theme",
			autoHeight : true
		});
		
		var nextButton = $k(this).parent().find('.next');
		var prevButton = $k(this).parent().find('.prev');
		nextButton.click(function(){
			slider.trigger('slider.next');
		})
		prevButton.click(function(){
			slider.trigger('slider.prev');
		})
	});
}
//$(window).load(function(){productCarouselAutoSet();});
$k(document).ready(function(){productCarouselAutoSet();});


function productListAutoSet() { 
	$k("ul.products-grid").each(function(){	
	var objectID = $k(this).attr('id');
		if(objectID.length >0){		
			if(widthClassOptions[objectID.replace('-grid','')])
				var myDefClass= widthClassOptions[objectID.replace('-grid','')];
			}else{ 
			var myDefClass= 'grid_default_width';			
		}	
		$k(this).smartColumnsRows({
			defWidthClss : myDefClass,
			subElement   : 'li',
			subClass     : 'product-block'
		});
	});		
}
$k(window).load(function(){productListAutoSet();});
$k(window).resize(function(){productListAutoSet();}); 

 
function tableMakeResponsive(){
	 if ($k(window).width() < 640){
		// SHOPPING CART TABLE
		if($k("table#shopping-cart-table").length != 0) {
			if($k("#cart-shopping-table").length == 0) {
				$k('<div id="cart-shopping-table"></div>').insertBefore('.cart-table');
			}
			$k('table#shopping-cart-table').addClass("table-responsive");
			$k('table#shopping-cart-table thead').addClass("table-head");
			$k('table#shopping-cart-table tfoot').addClass("table-foot");
			$k('table#shopping-cart-table tr').addClass("row-responsive");
			$k('table#shopping-cart-table td').addClass("column-responsive clearfix");
			$k("table#shopping-cart-table").responsiveTable({prefix:'tm_responsive',target:'#cart-shopping-table'});
		}		
	}else{
		// SHOPPING CART TABLE
		if($k("table#shopping-cart-table").length != 0) {
			$k('table#shopping-cart-table').removeClass("table-responsive");
			$k('table#shopping-cart-table thead').removeClass("table-head");
			$k('table#shopping-cart-table tfoot').removeClass("table-foot");
			$k('table#shopping-cart-table tr').removeClass("row-responsive");
			$k('table#shopping-cart-table td').removeClass("column-responsive");
			$k("#cart-shopping-table").remove();
		}
	}
	
	
	 if ($k(window).width() < 640){
		// MULTIPLE ADDRESS TABLE
		if($k("table#multiship-addresses-table").length != 0) {
			if($k("#multiship-shopping-table").length == 0) {
				$k('<div id="multiship-shopping-table"></div>').insertBefore('#multiship-addresses-table');
			}
			$k('table#multiship-addresses-table').addClass("table-responsive");
			$k('table#multiship-addresses-table thead').addClass("table-head");
			$k('table#multiship-addresses-table tfoot').addClass("table-foot");
			$k('table#multiship-addresses-table tr').addClass("row-responsive");
			$k('table#multiship-addresses-table td').addClass("column-responsive clearfix");
			$k("table#multiship-addresses-table").responsiveTable({prefix:'tm_responsive',target:'#multiship-shopping-table'});
		}		
	}else{
		// MULTIPLE ADDRESS TABLE
		if($k("table#multiship-addresses-table").length != 0) {
			$k('table#multiship-addresses-table').removeClass("table-responsive");
			$k('table#multiship-addresses-table thead').removeClass("table-head");
			$k('table#multiship-addresses-table tfoot').removeClass("table-foot");
			$k('table#multiship-addresses-table tr').removeClass("row-responsive");
			$k('table#multiship-addresses-table td').removeClass("column-responsive");
			$k("#multiship-shopping-table").remove();
		}
	}

	 if ($k(window).width() < 640){
		// CHECKOUT TABLE
		if($k("table#checkout-review-table").length != 0) {
			if($k("#review-checkout-table").length == 0) {
				$k('<div id="review-checkout-table"></div>').insertBefore('#checkout-review-table-wrapper');
			}
			$k('table#checkout-review-table').addClass("table-responsive");
			$k('table#checkout-review-table thead').addClass("table-head");
			$k('table#checkout-review-table tfoot').addClass("table-foot");
			$k('table#checkout-review-table tr').addClass("row-responsive");
			$k('table#checkout-review-table td').addClass("column-responsive clearfix");
			$k("table#checkout-review-table").responsiveTable({prefix:'tm_responsive',target:'#review-checkout-table'});
		}		
	}else{
		// CHECKOUT TABLE
		if($k("table#checkout-review-table").length != 0) {
			$k('table#checkout-review-table').removeClass("table-responsive");
			$k('table#checkout-review-table thead').removeClass("table-head");
			$k('table#checkout-review-table tfoot').removeClass("table-foot");
			$k('table#checkout-review-table tr').removeClass("row-responsive");
			$k('table#checkout-review-table td').removeClass("column-responsive");
			$k("#review-checkout-table").remove();
		}
	}
	
	 if ($k(window).width() < 640){
		// OREDER TABLE
		if($k("table#my-orders-table").length != 0) {
			if($k("#order-table").length == 0) {
				$k('<div id="order-table"></div>').insertBefore('#my-orders-table');
			}
			$k('table#my-orders-table').addClass("table-responsive");
			$k('table#my-orders-table thead').addClass("table-head");
			$k('table#my-orders-table tfoot').addClass("table-foot");
			$k('table#my-orders-table tr').addClass("row-responsive");
			$k('table#my-orders-table td').addClass("column-responsive clearfix");
			$k("table#my-orders-table").responsiveTable({prefix:'tm_responsive',target:'#order-table'});
		}		
	}else{
		// OREDER TABLE
		if($k("table#my-orders-table").length != 0) {
			$k('table#my-orders-table').removeClass("table-responsive");
			$k('table#my-orders-table thead').removeClass("table-head");
			$k('table#my-orders-table tfoot').removeClass("table-foot");
			$k('table#my-orders-table tr').removeClass("row-responsive");
			$k('table#my-orders-table td').removeClass("column-responsive");
			$k("#order-table").remove();
		}
	}
	
	 if ($k(window).width() < 640){
		// SUPER PRODUCT TABLE
		if($k("table#super-product-table").length != 0) {
			if($k("#super-table").length == 0) {
				$k('<div id="super-table"></div>').insertBefore('#super-product-table');
			}
			$k('table#super-product-table').addClass("table-responsive");
			$k('table#super-product-table thead').addClass("table-head");
			$k('table#super-product-table tfoot').addClass("table-foot");
			$k('table#super-product-table tr').addClass("row-responsive");
			$k('table#super-product-table td').addClass("column-responsive clearfix");
			$k("table#super-product-table").responsiveTable({prefix:'tm_responsive',target:'#super-table'});
		}		
	}else{
		// SUPER PRODUCT TABLE
		if($k("table#super-product-table").length != 0) {
			$k('table#super-product-table').removeClass("table-responsive");
			$k('table#super-product-table thead').removeClass("table-head");
			$k('table#super-product-table tfoot').removeClass("table-foot");
			$k('table#super-product-table tr').removeClass("row-responsive");
			$k('table#super-product-table td').removeClass("column-responsive");
			$k("#super-table").remove();
		}
	}
	
	 if ($k(window).width() < 640){
		// WISHLIST TABLE
		if($k("table#wishlist-table").length != 0) {
			if($k("#new-wishlist-table").length == 0) {
				$k('<div id="new-wishlist-table"></div>').insertBefore('#wishlist-table');
			}
			$k('table#wishlist-table').addClass("table-responsive");
			$k('table#wishlist-table thead').addClass("table-head");
			$k('table#wishlist-table tfoot').addClass("table-foot");
			$k('table#wishlist-table tr').addClass("row-responsive");
			$k('table#wishlist-table td').addClass("column-responsive clearfix");
			$k("table#wishlist-table").responsiveTable({prefix:'tm_responsive',target:'#new-wishlist-table'});
		}		
	}else{
		// WISHLIST TABLE
		if($k("table#wishlist-table").length != 0) {
			$k('table#wishlist-table').removeClass("table-responsive");
			$k('table#wishlist-table thead').removeClass("table-head");
			$k('table#wishlist-table tfoot').removeClass("table-foot");
			$k('table#wishlist-table tr').removeClass("row-responsive");
			$k('table#wishlist-table td').removeClass("column-responsive");
			$k("#new-wishlist-table").remove();
		}
	}
	
	
	if ($k(window).width() < 640){
		// DOWNLOADABLE TABLE
		if($k("table#my-downloadable-products-table").length != 0) {
			if($k("#downloadable-products-table").length == 0) {
				$k('<div id="downloadable-products-table"></div>').insertBefore('#my-downloadable-products-table');
			}			 
			 
			$k('table#my-downloadable-products-table').addClass("table-responsive");
			$k('table#my-downloadable-products-table thead').addClass("table-head");
			$k('table#my-downloadable-products-table tfoot').addClass("table-foot");
			$k('table#my-downloadable-products-table tr').addClass("row-responsive");
			$k('table#my-downloadable-products-table td').addClass("column-responsive clearfix");
			$k("table#my-downloadable-products-table").responsiveTable({prefix:'tm_responsive',target:'#downloadable-products-table'});
		}		
	}else{
		// DOWNLOADABLE TABLE
		if($k("table#my-downloadable-products-table").length != 0) {
			$k('table#my-downloadable-products-table').removeClass("table-responsive");
			$k('table#my-downloadable-products-table thead').removeClass("table-head");
			$k('table#my-downloadable-products-table tfoot').removeClass("table-foot");
			$k('table#my-downloadable-products-table tr').removeClass("row-responsive");
			$k('table#my-downloadable-products-table td').removeClass("column-responsive");
			$k("#downloadable-products-table").remove();
		}
	}
	
}
$k(document).ready(function(){tableMakeResponsive();});
$k(window).resize(function(){tableMakeResponsive();});	


function mobileTabToggle(){
	//alert($(window).width());
	if ($k(window).width() < 980)
	{	
		
		$k(".padder .mobile_togglemenu").remove();
		$k(".padder h6").append( "<h5 class='mobile_togglemenu'>&nbsp;</h5>" );
		$k(".padder h6").addClass('toggle');
		$k(".padder .mobile_togglemenu").click(function(){
			$k(this).parent().toggleClass('active').parent().find('ol').toggle('fast');
		});

	}else{
		$k(".padder h6").parent().find('ol').removeAttr('style');
		$k(".padder h6").removeClass('active');
		$k(".padder h6").removeClass('toggle');
		$k(".padder .mobile_togglemenu").remove();
	}	
}
$k(document).ready(function(){mobileTabToggle();});
$k(window).resize(function(){mobileTabToggle();});







function flexwidth(){
		var fheight=$k(window).height();  
		var fwidth=$k(window).width();
		var fwidth1=fwidth/2;
		var flxslide=$k('.cms-home .sub-flex');
		var slider1=$k('.cms-home .sub-flex1');
		var slider2=$k('.cms-home .sub-flex2');
		if ($k(window).width() > 979){
				flxslide.css("width",fwidth);
				flxslide.css("height",fheight);
				slider1.css("width",fwidth1);
				slider1.css("height",fheight);
				slider2.css("width",fwidth1);
				slider2.css("height",fheight);
		}else{
				fheight="auto";
				flxslide.css("width",fwidth);
				flxslide.css("height",fheight);
				slider1.css("width",fwidth1);
				slider1.css("height",fheight);
				slider2.css("width",fwidth1);
				slider2.css("height",fheight);
			}

}
jQuery(document).ready(function() { flexwidth();});
jQuery(window).resize(function() {flexwidth();});

function mobilenav(){
					var header = $k('.header-container');    
					if ($k(window).width() > 767){	 
							if ($k(this).scrollTop() >0) {
								header.addClass("header-fix");
							} else {
								header.removeClass("header-fix");
							}	
					}else{		
							header.removeClass("fix-nav");
					}

}
jQuery(document).ready(function() { mobilenav();});
jQuery(window).resize(function() {mobilenav();});
jQuery(window).scroll(function() {mobilenav();});
  
  
function flex(){
	
				$k('.cms-home .flexslider .slides li .sub-flex-common').each(function() {
				var that = $k(this);
				var url = that.find('img').attr('src');
						if ($k(window).width() > 979){	 
						that.css({'background-image':'url("' + url + '")'});    
						}else{
						that.css({'background-image':'none'}); 	
						}
				});
	
	
}
jQuery(document).ready(function() { flex();});
jQuery(window).resize(function() {flex();});




$k(window).load(function(){	 
	var isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent);
	if(!isMobile) {
		if($k(".parallex,.parallex_sec").length){  $k(".parallex,.parallex_sec").sitManParallex({  invert: false });};    
	}else{
		$k(".parallex,.parallex_sec").sitManParallex({  invert: true });
		
		}	
});