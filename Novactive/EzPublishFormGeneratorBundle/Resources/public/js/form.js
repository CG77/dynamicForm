$(function () {
    dynamicForm.init();
});

var dynamicForm = function () {

    var currentPage = 1;
    var maxPage = 1;
    var myForm;
    var btnSubmit;
    var lastPage = 0;
    function _init() {
        myForm = $("#dynamic_form");
        btnSubmit = $(myForm).find('.btnSubmit');
        lastPage = $('#last_page').val();
        currentPage = lastPage;
        maxPage = $(myForm).data('max-page');

        $(myForm).find('.tabGroup .select').each(
            function () {
                var sel = $(this).find(':selected');
                var linked = $(sel).data('target');
                //$(this).closest('.tabGroup').find('.tabContent .tabPanel').hide();
                $(linked).show();
        });

        $(myForm).find('input[type=radio]').each(function () {
            if($(this).is(':checked')){
                var id = $(this).attr('data-target');
                $(id).addClass('active');
            }
        });

        $(myForm).find('input[type=checkbox]').each(function () {
            if($(this).is(':checked')){
                var id = $(this).attr('data-target');
                $(id).addClass('in');
            }
        });

        $('.commune').on('keyup',function(){
            var value = $(this).val();
            var tabResults;
            var tabZipCode = [];
            $(this).autocomplete({
                source: function (request, response) {
                    $.ajax({
                        type: "POST",
                        dataType: "json",
                        url: "/form/get_commune",
                        data: "value=" + value,
                        success: function (data) {
                            tabResults = data;
                            response($.map(data, function (item) {
                                return {
                                    label: item.commune,
                                    value: item.commune,
                                    zipcode: item.zipcode,
                                    mds: item.mds
                                };
                            }));
                        },
                        error: function () {
                        }
                    });
                },
                select: function (event, ui) {
                    $('.code_postal').val(ui.item.zipcode);
                    $('.mds').val(ui.item.mds);
                    $('#form_collector_builder_zipcode_mds').val(ui.item.zipcode);
                }
            })
        })



//        $(myForm).submit(function () {
//            if (currentPage < maxPage) {
////                if (myForm.valid()) {
//                    //currentPage++;
//                    //_updateCurrentPage(currentPage);
////                }
//                //return false;
//            }
//            return true;
//        });

//        $(myForm).validate({
//            ignore: ":hidden",
//            errorElement: "span",
//            errorClass : "form_error",
//            errorPlacement: function ($error, $element) {
//                var name = $element.attr("name");
//
//                $error.insertAfter($element.closest('.field').find('label'));
//
////                if ($element.is(':radio')) {
////                    $error.insertAfter($element.closest('ul'));
////                } else {
////                    $error.insertBefore($element);
////                }
//                $element.focus();
//
//            }
//        });

//        $(myForm).find('input[type=file]').each(function () {
//            $(this).rules('add', {
//                extension: $(this).data('allowed-extensions')
//            });
//        });
        _updateCurrentPage(lastPage);

        if( $('#flash_errors').children().length != 0 ){
            _displayErrors($('#flash_errors').children());
        }

        $(myForm).find(".pager.previous").click(function () {
            if (currentPage > 1) {
                currentPage--;
                _updateCurrentPage(currentPage);
            }
            return false;
        });

        $(myForm).find(".pager.next").click(function () {
            if (currentPage < maxPage) {
                currentPage++;
                _updateCurrentPage(currentPage);
            }
            return false;
        });

        $(myForm).find('.collapsePanel').each(function () {
            // cas spécifique, pour coller au design fourni sur la gestion des cases à cocher
            // on déplace le contenu des sous questions en js
            var id = "#" + $(this).attr('id');

            $(myForm).find("input[type=checkbox]").each(function () {
                var target = $(this).data('target');
                if (target == id) {
                    $(id).appendTo($(this).parent().parent().parent());
                    $(this).attr("data-toggle", "collapse");
                }
            });
        });

        $(myForm).find('.tabGroup .select').change(function (ev, sel) {
            var sel = $(this).find(':selected');
            var linked = $(sel).data('target');
            $(this).closest('.tabGroup').find('.tabContent .tabPanel').hide();

            $(linked).show();
        });

        $(myForm).find('table input[type=radio]').change(function () {

            var td = $(this).closest('td');
            var tr = $(this).closest('tr');
            var colIndex = $(td).parent().children().index($(td));
            var rowIndex = $(tr).parent().children().index($(tr));
            var table = $(this).closest('table');

            $(table).find('tr td:nth-child(' + (colIndex + 1) + ') input[type=radio]').each(function () {
                var tr = $(this).closest('tr');
                var rowIndex2 = $(tr).parent().children().index($(tr));
                if (rowIndex2 != rowIndex) {
                    $(this).prop('checked', false);
                    $(this).parent().removeClass('checked');
                }
            });
        });
    }

    function _updateCurrentPage(pageIndex) {
        $(myForm).find(".page").each(function () {
            var page = $(this).data('page');
            if (page != pageIndex) {
                _hidePage(this);
            } else {
                _showPage(this);
            }
            $(".current_page").html(pageIndex);
        });
        if (pageIndex == maxPage) {
            $(btnSubmit).show();
        } else {
            $(btnSubmit).hide();
        }
        _updatePaginator(pageIndex);
    }

    function _updatePaginator(pageIndex) {
        if (pageIndex == 1) {
            $(myForm).find(".pager.previous").hide();
        } else {
            $(myForm).find(".pager.previous").show();
        }
        if (pageIndex == maxPage) {
            $(myForm).find(".pager.next").hide();
        } else {
            $(myForm).find(".pager.next").show();
        }
    }

    function _hidePage(pageElement) {
        $(pageElement).hide();

        $(pageElement).find(":input").addClass("ignore");
    }

    function _showPage(pageElement) {
        $(pageElement).show();

        $(pageElement).find(":input").removeClass("ignore");
    }

    function _displayErrors(errorElement){
        var errors = '';
        errorElement.each( function(){
            errors += $(this).val()+"\n";
            $(this).remove();
        })
        alert(errors);
    }

    return {init: _init, showPage: _showPage}
}();


