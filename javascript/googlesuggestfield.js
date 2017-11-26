(function($) {
    var $editFormID = "Form_EditForm";
    var $altEditFormID = "Form_ItemEditForm";

    $.entwine('seo', function($){
        $('.cms-edit-form input.googlesuggest').entwine({
            // Constructor: onmatch
            onmatch : function() {
                if (!$("#" + $editFormID ).length) {
                    $editFormID = $altEditFormID;
                }
                var field_id = $(this).attr("ID");
                $( "#"+ field_id ).autocomplete({
                    source: function( request, response ) {
                        $.ajax({
                            url: "//suggestqueries.google.com/complete/search",
                            dataType: "jsonp",
                            data: {
                                client: 'firefox',
                                q: request.term
                            },
                            success: function( data ) {
                                response( data[1] );
                            }
                        });
                    },
                    minLength: 3
                });
            },
        });
    });
})(jQuery);