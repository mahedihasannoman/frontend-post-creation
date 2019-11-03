/*
* @Author: Mahedi Hasan
* @Since: 1.0.0
* @Description: frontend control script
*/
jQuery(document).ready(function($){
	'use strict'
	
	var searchRequest;
	$('.fpc-tags').autocomplete({
		minChars: 2,
		source: function(term, suggest){
			try { searchRequest.abort(); } catch(e){}
			searchRequest = $.post(fpc_object.ajax_url, { search: term, action: 'fpc_search_tags' }, function(res) {
				suggest(res.data);
			});
		}
	});
	//Autocomplete event handling for Tag field
	$(document).on( "autocompleteselect",'.fpc-tags', function( event, ui ) {
		event.preventDefault();
		var obj = $(this);
		
		addtags(ui.item.value);
		
	});
	//keypress event handling for Tag field
	$(document).on('keypress','.fpc-tags',function (e) {
		if (e.which == 13||e.keyCode==13) {
			e.preventDefault();
			if($(this).val()!=''){
				addtags($(this).val());
			}
		}
	});
	//Add tag button click event handling
	$(document).on('click','.fpc_tagadd',function (e) {
		e.preventDefault();
		if($('.fpc-tags').val()!=''){
			addtags($('.fpc-tags').val());
		}
	});
	
	//Remove tag button click event handling
	$(document).on('click','.fpc_tag_remove',function (e) {
		e.preventDefault();
		removetags($(this).parent().text());
	});
	
		// Remove tag function
	var addtags = function(value){
		var ctags = [];
		if($('#fpc-tags').val()!=''){
			ctags = $('#fpc-tags').val().split(",")
		}
		$('.fpc-tags').val('');
		console.log($.inArray(value, ctags));
		if ($.inArray(value, ctags) >= 0) {
			return;
		}else{
			
			ctags.push(value);
			$( ".fpc-tag_area ul" ).append( '<li><button class="fpc_tag_remove"><i class="fa fa-window-close" aria-hidden="true"></i></button>'+value+'</li>' );
			$('#fpc-tags').val(ctags.join(','));
		}
	}
	// Remove tag function
	var removetags = function(value){
		
		var ctags = [];
		if($('#fpc-tags').val()!=''){
			ctags = $('#fpc-tags').val().split(",")
		}
		$('.fpc-tags').val('');
		console.log($.inArray(value, ctags));
		if ($.inArray(value, ctags) >= 0) {
			
			ctags.splice($.inArray(value, ctags),1);
			$( ".fpc-tag_area ul" ).empty();
			$.each(ctags, function( index, tagvalue ) {
				$( ".fpc-tag_area ul" ).append( '<li><button class="fpc_tag_remove"><i class="fa fa-window-close" aria-hidden="true"></i></button>'+tagvalue+'</li>' );
			});
			$('#fpc-tags').val(ctags.join(','));
			
		}else{
			
			return;
		}
	}
	
	
})