jQuery(document).ready(function($){

	// reassign values on repeater rows
	function resetRows() {
		$('.row.row-repeater ul').each(function(){
			var order = 0;
			$(this).children('li.visible').each(function(i){
				$(this).attr('data-order', i).find('input[type=text], input[type=number], textarea').attr('name', 'alt_repeater_' + i);
			});
		});
	}

	// sortable repeater rows
	$('.row.row-repeater ul').sortable({
		cursor: 'move',
		handle: '.tab.drag',
		update: resetRows(),
	});

	// delete repeater rows
	$('.row.row-repeater .tab.delete').click(function(){
		$(this).parent('li').animate({'opacity':0}, 'fast').delay(100).slideUp('fast').find('input[type=text], input[type=number], textarea').attr('value', '').val('');
	});

	// add repeater row
	$('.add-new-repeater-row').click(function(){
		var cloned = $(this).siblings('ul').children('li.sentinal').clone(true);
		$(this).siblings('ul').append( cloned.removeClass('sentinal').addClass('visible') );
		resetRows();
		return false;
	});

});