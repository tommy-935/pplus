"use strict";
jQuery(function($){
	$(".los-header .menu").on("mouseover", ".menu-item", function(){
		var $this = $(this);
		$this.addClass("active");
		var $target = $this.children(".sub-menu");
		if($target.length == 0){
			return ;
		}
		$target.addClass("active");
	});

	$(".los-header .menu").on("mouseout", ".menu-item", function(){
		var $this = $(this);
		$this.removeClass("active");
		var $target = $this.children(".sub-menu");
		if($target.length == 0){
			return ;
		}
		$target.removeClass("active");
	});

	$(".los-header").on("mouseover", "#site-header-cart", function(){
		var $obj = $("#min-cart-content");
		$obj.addClass("active");
	});

	$(".los-header").on("mouseout", "#site-header-cart", function(){
		var $obj = $("#min-cart-content");
		$obj.removeClass("active");
	});

	/* woocommerce product start */
	$("#wpi-gallery-box").slick({
		// dots: true,
		// vertical: true
	});
	var device_width = $(window).width(),
		is_vertical = true,
		verticalSwiping = true,
		slidesToShow = 4;
	if(device_width <= 768){
		is_vertical = false;
		verticalSwiping = false;
		slidesToShow = 4;
	}

	$("#wpi-image-thumb").slick({
    	dots: false,
    	vertical: is_vertical,
    	verticalSwiping: verticalSwiping,
    	slidesToShow: slidesToShow,
    	infinite: false,
    });

    $("#wpi-image-thumb").on("click", ".slick-slide", function(){
    	var $this = $(this);
    	$(".slick-slide").removeClass("active");
    	$this.addClass("active");
    	var index = $this.data("slick-index");
    	$("#wpi-gallery-box").slick("slickGoTo", index);

    	var $current_target = $("#wpi-gallery-box").find(".slick-current");
        var img_src = $current_target.find("img").attr('src');
        console.log(img_src);
         $("#wpi-max img").attr("src", img_src);
    });
    var $target_obj = $("#wpi-image-thumb .wpi-image-item");
    if($target_obj.length > 0){
    	$($target_obj[0]).click();
    }

	var onMoveAction = function(){
		var $shade = $("#wpi-fd"),
			$max = $("#wpi-max"),
			$container = $("#wpi-image-gallery");
		var shade_width = $shade.width(),
			shade_height = $shade.height(),
			max_width = $max.width(),
			max_height = $max.height(),
			$img = $max.find("img"),
			img_width = $img.width(),
			img_height = $img.height(),
			container_width = $container.width(),
			container_height = $container.height();
		var rate_x = max_width / shade_width,
			rate_y = max_height / shade_height;

		$container.hover(function() {
        	$shade.addClass("active");
        	$max.addClass("active");
      	}, function() {
        	$shade.removeClass("active");
        	$max.removeClass("active");
      	}).mousemove(function(e) {
        	var x = e.pageX,
          		y = e.pageY;

        	$shade.offset({
          		top: y - shade_height / 2,
          		left: x - shade_width / 2
        	});

        	var cur = $shade.position(),
          		_top = cur.top,
          		_left = cur.left,
          		hdiffer = container_height - shade_height,
          		wdiffer = container_width - shade_width;

        	if (_top < 0){
        		_top = 0;
        	} else if (_top > hdiffer){
        		_top = hdiffer;
        	}
        	if (_left < 0){
        		_left = 0;
        	} else if (_left > wdiffer){
        		_left = wdiffer;
        	}

        	$shade.css({
			  	top: _top,
			  	left: _left
			});
			// console.log(_top + "===" + rate_y);
			console.log(_left + "===" + rate_x);
        	$img.css({
          		top: - (rate_y * _top),
          		left: - (rate_x * _left)
        	});
      });
	}
	if(device_width > 768){
		onMoveAction();
	}

	/* woocommerce product end */

	$("#btn-mobile-menu").on("click", function(){
		var $target = $("#mobile-menu-dropdown");
		$target.toggleClass("active");
	});
});
