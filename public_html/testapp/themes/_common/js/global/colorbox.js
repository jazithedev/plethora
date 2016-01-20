



var cboxOptions = {
	width: '95%',
	maxWidth: '1200px',
	maxHeight: '1200px'
};


$(function() {
	$('a.colorbox').colorbox(cboxOptions);
});

// ON RESIZE
$(window).resize(function() {
	$.colorbox.resize({
		width: window.innerWidth > parseInt(cboxOptions.maxWidth) ? cboxOptions.maxWidth : cboxOptions.width,
		height: window.innerHeight > parseInt(cboxOptions.maxHeight) ? cboxOptions.maxHeight : cboxOptions.height
	});
});