jQuery(document).ready( function($) {

	$('.searchFilter').keyup(function(){
		var valThis = $(this).val().toLowerCase();
		$('.sk-faq-list .faq-item').each(function() {
	  	var text = $(this).find('h5 span').text().toLowerCase();
	  	(text.indexOf(valThis) >= 0) ? $(this).fadeIn() : $(this).fadeOut();
		});

	});
	
	$('.faq-answer').hide();
	$('.faq-question').on( 'click', function() {

		$(this).closest('.box-qa-answers').find('.faq-answer').toggle();
		
		if( $(this).find('.arrow use').attr('xlink:href') == '#arrow-up' ){
			$(this).find('.arrow use').attr('xlink:href', '#arrow-down');	
		}else{
			$(this).find('.arrow use').attr('xlink:href', '#arrow-up');
		}

		$(this).closest('.faq-item').siblings().find('.faq-answer').hide();
		$(this).closest('.faq-item').siblings().find('.arrow use').attr('xlink:href', '#arrow-down');

	 });
	
}); 