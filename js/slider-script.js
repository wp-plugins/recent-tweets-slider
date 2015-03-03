jQuery(window).load(function() {
	
	jQuery(".tw_slider_wrap ul").filter(function( index ) {
		return jQuery( "li", this ).length === 1;
    }).closest(".tw_slider_wrap").find(".tw_slider_prev, .tw_slider_next").remove();
	
	jQuery('#tw_slider li:first-child').addClass('tw_slider_moving');
	jQuery('#tw_slider li').addClass('tw_animated');
	
	jQuery(".tw_slider_next").click(function () { // when click on next button
		var $nextButton = jQuery(this);
		var $sliderContainer = $nextButton.closest('.tw_slider_wrap');
		var $first = $sliderContainer.find('li:first-child');
		var $next;
		
	  	$tw_slider_moving = $sliderContainer.find('.tw_slider_moving');
	  	$next = $tw_slider_moving.next('li').length ? $tw_slider_moving.next('li') : $first;

	  	$tw_slider_moving.removeClass("animate_left animate_right");
	  	$tw_slider_moving.removeClass("tw_slider_moving").fadeOut(500);

	  	$next.addClass('animate_right');
	  	$next.addClass('tw_slider_moving').fadeIn(500);
	});

	jQuery(".tw_slider_prev").click(function () { // when click on prev button
		var $prevButton = jQuery(this);
		var $sliderContainer = $prevButton.closest('.tw_slider_wrap');
		var $last = $sliderContainer.find('li:last-child');
		var $prev;
		
    	$tw_slider_moving =  $sliderContainer.find(".tw_slider_moving");
    	$prev = $tw_slider_moving.prev('li').length ? $tw_slider_moving.prev('li') : $last;
		
    	$tw_slider_moving.removeClass("animate_right animate_left");
		$tw_slider_moving.removeClass("tw_slider_moving").fadeOut(500);
		
		$prev.addClass('animate_left');
    	$prev.addClass('tw_slider_moving').fadeIn(500);
	});
	
	jQuery('.tw_auto_slider .tw_slider_next, .tw_auto_slider .tw_slider_prev').remove();

});