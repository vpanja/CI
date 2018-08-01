alert(0);
$(function ()
{
    // Variable to store your files
    var files;

    // Add events
    $('input[type=file]').on('change', prepareUpload);
    $('form').on('submit', uploadFiles);

    // Grab the files and set them to our variable
    function prepareUpload(event)
    {
        files = event.target.files;
    }

    // Catch the form submit and upload the files
    function uploadFiles(event)
    {
        event.stopPropagation(); // Stop stuff happening
        event.preventDefault(); // Totally stop stuff happening

        // START A LOADING SPINNER HERE

        // Create a formdata object and add the files
        var data = new FormData();
        $.each(files, function (key, value)
        {
            data.append(key, value);
        });

        $.ajax({
            url: submitUrl,
            type: 'POST',
            data: data,
            cache: false,
            dataType: 'json',
            processData: false, // Don't process the files
            contentType: false, // Set content type to false as jQuery will tell the server its a query string request
            success: function (data, textStatus, jqXHR)
                {
                    alert(data);
                if (typeof data.error === 'undefined')
                {
                    // Success so call function to process the form
                    submitForm(event, data);
                }
                else
                {
                    // Handle errors here
                    console.log('ERRORS: ' + data.error);
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                // Handle errors here
                console.log('ERRORS: ' + textStatus);
                // STOP LOADING SPINNER
            }
        });
    }

    function submitForm(event, data)
    {
        // Create a jQuery object from the form
        $form = $(event.target);

        // Serialize the form data
        var formData = $form.serialize();

        // You should sterilise the file names
        $.each(data.files, function (key, value)
        {
            formData = formData + '&filenames[]=' + value;
        });

        $.ajax({
            url: submitUrl,
            type: 'POST',
            data: formData,
            cache: false,
            dataType: 'json',
            success: function (data, textStatus, jqXHR)
            {
                if (typeof data.error === 'undefined')
                {
                    // Success so call function to process the form
                    console.log('SUCCESS: ' + data.success);
                }
                else
                {
                    // Handle errors here
                    console.log('ERRORS: ' + data.error);
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                // Handle errors here
                console.log('ERRORS: ' + textStatus);
            },
            complete: function ()
            {
                // STOP LOADING SPINNER
            }
        });
    }
});


    $(document).ready(function () {



        var option_row = 0;
        html = '<div id="tab-option-0" class="vtabs-content">';
        html += '	<table class="form">';
        html += '	  <tr>';
        html += '		<td>Required:</td>';
        html += '       <td><select name="product_option[0][required]">';
        html += '	      <option value="1">Yes</option>';
        html += '	      <option value="0">No</option>';
        html += '	    </select></td>';
        html += '     </tr>';
        html += '  </table>';
        html += '  <table id="option-value0" class="list">';
        html += '  	 <thead>';
        html += '      <tr>';
        html += '        <td class="left">Option Value:</td>';
        html += '        <td class="right">Quantity:</td>';
        html += '        <td class="right">Price:</td>';
        html += '        <td></td>';
        html += '      </tr>';
        html += '  	 </thead>';
        html += '    <tfoot>';
        html += '      <tr>';
        html += '        <td colspan="3"></td>';
        html += '        <td class="left"><a onclick="addOptionValue(0);" class="button">Add Option Value</a></td>';
        html += '      </tr>';
        html += '    </tfoot>';
        html += '  </table>';
        html += '  <select id="option-values0" style="display: none;">';
        html += '  <option value="0">Regular</option>';
        html += '  </select>';
        html += '</div>';
        $('#option-add').before('<a href="#tab-option-0" id="option-0" style="width:100%">Size&nbsp;<img src="<?php echo base_url() ?>assets/img/delete.png" alt="" onclick="$(\'#option-0\').remove(); $(\'#tab-option-0\').remove(); $(\'#vtab-option a:first\').trigger(\'click\'); return false;" /></a>');
        $('#vtab-option a').tabs();
        TinyMCEStart('#wysiwig_simple', null);
        // Create jQuery-UI tabs

        $("#tabs").tabs();
    });
