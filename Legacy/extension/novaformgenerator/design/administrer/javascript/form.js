$(function () {
    managePage.init();
    manageAttributes.init();
    manageAnswer.init();
    manageSubattributes.init();
    manageGrid.init();
    manageIllustration.init();
    exportAnswers.init();
});
var managePage = function () {
    function _init() {
        $(document).on("click", ".page_rename", function () {
            var pageId = $(this).attr("data-pageid");
            $("#" + pageId).show();
        });

        $("#add_page").on("click", function () {
            $("#ajaxuploader-loader").show();
            var nbPage = $("#questions .page_block").length;
            $.ez('form::addPage', { nbPage: nbPage }, function (data) {
                if (data.error_text) {
                    $("#questions").append(data.error_text);
                } else {
                    $("#questions").append(data.content);
                }
                managePage.initSortable();
                $("#ajaxuploader-loader").hide();
            });
        });

        $(document).on("focusout", ".page_name", function () {
            var value = $(this).val();
            var id = $(this).attr("id");
            var input = $(this);
            $.ez('form::changePageName', {id: id, value: value }, function (data) {
                if (data.error_text) {
                    $("#questions").append(data.error_text);
                } else {
                    input.hide();
                    $("#span-" + id).html(data.content);
                }
            });
        });

        $(document).on("click", ".delete_page", function () {
            var id = $(this).attr("data-pageid");
            $.ez('form::deletePage', {id: id}, function (data) {
                if (data.error_text) {
                    $("#questions").append(data.error_text);
                } else {
                    $("#container_" + id).hide();
                }
            });
        });

        managePage.initSortable();
    }

    function _initSortable() {
        $(".sortable").each(function () {
            if ($(this).data('sortable-enabled')) {
                return;
            }
            $(this).data('sortable-enabled', 1);
            $(this).sortable({
                    update: function( event, ui ) {
                        var i = 0;
                        $(this).children('*').each(function( ) {
                            $(this).find('.rank:first').val(i++);
                        })
                    },
                    placeholder: "ui-state-highlight",
                    opacity: 0.5,
                    cursor: "move"

                }
            );
        });
    }

    return {init: _init, initSortable: _initSortable}
}();

var manageAttributes = function () {
    function _init() {
        $( 'body' ).on( 'blur', '.nova_ezformgenerator .parent input[type=text]', function() {
            if (!this.name.match(/^attribute_[a-z]+_[0-9]+\[name\]$/)) {
                return false;
            }
            var tabName = this.name.split('_');
            var type = tabName[1];
            var id = parseInt(tabName[2]);
            var identifier = $( this ).parents( '.parent' ).find( '#attribute_' + type + '_' + id.toString() + '_identifier' );
            if ( identifier.val() == "" && $( this ).val() != "" ) {
                nameCleaned = _removeDiacritics( $( this ).val() );
                if ( nameCleaned.substr(-1) != "_" ) {
                    nameCleaned += "_";
                }
                nameCleaned += id.toString();
                identifier.val( nameCleaned );
            }
        });
        $(document).on("click", ".add_attribute", function () {
            var pageid = $(this).attr("data-id");
            var attribute = $("#add_attribute_" + pageid).val();
            if (attribute == 0) {
                alert("Veuillez sélectionner un type d'attribut");
            } else {
                var pageContent = $("#attribute_content_" + pageid);
                var placement = $(pageContent).children('fieldset.parent').length + 1;
                $.ez('form::addAttribute', {pageid: pageid, attribute: attribute, placement: placement}, function (data) {
                    if (data.error_text) {
                        $("#attribute_content_" + pageid).append(data.error_text);
                    } else {
                        $("#attribute_content_" + pageid).append(data.content);
                        var rank = $(pageContent).children('fieldset.parent').length;
                        $(pageContent).children('fieldset.parent:last').find('.rank:first').val(rank);
                        managePage.initSortable();
                    }
                });
            }
        });

        $(document).on("click", ".delete_attribute", function () {
            var parent = $(this).parent().parent();
            var div = $(this).parent().next("div");
            var id = div.attr("id").split("_")[2];
            $.ez('form::deleteAttribute', {id: id }, function (data) {
                if (data.error_text) {
                    parent.append(data.error_text);
                } else {
                    parent.hide();
                }
            });
        });
    }

    function _removeDiacritics (str) {

        var defaultDiacriticsRemovalMap = [
            {'base':'a', 'letters':/[\u0041\u24B6\uFF21\u00C0\u00C1\u00C2\u1EA6\u1EA4\u1EAA\u1EA8\u00C3\u0100\u0102\u1EB0\u1EAE\u1EB4\u1EB2\u0226\u01E0\u00C4\u01DE\u1EA2\u00C5\u01FA\u01CD\u0200\u0202\u1EA0\u1EAC\u1EB6\u1E00\u0104\u023A\u2C6F]/g},
            {'base':'aa','letters':/[\uA732]/g},
            {'base':'ae','letters':/[\u00C6\u01FC\u01E2]/g},
            {'base':'ao','letters':/[\uA734]/g},
            {'base':'au','letters':/[\uA736]/g},
            {'base':'av','letters':/[\uA738\uA73A]/g},
            {'base':'ay','letters':/[\uA73C]/g},
            {'base':'b', 'letters':/[\u0042\u24B7\uFF22\u1E02\u1E04\u1E06\u0243\u0182\u0181]/g},
            {'base':'c', 'letters':/[\u0043\u24B8\uFF23\u0106\u0108\u010A\u010C\u00C7\u1E08\u0187\u023B\uA73E]/g},
            {'base':'d', 'letters':/[\u0044\u24B9\uFF24\u1E0A\u010E\u1E0C\u1E10\u1E12\u1E0E\u0110\u018B\u018A\u0189\uA779]/g},
            {'base':'dz','letters':/[\u01F1\u01C4]/g},
            {'base':'dz','letters':/[\u01F2\u01C5]/g},
            {'base':'e', 'letters':/[\u0045\u24BA\uFF25\u00C8\u00C9\u00CA\u1EC0\u1EBE\u1EC4\u1EC2\u1EBC\u0112\u1E14\u1E16\u0114\u0116\u00CB\u1EBA\u011A\u0204\u0206\u1EB8\u1EC6\u0228\u1E1C\u0118\u1E18\u1E1A\u0190\u018E]/g},
            {'base':'f', 'letters':/[\u0046\u24BB\uFF26\u1E1E\u0191\uA77B]/g},
            {'base':'g', 'letters':/[\u0047\u24BC\uFF27\u01F4\u011C\u1E20\u011E\u0120\u01E6\u0122\u01E4\u0193\uA7A0\uA77D\uA77E]/g},
            {'base':'h', 'letters':/[\u0048\u24BD\uFF28\u0124\u1E22\u1E26\u021E\u1E24\u1E28\u1E2A\u0126\u2C67\u2C75\uA78D]/g},
            {'base':'i', 'letters':/[\u0049\u24BE\uFF29\u00CC\u00CD\u00CE\u0128\u012A\u012C\u0130\u00CF\u1E2E\u1EC8\u01CF\u0208\u020A\u1ECA\u012E\u1E2C\u0197]/g},
            {'base':'j', 'letters':/[\u004A\u24BF\uFF2A\u0134\u0248]/g},
            {'base':'k', 'letters':/[\u004B\u24C0\uFF2B\u1E30\u01E8\u1E32\u0136\u1E34\u0198\u2C69\uA740\uA742\uA744\uA7A2]/g},
            {'base':'l', 'letters':/[\u004C\u24C1\uFF2C\u013F\u0139\u013D\u1E36\u1E38\u013B\u1E3C\u1E3A\u0141\u023D\u2C62\u2C60\uA748\uA746\uA780]/g},
            {'base':'lj','letters':/[\u01C7]/g},
            {'base':'lj','letters':/[\u01C8]/g},
            {'base':'m', 'letters':/[\u004D\u24C2\uFF2D\u1E3E\u1E40\u1E42\u2C6E\u019C]/g},
            {'base':'n', 'letters':/[\u004E\u24C3\uFF2E\u01F8\u0143\u00D1\u1E44\u0147\u1E46\u0145\u1E4A\u1E48\u0220\u019D\uA790\uA7A4]/g},
            {'base':'nj','letters':/[\u01CA]/g},
            {'base':'nj','letters':/[\u01CB]/g},
            {'base':'o', 'letters':/[\u004F\u24C4\uFF2F\u00D2\u00D3\u00D4\u1ED2\u1ED0\u1ED6\u1ED4\u00D5\u1E4C\u022C\u1E4E\u014C\u1E50\u1E52\u014E\u022E\u0230\u00D6\u022A\u1ECE\u0150\u01D1\u020C\u020E\u01A0\u1EDC\u1EDA\u1EE0\u1EDE\u1EE2\u1ECC\u1ED8\u01EA\u01EC\u00D8\u01FE\u0186\u019F\uA74A\uA74C]/g},
            {'base':'oi','letters':/[\u01A2]/g},
            {'base':'oo','letters':/[\uA74E]/g},
            {'base':'ou','letters':/[\u0222]/g},
            {'base':'p', 'letters':/[\u0050\u24C5\uFF30\u1E54\u1E56\u01A4\u2C63\uA750\uA752\uA754]/g},
            {'base':'q', 'letters':/[\u0051\u24C6\uFF31\uA756\uA758\u024A]/g},
            {'base':'r', 'letters':/[\u0052\u24C7\uFF32\u0154\u1E58\u0158\u0210\u0212\u1E5A\u1E5C\u0156\u1E5E\u024C\u2C64\uA75A\uA7A6\uA782]/g},
            {'base':'s', 'letters':/[\u0053\u24C8\uFF33\u1E9E\u015A\u1E64\u015C\u1E60\u0160\u1E66\u1E62\u1E68\u0218\u015E\u2C7E\uA7A8\uA784]/g},
            {'base':'t', 'letters':/[\u0054\u24C9\uFF34\u1E6A\u0164\u1E6C\u021A\u0162\u1E70\u1E6E\u0166\u01AC\u01AE\u023E\uA786]/g},
            {'base':'tz','letters':/[\uA728]/g},
            {'base':'u', 'letters':/[\u0055\u24CA\uFF35\u00D9\u00DA\u00DB\u0168\u1E78\u016A\u1E7A\u016C\u00DC\u01DB\u01D7\u01D5\u01D9\u1EE6\u016E\u0170\u01D3\u0214\u0216\u01AF\u1EEA\u1EE8\u1EEE\u1EEC\u1EF0\u1EE4\u1E72\u0172\u1E76\u1E74\u0244]/g},
            {'base':'v', 'letters':/[\u0056\u24CB\uFF36\u1E7C\u1E7E\u01B2\uA75E\u0245]/g},
            {'base':'vy','letters':/[\uA760]/g},
            {'base':'w', 'letters':/[\u0057\u24CC\uFF37\u1E80\u1E82\u0174\u1E86\u1E84\u1E88\u2C72]/g},
            {'base':'x', 'letters':/[\u0058\u24CD\uFF38\u1E8A\u1E8C]/g},
            {'base':'y', 'letters':/[\u0059\u24CE\uFF39\u1EF2\u00DD\u0176\u1EF8\u0232\u1E8E\u0178\u1EF6\u1EF4\u01B3\u024E\u1EFE]/g},
            {'base':'z', 'letters':/[\u005A\u24CF\uFF3A\u0179\u1E90\u017B\u017D\u1E92\u1E94\u01B5\u0224\u2C7F\u2C6B\uA762]/g},
            {'base':'a', 'letters':/[\u0061\u24D0\uFF41\u1E9A\u00E0\u00E1\u00E2\u1EA7\u1EA5\u1EAB\u1EA9\u00E3\u0101\u0103\u1EB1\u1EAF\u1EB5\u1EB3\u0227\u01E1\u00E4\u01DF\u1EA3\u00E5\u01FB\u01CE\u0201\u0203\u1EA1\u1EAD\u1EB7\u1E01\u0105\u2C65\u0250]/g},
            {'base':'aa','letters':/[\uA733]/g},
            {'base':'ae','letters':/[\u00E6\u01FD\u01E3]/g},
            {'base':'ao','letters':/[\uA735]/g},
            {'base':'au','letters':/[\uA737]/g},
            {'base':'av','letters':/[\uA739\uA73B]/g},
            {'base':'ay','letters':/[\uA73D]/g},
            {'base':'b', 'letters':/[\u0062\u24D1\uFF42\u1E03\u1E05\u1E07\u0180\u0183\u0253]/g},
            {'base':'c', 'letters':/[\u0063\u24D2\uFF43\u0107\u0109\u010B\u010D\u00E7\u1E09\u0188\u023C\uA73F\u2184]/g},
            {'base':'d', 'letters':/[\u0064\u24D3\uFF44\u1E0B\u010F\u1E0D\u1E11\u1E13\u1E0F\u0111\u018C\u0256\u0257\uA77A]/g},
            {'base':'dz','letters':/[\u01F3\u01C6]/g},
            {'base':'e', 'letters':/[\u0065\u24D4\uFF45\u00E8\u00E9\u00EA\u1EC1\u1EBF\u1EC5\u1EC3\u1EBD\u0113\u1E15\u1E17\u0115\u0117\u00EB\u1EBB\u011B\u0205\u0207\u1EB9\u1EC7\u0229\u1E1D\u0119\u1E19\u1E1B\u0247\u025B\u01DD]/g},
            {'base':'f', 'letters':/[\u0066\u24D5\uFF46\u1E1F\u0192\uA77C]/g},
            {'base':'g', 'letters':/[\u0067\u24D6\uFF47\u01F5\u011D\u1E21\u011F\u0121\u01E7\u0123\u01E5\u0260\uA7A1\u1D79\uA77F]/g},
            {'base':'h', 'letters':/[\u0068\u24D7\uFF48\u0125\u1E23\u1E27\u021F\u1E25\u1E29\u1E2B\u1E96\u0127\u2C68\u2C76\u0265]/g},
            {'base':'hv','letters':/[\u0195]/g},
            {'base':'i', 'letters':/[\u0069\u24D8\uFF49\u00EC\u00ED\u00EE\u0129\u012B\u012D\u00EF\u1E2F\u1EC9\u01D0\u0209\u020B\u1ECB\u012F\u1E2D\u0268\u0131]/g},
            {'base':'j', 'letters':/[\u006A\u24D9\uFF4A\u0135\u01F0\u0249]/g},
            {'base':'k', 'letters':/[\u006B\u24DA\uFF4B\u1E31\u01E9\u1E33\u0137\u1E35\u0199\u2C6A\uA741\uA743\uA745\uA7A3]/g},
            {'base':'l', 'letters':/[\u006C\u24DB\uFF4C\u0140\u013A\u013E\u1E37\u1E39\u013C\u1E3D\u1E3B\u017F\u0142\u019A\u026B\u2C61\uA749\uA781\uA747]/g},
            {'base':'lj','letters':/[\u01C9]/g},
            {'base':'m', 'letters':/[\u006D\u24DC\uFF4D\u1E3F\u1E41\u1E43\u0271\u026F]/g},
            {'base':'n', 'letters':/[\u006E\u24DD\uFF4E\u01F9\u0144\u00F1\u1E45\u0148\u1E47\u0146\u1E4B\u1E49\u019E\u0272\u0149\uA791\uA7A5]/g},
            {'base':'nj','letters':/[\u01CC]/g},
            {'base':'o', 'letters':/[\u006F\u24DE\uFF4F\u00F2\u00F3\u00F4\u1ED3\u1ED1\u1ED7\u1ED5\u00F5\u1E4D\u022D\u1E4F\u014D\u1E51\u1E53\u014F\u022F\u0231\u00F6\u022B\u1ECF\u0151\u01D2\u020D\u020F\u01A1\u1EDD\u1EDB\u1EE1\u1EDF\u1EE3\u1ECD\u1ED9\u01EB\u01ED\u00F8\u01FF\u0254\uA74B\uA74D\u0275]/g},
            {'base':'oi','letters':/[\u01A3]/g},
            {'base':'ou','letters':/[\u0223]/g},
            {'base':'oo','letters':/[\uA74F]/g},
            {'base':'p','letters':/[\u0070\u24DF\uFF50\u1E55\u1E57\u01A5\u1D7D\uA751\uA753\uA755]/g},
            {'base':'q','letters':/[\u0071\u24E0\uFF51\u024B\uA757\uA759]/g},
            {'base':'r','letters':/[\u0072\u24E1\uFF52\u0155\u1E59\u0159\u0211\u0213\u1E5B\u1E5D\u0157\u1E5F\u024D\u027D\uA75B\uA7A7\uA783]/g},
            {'base':'s','letters':/[\u0073\u24E2\uFF53\u00DF\u015B\u1E65\u015D\u1E61\u0161\u1E67\u1E63\u1E69\u0219\u015F\u023F\uA7A9\uA785\u1E9B]/g},
            {'base':'t','letters':/[\u0074\u24E3\uFF54\u1E6B\u1E97\u0165\u1E6D\u021B\u0163\u1E71\u1E6F\u0167\u01AD\u0288\u2C66\uA787]/g},
            {'base':'tz','letters':/[\uA729]/g},
            {'base':'u','letters':/[\u0075\u24E4\uFF55\u00F9\u00FA\u00FB\u0169\u1E79\u016B\u1E7B\u016D\u00FC\u01DC\u01D8\u01D6\u01DA\u1EE7\u016F\u0171\u01D4\u0215\u0217\u01B0\u1EEB\u1EE9\u1EEF\u1EED\u1EF1\u1EE5\u1E73\u0173\u1E77\u1E75\u0289]/g},
            {'base':'v','letters':/[\u0076\u24E5\uFF56\u1E7D\u1E7F\u028B\uA75F\u028C]/g},
            {'base':'vy','letters':/[\uA761]/g},
            {'base':'w','letters':/[\u0077\u24E6\uFF57\u1E81\u1E83\u0175\u1E87\u1E85\u1E98\u1E89\u2C73]/g},
            {'base':'x','letters':/[\u0078\u24E7\uFF58\u1E8B\u1E8D]/g},
            {'base':'y','letters':/[\u0079\u24E8\uFF59\u1EF3\u00FD\u0177\u1EF9\u0233\u1E8F\u00FF\u1EF7\u1E99\u1EF5\u01B4\u024F\u1EFF]/g},
            {'base':'z','letters':/[\u007A\u24E9\uFF5A\u017A\u1E91\u017C\u017E\u1E93\u1E95\u01B6\u0225\u0240\u2C6C\uA763]/g},
            {'base':'_','letters':/[,'"\[\]\{\}\(\)_\- =+*]/g}
        ];

        for(var i=0; i < defaultDiacriticsRemovalMap.length; i++) {
            str = str.replace( defaultDiacriticsRemovalMap[i].letters, defaultDiacriticsRemovalMap[i].base );
        }

        return str;

    }

    return {init: _init}
}();

var manageAnswer = function () {
    function _init() {
        $(document).on("click", ".button_answer", function () {
            var divparent = $(this).prev("div").prev('span').prev("input").prev('div');

            var collectionHolder = divparent.find("div.answers");
            var prototype = collectionHolder.attr("data-prototype");
            var newForm = prototype.replace(/__name__label__/g, "").replace(/__name__/g, collectionHolder.children().length);
            collectionHolder.append(newForm);
        });

        $(document).on("click", ".delete_answer", function () {
            $(this).parent().parent().remove();
        });
    }

    return {init: _init}
}();

var manageSubattributes = function () {
    function _init() {
        $(document).on("click", ".show_novsubattribute", function () {
            var div = $(this).parent();
            var parentdiv = $(this).parent().parent().parent();
            var id = parentdiv.attr("id").split("_")[2];
            $.ez('form::showAttributeList', {id: id}, function (data) {
                if (data.error_text) {
                    div.append(data.error_text);
                } else {
                    div.append(data.content);
                }
            });
        });

        $(document).on("click", ".delete_sub_attribute", function () {
            var parent = $(this).closest('.parent');

            $(parent).remove();
        });

        $(document).on("click", ".add_nova_ezformgenerator_subattribute", function () {
            var select = $(this).prev("select");
            var attribute_type_val = select.val();
            var collectionHolder = $(this).parent().find("div.subattributes");
            if (attribute_type_val == 0) {
                alert("Veuillez sélectionner un type d'attribut");
            } else {
                var selectionValue_id = collectionHolder.attr("data-id");

                var id = collectionHolder.attr("id");
                var prototype = collectionHolder.attr("data-prototype-" + attribute_type_val);


                if (attribute_type_val == 'selection') {
                    var level = $(this).closest('.answers').data('level');
                    var regex = new RegExp("__name__" + level + "__", "g");
                    var newForm = prototype
                        .replace(regex, collectionHolder.children().length);

                } else {
                    var regex1 = new RegExp("_" + attribute_type_val + "_", "g");
                    var regex2 = new RegExp("\\[" + attribute_type_val + "\\]", "g");
                    var regex3 = new RegExp("_" + attribute_type_val, "g");

                    var newForm = prototype
                            .replace(regex1, "_subAttributes_" + collectionHolder.children().length + "_id_")
                            .replace(regex2, "[subAttributes][" + collectionHolder.children().length + "][id]")
                            .replace(regex3, "_subAttributes_" + collectionHolder.children().length + "_id")
                        ;
                }

                var rank = collectionHolder.children().length;
                var id = id + '_' + collectionHolder.children().length;

                collectionHolder.append('<div><label></label><div id="' + id + '">' + newForm + '</div></div>');

                $(collectionHolder).find('#'+id).find('.rank:first').val(rank);

                managePage.initSortable();
            }
        });

        $(".nova_ezformgenerator").on("click", ".delete_subattribute", function () {
            $(this).parent().parent().remove();
        });

    }

    return {init: _init}
}();


var manageGrid = function () {

    function _init() {

        $(".nova_ezformgenerator").on("click", ".grid_new_row", function () {
            var table = $(this).parent().find('table');
            var body = $(table).find('tbody');
            var head = $(table).find('thead');

            var rowPrototype = $(table).data('prototype-row');
            var countRow = $(body).find("tr").length;
            var countColumn = $(head).find("th").length - 1;

            var rowInput = rowPrototype.replace(/__grid_row__/g, countRow);

            // add new row
            var row = $('<tr></tr>');
            $(row).append($('<td>' + rowInput + '</td>'));
            $(body).append(row);

            var rowWidgetPrototype = $(row).find('td:first input').data('prototype');
            for (var i = 0; i < countColumn; ++i) {
                // add columns for new row
                var rowWidget = rowWidgetPrototype.replace(/__grid_row_name__/g, i);

                var td = $("<td>" + rowWidget + "</td>");
                $(row).append(td);

                manageGrid.makeWidgetForColumn(td, table, i);
            }

            // add remove button at the end of the row
            $(row).append($('<td><button class="grid_delete_row">X</button></td>'));

            return false;
        });

        $(".nova_ezformgenerator").on("click", ".grid_new_column", function () {
            var table = $(this).parent().find('table');
            var body = $(table).find('tbody');
            var head = $(table).find('thead');

            var columnPrototype = $(table).data('prototype-column');
            var countColumn = $(head).find("th").length - 1;

            var columnInput = columnPrototype.replace(/__grid_column__/g, countColumn);

            // add new row
            var column = $('<th>' + columnInput + '</th>');
            $(head).find('tr').append(column);


            $(body).children('tr').each(function () {
                var rowWidgetPrototype = $(this).find('td:first input').data('prototype');
                var rowWidget = rowWidgetPrototype.replace(/__grid_row_name__/g, countColumn);

                var td = $('<td>' + rowWidget + '</td>');
                $(this).find('td').last().before(td);

                manageGrid.makeWidgetForColumn(td, table, countColumn);
            });

            return false;
        });

        $(".nova_ezformgenerator").on("click", ".grid_delete_row", function () {
            $(this).closest('tr').remove();
        });

        $(".nova_ezformgenerator").on("click", ".grid_delete_column", function () {
            var table = $(this).closest('table');
            var body = $(table).find('tbody');

            var container = $(this).closest('tr');
            var th = $(this).closest('th');
            var index = $(container).find('th').index(th);

            $(th).remove();

            $(body).children('tr').each(function () {
                $(this).find('td').get(index).remove();
            });
        });

        $(".nova_ezformgenerator").on("change", ".grid_item_type_selector", function () {
            var table = $(this).closest('table');
            var body = $(table).find('tbody');
            var container = $(this).closest('tr');
            var td = $(this).closest('th');
            var type = $(this).val();
            var index = $(container).find('th').index(td);
            index = index - 1; // skip empty cell on top left

            $(body).children('tr').each(function () {
                var td = $(this).find('td').get(index + 1);
                $(td).find('input.value').val('');
                manageGrid.makeWidgetForColumn(td, table, index);
            });

        });

        $('.novaform_grid').each(function () {
            manageGrid.initGridWidgets(this);
        });

        $('table.novaform_grid').find('input[type=radio]').change(function() {

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

    function _getTypeOfColumn(table, columnIndex) {
        var columnHeader = $(table).find('thead th').get(columnIndex + 1);

        if (!columnHeader) {
            return null;
        }
        return $(columnHeader).find('select').val();
    }

    function _makeWidgetForColumn(td, table, index) {
        var type = manageGrid.getTypeOfColumn(table, index);
        var inputValue = $(td).find('input.value');
        var inputType = $(td).find('input.type');

        if (type == 'checkbox' || type == 'radio') {
            if ($(inputValue).val()) {
                $(inputValue).attr('checked', 'checked');
            } else {
                $(inputValue).removeAttr('checked');
            }
            $(inputValue).val(1);
        }
        $(inputType).val(type);
        $(inputValue).attr('type', type);
    }

    function _initGridWidgets(table) {
        $(table).find('tbody tr').each(function () {
            var i = 0;
            var first = true;
            $(this).find('td').each(function () {
                if (first) {
                    first = false;
                    return;
                }
                manageGrid.makeWidgetForColumn(this, table, i);
                i++;
            });
        });
    }

    return {init: _init, getTypeOfColumn: _getTypeOfColumn, makeWidgetForColumn: _makeWidgetForColumn, initGridWidgets: _initGridWidgets}
}();

var manageIllustration = function(){
    function _init(){
        var tabClass = '';
        var subElementID = '';
        var tabHref = window.location.href.split('/');
        var button = '';
        var isDataSubAttr = '';
        // Open popin with the content edit for creation
        $("body").on("click", ".imageCreate", function () {
            var imageObjectId = 0;
            button = $(this);
            tabClass = $(this).attr('class').split(' ');
            isDataSubAttr = $(this).prev('div').attr('data-sub-attribute');
            if( isDataSubAttr  != undefined && isDataSubAttr != '' ){
                subElementID = $(this).prev('div').attr('id');
            }
            $("#ImageCreateModal").find(".modal-body").html("");
            var ezformtoken = $('input[name="ezxform_token"]').val();
            if($(this).attr('image-content-id')!= undefined && $(this).attr('image-content-id')!=''){
                imageObjectId = $(this).attr('image-content-id');
            }
            $.ajax({
                type: "POST",
                data: { ezxform_token: ezformtoken },
                url: $.ez.url+'run/image/upload/(image)/'+imageObjectId,
                success: function (data) {
                    $("#ImageCreateModal").find(".modal-body").append(data);
                    $("#ImageCreateModal").modal("show");
                    var action = $('#imageFormSubmit').attr('action');
                    $('#imageFormSubmit').attr('action',action+"/"+tabHref[6]+"/"+tabHref[7]);
                    $('#upload_image').attr('src',$('#imageFormSubmit').attr('action')+"/"+tabHref[6]+"/"+tabHref[7]+"/");
                    $('#imageFormSubmit').attr('target','upload_image');
                    //Ajax request for submitting form
                    $('#upload_image').on('load',function(){
                        $('#file').html('');
                        $('#name').html('');
                        $('#generic_file').html('');
                        try{
                            var oResult = $.parseJSON($(this).contents().find('body').html());
                            if(!oResult.success){
                                $.each(oResult.error,function(index,value){
                                    $('#'+index).html(value);
                                })
                            }else{
                                if(subElementID !=''){
                                    $('#'+subElementID+'_illustration_object_id').val(oResult.objectId);
                                }else{
                                    $('.attr_'+tabClass[1]).val(oResult.objectId);
                                }
                                $('.success').html(oResult.success);
                                $('#image_uploaded').append('<img width="200" src="/' + oResult.image + '" alt=""/>');
                                $('#links').append('<input id="edit_image" type="button" value="Modifier" class="button" object_image_id="'+oResult.objectId+'">');
                                $('#links').append('<input type="button" value="Supprimer" class="button deleteAction">');

                                $('#file_uploader').remove();
                                button.next().html(oResult.name);
                                button.attr('image-content-id',oResult.objectId);

                                $("#btnSubmitImage").attr('value','OK');
                                $("#btnSubmitImage").attr('type','button');
                                $("#btnSubmitImage").addClass('upload-ok');
                            }
                        }catch(err){}
                    })
                },
                error: function () {}
            });
        });
        $(document).on('click','#edit_image',function(){
            var objectId = $(this).attr('object_image_id');
            var linkImage = tabHref[0]+"//"+tabHref[2]+"/"+tabHref[3]+"/"+tabHref[4]+"/"+tabHref[5]
                +"/"+objectId+"/f/"+tabHref[8];
            window.open(linkImage,'_blank');
        })

        $(document).on('click','.upload-ok',function(){
            $( "#ImageCreateModal" ).modal( "hide" );
        })
        //Delete image
        $(document).on('click', '.deleteAction', function () {
            var ezformtoken = $('input[name="ezxform_token"]').val();
            var objectId = button.attr('image-content-id');
            if (confirm("Etes vous sûr de supprimer cet image ?")) {
                $.ajax({
                    type: 'POST',
                    url: $.ez.url+'run/image/delete/' + objectId,
                    data: 'ezxform_token=' + ezformtoken,
                    success: function (data) {

                        if(subElementID !=''){
                            $('#'+subElementID+'_illustration_object_id').val("");
                        }else{
                            $('.attr_'+tabClass[1]).val("");
                        }

                        button.removeAttr('image-content-id');
                        button.prev().html('');
                        button.attr('value',"Gérer l'illustration");

                        $( "#ImageCreateModal" ).find( ".modal-body" ).html( "" );
                        $( "#ImageCreateModal" ).find( ".modal-body" ).append('<div class="success">Image supprimée avec succès</div>');

                        setTimeout(function(){
                            $( "#ImageCreateModal" ).modal( "hide" );
                        },2000);
                    }
                });
            }
        })
    }
    return { init: _init }
}();
var exportAnswers = function(){

    function _init(){
        $(document).on('click','#export_csv',function(){
            var objectId = $('#content_id').val();
            var tabHref = window.location.href.split('/');
            self.location.href = "/"+tabHref[3]+"/forms/list/(object)/"+objectId;
        })
    }
    return { init: _init }
}();
