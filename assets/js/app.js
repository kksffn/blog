(function($) {

	/**
	 * INSERT FORM - NEW TAG-----------------------------------------------------------------------------------------
	 */
	var add_form  = $('#add-new-tag'),
		list  = $('#tag-list'),
	    add_input = add_form.find('#new-tag'),
		span ='';
	add_input.val('').focus();
	$('.btn-add').hide();

	/**
	 * SETTINGS of animation for adding new tag
	 */
	var animation = {
		startColor: '#00bc8c',
		endColor: list.find('span').css('backgroundColor') || '#303030',
		delay: 200
	}

	/** On submit add new tag with delete-link and number of posts info to the list */
	add_form.on('submit', function(event) {
		event.preventDefault();

		var request = $.ajax({
			url: add_form.attr('action'),
			type: 'POST',
			data: add_form.serialize(),
			dataType: 'json',
		});
		request.done(function(data) {
			if ( data.status === 'success' ) {
				span = $('<span class="pull-left col-md-3"> ' +
						'<form class="pull-left" action="edit-tag/' + data.id + '" id="edit-tag-form-'+data.id+
							'" method="post">' +
							'<input type="text" class="btn btn-warning btn-xs tag-btn pull-left input-tag"' +
								' value="' + data.tag + '" name="edit-tag-'+ data.id +'" id="edit-tag-' + data.id +'" ' +
								'onclick="this.select()" onchange="add_tag_id_input(this)">' +
							'<input type="hidden" id="tagname-' + data.id +'" name="tagname-\' + data.id +"' +
								' value="' + data.id + '">' +
						'</form>' +
						'<form class="pull-left" action="' + baseURL +'/delete-tag/' + data.id + '"' +
							' id="edit-tag-form-'+ data.id + '" method="post">' +

							'<div class="tag-controls"> (0 posts)' +
							'<button type="submit" id="delete-tag-'+ data.id +'" class="odd btn btn-xs tag-delete-btn">' +
								'<small> &times delete tag</small>' +
							'</button>' +
							'</div>'+
						'</form>' +
					'</span>'
				);
			/* animate it so the user can see it and focus on input field */
				span.appendTo( list )
					.css({ backgroundColor: animation.startColor })
					.delay( animation.delay )
					.animate({ backgroundColor: animation.endColor });

				// $.ajax({ url: baseURL + '/tags' }).done(function(html) {
				// 	var newItem = $(html).find('#tag-item-' + data.id);
				//
				// 	newItem.appendTo( list )
				// 		.css({ backgroundColor: animation.startColor })
				// 		.delay( animation.delay )
				// 		.animate({ backgroundColor: animation.endColor });
				// });
					add_input.val('').focus();
			} else {
				alert(data.message)
			}
		})
	});

	add_input.on('keypress', function(event) {
		if ( event.which === 13 ) {
			add_form.submit();
			return false;
		}
	});
/*----------------------------------------------------------------------------------------------------------------*/

	/**
	 * EDIT TAG FORM----------------------------------------------------------------------------------------
	 */

	// in assets/js/edit.tag.js - has to be loaded later (when id of updated tag is known)


/*----------------------------------------------------------------------------------------------------------------*/

	/**
	 * DELETE link for TAG - confirmation needed ------------------------------------------------------------
	 */
	list.find('button').on('click', function() {
		return confirm('Are you sure you want to vymazat this tag?');
	});
/*----------------------------------------------------------------------------------------------------------------*/
	/**
	 * COMMENTS-----------------------------------------------------------------------------------------------
	 */
	var add_comment_form = $('#add-comment'),
		comment_text = $('#comment_text'),
		container =$('#new-comment-container');


	comment_text.on('keypress', function(event) {
		if ( (event.which === 13 || event.which === 10 ) && event.ctrlKey ) {
			add_comment_form.submit();
			return false;
		}
	});

	add_comment_form.on('submit', function(event) {
		event.preventDefault();

		var request = $.ajax({
			url: add_comment_form.attr('action'),
			type: 'POST',
			data: add_comment_form.serialize(),
			dataType: 'json',
		});
		request.done(function (data) {
			if (data.status === 'success') {

				var li = document.createElement("li"),
					commentnumber =  document.createElement("div"),
					cite = document.createElement("cite"),
					b_cite = document.createElement("b"),
					commentcontent = document.createElement("div"),
					commentmetadata = document.createElement("small"),
					time = document.createElement("time"),
					b_time = document.createElement("b");

				$(li).hide();
				li.id="comment-"+data['comment_id'];
				$(commentnumber)
					.addClass("commentnumber")
					.html(data["i"])
					.appendTo(li);
				li.append(cite);
				cite.append(b_cite);
				$(b_cite).html(data['nickname'] + " wrote:");
				li.append(document.createElement("br"));

				$(commentcontent)
					.addClass("commentcontent")
					.html(data["comment_text"])
					.appendTo(li);
				$(commentmetadata)
					.addClass("commentmetadata")
					.html(b_time)
					.appendTo(li);
				b_time.append(time);
				$(time).html(data["time"])

				container.append(li);
				$(li).show();

				comment_text.val('');

			}	else {
				alert(data['message']);
			}

		});
	});

////////////////////////////////////Hide alerts on click//////////////////////////////////
	$('.alert')
		.prepend('<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>');

	$('.close')
		.on('click', function(){
		$(this).parent()
			.hide();
	});

	$('.alert').delay(4000).fadeOut();

//////////////////////////////Asks before deleting all user's posts//////////////////////////



}(jQuery));

/**
 * Add input field with id of edited user (on admin's page) - to create update query only for edited users
 * @param val ("user_row-$user_id")
 */
function add_user_id_input(val) {
	var user_id = get_id_from_element(val);
	var tr = $('.user_row-'+user_id); //val??
	tr.append( '<input type="hidden" name="user[]" value="'+ user_id +'">');
}

/**
 * Add input and script fields to edited tag (for admin only) - to know which tag was edited
 * @param val ("edit-tag-$tag->id")
 */
function add_tag_id_input(val) {
	var tag_id = get_id_from_element(val);
	//var tag_input = $('#edit-tag-'+tag_id);
	var container = document.getElementById("edit-container-"+tag_id);
	while (container.hasChildNodes()) {
		container.removeChild(container.lastChild);
	}
	var input = document.createElement("input");
	input.type = "hidden";
	input.name = "tag[]";
	input.id = "this-was-changed";
	input.value = tag_id;
	container.appendChild(input);
	var script = document.createElement("script");
	script.src ="assets/js/edit_tag.js";
	container.appendChild(script);

	//tag_input.append('<input type="" name="" id="" value="'+tag_id+'">');
	// Now we know the id of changed tag (and id of the submitted form) so we can use script to it
	//tag_input.append('<script src=""></script>');
}

function get_id_from_element(val) {
	var sub_val = val.id.split('-');
	return sub_val[sub_val.length - 1];
}

function update_tag(tag_id) {

	// var edit_form = document.getElementById('edit-tag-form-' + tag_id),
	// 	edit_input = document.getElementById('edit-tag-' + tag_id);
	//
	// edit_form.addEventListener("submit", function (event) {
	// 	event.preventDefault();
	// });
//....................
}

function askBeforeDelete(text) {
	return confirm('Are you sure you want to vymazat ' + text + '?');
}