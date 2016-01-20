$(function() {
	// toolbar expander
	$('div#dev_toolbar div.dv-button').click(function() {
		var $this = $(this);
		var $content = $this.siblings('div.dv-content');

		$content.stop().animate({width: 'toggle'}, 350);
	});

	// section clicking
	$('div#dev_toolbar div.dv-content div.dv-section.active div.dv-section-header').click(function() {
		var $this = $(this);
		var $section = $this.closest('div.dv-section');
		var $content = $section.find('div.dv-section-content');

		if(!$section.hasClass('expanded')) {
			$('div#dev_toolbar div.dv-section.expanded div.dv-section-content').stop().animate({height: 'hide'}, 350);
			$('div#dev_toolbar div.dv-section.expanded').removeClass('expanded');
		}


		$section.toggleClass('expanded');
		$content.stop().animate({height: 'toggle'}, 350);
	});
	
	// close icon
	$('div#dev_toolbar div.dv-content div.close_toolbar a').click(function() {
		$('div#dev_toolbar').remove();
	});

	// routes additional informations
	$('div.dv-section.routing div.single_route > span').click(function() {
		var $this = $(this);
		var $route = $this.parent();
		
		if(!$route.hasClass('expanded')) {
			$('div.dv-section.routing div.single_route.expanded').removeClass('expanded');
		}

		$route.toggleClass('expanded');
	});
});