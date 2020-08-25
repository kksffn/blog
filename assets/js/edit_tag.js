(function($) {

    /**
     * EDIT TAG FORM -------------------------------------------------------------------------------------------------
     * Changes the tag after submitting it
     */

    var
        tag_id = $('#this-was-changed').val(), //id of changed tag
        edit_form = $('#edit-tag-form-' + tag_id),
        list  = $('#tag-list'),
        edit_input = $('#edit-tag-' + tag_id),
        oldval = $('#tagname-' + tag_id).val(); //the original name of tag - used if not changed

    /**
     * SETTINGS (animation of the change)
     */

    var animation = {
        startColor: '#00bc8c',
        endColor: list.find('input').css('backgroundColor') || '#303030',
        delay: 200
    }

/*-------------- On submit change the tag in DB and animate the change-------------------------------------*/
    edit_form.on('submit', function (event) {
        event.preventDefault();
        var request = $.ajax({
            url: edit_form.attr('action'),
            type: 'POST',
            data: edit_form.serialize(),
            dataType: 'json',
        });

        request.done(function(data) {
            if ( data.status === 'success' ) {
                edit_input
                    .css({ backgroundColor: animation.startColor })
                    .delay( animation.delay )
                    .animate({ backgroundColor: animation.endColor });
                $('#tagname-' + tag_id).val(edit_input.val()); // This field controls if the value was changed

        //alert if not successful, animate (red color) and set the old value of tag
            } else {
                alert("NENE: " + data.message)
                edit_input
                    .css({backgroundColor:'#ff0000'})
                    .delay( animation.delay )
                    .animate({ backgroundColor: animation.endColor })
                    .val(oldval);
            }
        })
        // focus on add form
        var add_form  = $('#add-new-tag'),
            add_input = add_form.find('#new-tag');
        add_input.focus();
    });

}(jQuery));