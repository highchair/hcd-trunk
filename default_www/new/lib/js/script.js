/*
 * Scripts for this specific project
 */


/* For mobile, hide the iOS toolbar on initial page load */
/mobile/i.test(navigator.userAgent) && !window.location.hash && setTimeout(function () { window.scrollTo(0, 0); }, 1000); 


/*
 * "Watch" the body:after { content } to find out how wide the viewport is.
 * Thanks to http://adactio.com/journal/5429/ for details about this method
 */
if ( is_ltie9 ) { 
    var mq_tag = ''; 
} else {
    var mq_tag = window.getComputedStyle(document.body,':after').getPropertyValue('content'); 
}
console.log( "mq_tag=" + mq_tag );


/*
 * Set Modernizr variables that any script can check against. 
 */
var hastouch = has_feature( "touch" ),
    hasplaceholder = has_feature( "placeholder" ),
    hasboxsizing = has_feature( "boxsizing" ),
    is_ltie9 = has_feature( "lt-ie9" );

//console.log( "hastouch=" + hastouch ); 
//console.log( "hasplaceholder=" + hasplaceholder ); 
//console.log( "hasboxsizing=" + hasboxsizing ); 
//console.log( "is_ltie9=" + is_ltie9 );

if( !window.Retina ) { 
    var is_retina = false; 
    $("html").addClass( "no-retina" ); 
} else {
    var is_retina = Retina.isRetina(); 
    if ( is_retina ) {
        $("html").addClass( "retina" ); 
    } else {
        $("html").addClass( "no-retina" ); 
    }
}


/* Variables to set to true later and check */
var loadpopups_loaded = false;


/* 
 * Load progressive content. Must remember to also load them for IE 7 and 8 if needed 
 * It is safe to put ALL resize or onLoad events in here
 */
function on_resize_orientationchange() {
    
    		
	// Check again on resize/orientation change
    if ( is_ltie9 ) { 
        var mq_tag = ''; 
    } else {
        var mq_tag = window.getComputedStyle(document.body,':after').getPropertyValue('content'); 
    }
    console.log( "mq_tag=" + mq_tag );
    
    
    // Initiate Magnific Popup 
    if ( mq_tag.indexOf("loadpopups") !=-1 && ! loadpopups_loaded ) {
        
        $('.js-gallery').each(function() {
            $(this).magnificPopup({
                disableOn: 600, // disable this plugin when window width is less that number
                delegate: '.js-popup',
                removalDelay: 500, 
                mainClass: 'mfp-fade', 
                type: 'image',
                gallery: {
                    enabled: true, // set to true to enable gallery
                    preload: [1,3], 
                }
            });
        });
        loadpopups_loaded = true; 
    
    } else {
        
        $('.js-gallery .js-popup').click(function(event) {
            event.preventDefault();
        }); 
    }
    
    
    // Initiate Flexslider for inserted Carousels
    $('.slideshow__inserted .js-slideshow').each( function(){
        
        $(this).flexslider({
            // All options here: http://www.woothemes.com/flexslider/
            initDelay: 250, // delay in milliseconds on first animation
            animation: 'slide', // fade or slide
            direction: 'horizontal',
            animationLoop: true,
            smoothHeight: false,
            slideshow: true, // if true, animate automatically on load
            slideshowSpeed: 5000, // in ms
            animationSpeed: 500, // in ms
            randomize: false,
            
            // Usability features
            pauseOnAction: true, // Pause when interacting with control elements
            pauseOnHover: true, // Pause when hovering over slider, then resume when no longer hovering
            useCSS: true, // Slider will use CSS3 transitions if available
            touch: hastouch, // Allow touch swipe navigation on touch-enabled devices. touch should be detected via modernizr
            video: false, // If using video in the slider, will prevent CSS3 3D Transforms to avoid graphical glitches

            // Primary Controls
            controlNav: false, // Create navigation for paging controls
            directionNav: true, // Create navigation for previous/next controls
            prevText: 'Previous', // String: Set the text for the "previous" directionNav item
            nextText: 'Next', // String: Set the text for the "next" directionNav item 
            
            start: function(slider){
                $(this).fadeIn(); 
                $('body').removeClass('loading');
            }
        });
    });
};


/* Events and Listeners that do not need to listen to the @media-query label */
$().ready(function() {
    
    
    /* Image thumbnails under main project image are an image switcher */
	$(".js-thumb-trigger").click(function(e) {
	    
		var listid = $(this).attr('data-id');
		
		// Loop through all the images and change the classes
		$('.js-gallery li').each(function() {
			if ( $(this).hasClass('images--item__active') ) {
				$(this).removeClass('images--item__active').addClass('images--item__disabled');
			}
		});
		// Add the show class to the one that matches the clicked thumbnail
		$('li#' + listid).removeClass('images--item__disabled').addClass('images--item__active');
		
		// Loop through the thumbnails to remove the selected style
		$(".js-thumbs li").each(function() {
			$(this).removeClass('images--thumb__disabled');
		});
		// Add the class in for the selected one
		$(this).parent().addClass('images--thumb__disabled');
		
		e.preventDefault();
	}); 
	
	/*<div class="project slide--container">  
        <ul class="images--list menu js-gallery">
            <li class="images--previous-button">
                <a href="#"><span class="icon icon-angle-left" aria-hidden="true">«</span></a>
            </li>
            <li class="images--item images--item__active" id="1">
                <a class="images--link js-popup" href="/www/documents/gallery_photos/1251.DSC_0010.jpg">
                    <figure class="slide--photo">
                        <img src="/www/documents/gallery_photos/1251.DSC_0010.jpg" alt="">
                    </figure>
                </a>
            </li>
            <li class="images--item images--item__disabled" id="2">
                <a class="images--link js-popup" href="/www/documents/gallery_photos/1254.DSC_0028.jpg">
                    <figure class="slide--photo">
                        <img src="/www/documents/gallery_photos/1254.DSC_0028.jpg" alt="">
                    </figure>
                </a>
            </li>
            <li class="images--next-button">
                <a href="#"><span class="icon icon-angle-right" aria-hidden="true">»</span></a>
            </li>
        </ul>
        <ul class="images--thumbs menu js-thumbs">
            <li class="images--thumb images--thumb__disabled">
                <a href="/www/documents/gallery_photos/1251.DSC_0010.jpg" class="js-thumb-trigger" data-id="1">
                    <img src="/www/documents/gallery_photos/1251.DSC_0010.jpg" alt="">
                </a>
            </li>
            <li class="images--thumb">
                <a href="/www/documents/gallery_photos/1254.DSC_0028.jpg" class="js-thumb-trigger" data-id="2">
                    <img src="/www/documents/gallery_photos/1254.DSC_0028.jpg" alt="">
                </a>
            </li>
        </ul>
    </div>*/
	
	
	/* Add controls for the previous and next buttons */
	$(".images--next-button a").click(function(n) {
	   
        // Loop through all the images and change the classes
        $('.js-gallery li').each(function() {
        	if ( $(this).hasClass('images--item__active') ) {
        		if ( $(this).next().length != 0 ) {
            		$(this).removeClass('images--item__active').addClass('images--item__disabled');
            		$(this).next("li").addClass('images--item__active').removeClass('images--item__disabled');
                } else {
                    $(".images--next-button").addClass('images--next-button__disabled');
                }
                return false; 
        	}
        });
        $(".js-thumbs li").each(function() {
    		if ( $(this).hasClass('images--thumb__disabled') ) {
    			if ( $(this).next().length != 0 ) {
        			$(this).removeClass('images--thumb__disabled');
        			$(this).next( "li" ).addClass('images--thumb__disabled');
    			} 
    			return false;  
			}
		});
		if ( $(".images--previous-button").hasClass('images--previous-button__disabled') ) {
    		$(".images--previous-button").removeClass('images--previous-button__disabled');
		}
		n.preventDefault();
	});
	
	$(".images--previous-button a").click(function(p) {
	   
        // Loop through all the images and change the classes
        $('.js-gallery li').each(function() {
        	if ( $(this).hasClass('images--item__active') ) {
        		if ( $(this).prev().length != 0 ) {
            		$(this).removeClass('images--item__active').addClass('images--item__disabled');
            		$(this).prev("li").addClass('images--item__active').removeClass('images--item__disabled');
                } else {
                    $(".images--previous-button").addClass('images--previous-button__disabled');
                }
                return false; 
        	}
        });
        $(".js-thumbs li").each(function() {
    		if ( $(this).hasClass('images--thumb__disabled') ) {
    			if ( $(this).prev().length != 0 ) {
        			$(this).removeClass('images--thumb__disabled');
        			$(this).prev( "li" ).addClass('images--thumb__disabled');
    			} 
    			return false;  
			}
		});
		if ( $(".images--next-button").hasClass('images--next-button__disabled') ) {
    		$(".images--next-button").removeClass('images--next-button__disabled');
		}
		p.preventDefault();
	}); 
	
    
    /* 
     * Animate some scrolling for smoother transitions 
     * http://css-tricks.com/snippets/jquery/smooth-scrolling/
     * Markup pattern: <div class="anchor-jump"><a class="bottom-jump" href="#top"><span class="icon icon-circle-arrow-up" aria-hidden="true"><span>&uarr;</span></span><span class="help-text">Back to Top</span></a></div>
     */
    $(function() {
        $('.js-smoovmove').click(function() {
            if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
                var target = $(this.hash);
                target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
                if (target.length) {
                    $('html,body').animate({
                        scrollTop: target.offset().top
                    }, 500);
                    return false;
                }
            }
        });
        $('.typography a').click(function() {
            if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
                var target = $(this.hash);
                target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
                if (target.length) {
                    $('html,body').animate({
                        scrollTop: target.offset().top
                    }, 500);
                    return false;
                }
            }
        });
    });
    
    
    /* 
     * Make it more apparent that a selection has been made when checkboxes or radios are selected
     * Adds a class to the selected radio/checkbox parent label 
     * Requires inputs to be nested in labels: 
     * <label for="checkbox2"><input id="checkbox2" name="checkbox" type="checkbox">Choice B</label>
     * <label for="radio1"><input id="radio1" name="radio" type="radio" checked="checked">Option 1</label>
     */
    $('input:radio').click(function() {
        $('label:has(input:radio:checked)').addClass('active');
        $('label:has(input:radio:not(:checked))').removeClass('active');
    });
    $('input:checkbox').click(function() {
        $('label:has(input:checkbox:checked)').addClass('active');
        $('label:has(input:checkbox:not(:checked))').removeClass('active');
    });
    /* Loop through them on initial page load as well */
    $('input:radio').each(function() {
        $('label:has(input:radio:checked)').addClass('active');
    });
    $('input:checkbox').each(function() {
        $('label:has(input:checkbox:checked)').addClass('active');
    });
    
    
    /* If there is no box-sizing present (IE 7 & 8 mostly) run the javascript polyfill */
    if ( hasboxsizing == false ) {
        /*
         * @author Alberto Gasparin http://albertogasparin.it/
         * @version 1.1, License MIT
         */
var borderBoxModel=(function(elements,value){var element,cs,s,width,height;for(var i=0,max=elements.length;i<max;i++){element=elements[i];s=element.style;cs=element.currentStyle;if(s.boxSizing==value||s["box-sizing"]==value||cs.boxSizing==value||cs["box-sizing"]==value){try{apply();}catch(e){}}}
function apply(){width=parseInt(cs.width,10)||parseInt(s.width,10);height=parseInt(cs.height,10)||parseInt(s.height,10);if(width){var
borderLeft=parseInt(cs.borderLeftWidth||s.borderLeftWidth,10)||0,borderRight=parseInt(cs.borderRightWidth||s.borderRightWidth,10)||0,paddingLeft=parseInt(cs.paddingLeft||s.paddingLeft,10),paddingRight=parseInt(cs.paddingRight||s.paddingRight,10),horizSum=borderLeft+paddingLeft+paddingRight+borderRight;if(horizSum){s.width=width-horizSum;}}
if(height){var
borderTop=parseInt(cs.borderTopWidth||s.borderTopWidth,10)||0,borderBottom=parseInt(cs.borderBottomWidth||s.borderBottomWidth,10)||0,paddingTop=parseInt(cs.paddingTop||s.paddingTop,10),paddingBottom=parseInt(cs.paddingBottom||s.paddingBottom,10),vertSum=borderTop+paddingTop+paddingBottom+borderBottom;if(vertSum){s.height=height-vertSum;}}}})(document.getElementsByTagName('*'),'border-box');
    }
    
    /* If there is no HTML5 placeholder present, run a javascript equivalent */
    if ( hasplaceholder == false ) {
        
        /* polyfill from hagenburger: https://gist.github.com/379601 */
        $('[placeholder]').focus(function() {
            var input = $(this);
            if (input.val() == input.attr('placeholder')) {
                input.val('');
                input.removeClass('placeholder');
            }
        }).blur(function() {
            var input = $(this);
            if (input.val() == '' || input.val() == input.attr('placeholder')) {
                input.addClass('placeholder');
                input.val(input.attr('placeholder'));
            }
        }).blur().parents('form').submit(function() {
            $(this).find('[placeholder]').each(function() {
                var input = $(this);
                if (input.val() == input.attr('placeholder')) {
                    input.val('');
                }
            })
        });
    }
    

    /* Non-media query enabled browsers need any critical content on page load. */
    if ( is_ltie9 ) {
                
        /* Loop through images inside .text and add classes to them */
        $('.text img').each(function() {
            if ( $(this).attr("style", "float: left;") ) {
                $(this).addClass("floatleft"); 
            }

            if ( $(this).attr("style", "float: right;") ) {
                $(this).addClass("floatright"); 
            }
        });
        
        /* Add rel="external" to links that are external (this.hostname !== location.hostname) 
         * BUT don't add to anchors containing images. 
         * Good because old IE wont support a[href^='http://'] but maybe best to use this everywhere instead? 
         */
        $('.text a').each(function() {
            // Compare the anchor tag's host name with location's host name
            return this.hostname && this.hostname !== location.hostname;
        }).not('a:has(img)').attr("rel","external");
    }
    
}); 


/* Leave this at the bottom of this file */

/*
 * Load, Resize and Orientation change methods
 * http://css-tricks.com/forums/discussion/16123/reload-jquery-functions-on-ipad-orientation-change/p1 */
//initial load
$(window).load( function() { on_resize_orientationchange(); });
//bind to resize
var resizeTimer;
$(window).resize(function () {
    if (resizeTimer) { clearTimeout(resizeTimer); }
    // set new timer
    resizeTimer = setTimeout(function() {
        resizeTimer = null;
        // put your resize logic here and it will only be called when there's been a pause in resize events
        on_resize_orientationchange();
    }, 350);
});
//check for the orientation event and bind accordingly
if (window.DeviceOrientationEvent) { window.addEventListener('orientationchange', on_resize_orientationchange, false); } 