function initFDP(element, showEmpty) {

    element.find('a[id^="JDRollUserControlLink"]').each(function(index, elt) {
        var jElt = $(elt);
        var isEmpty = 0;
        if (jElt.hasClass("editable-empty"))
            isEmpty = 1;


        if (element.has("#" + jElt.attr('id') + "_hidden").length > 0) {
            jElt.text($('#' + jElt.attr('id') + '_hidden').val());
            jElt.removeClass("editable-empty");
            isEmpty = 0;
        }

        jElt.editable({
            value: $('#' + jElt.attr('id') + '_hidden').val(),
            success: function(response, newValue) {
                if (newValue.city) {
                    if (jElt.css('color') == 'rgb(221, 17, 68)')
                        jElt.css('color', '#0088cc');
                    jElt.text(newValue.city);
                    if (jElt.parent().parent().has('#' + jElt.attr('id') + '_hidden').length > 0)
                        jElt.parent().parent().find('#' + jElt.attr('id') + '_hidden').val(newValue.city);
                    else
                        jElt.parent().append('<input type="hidden" id="' + jElt.attr('id') + '_hidden" value="' + newValue.city + '"/>');
                } else {

                    if (newValue.city == "") {
                        if (jElt.css('color') == 'rgb(0, 136, 204)')
                            jElt.css('color', '#DD1144');
                        if (jElt.parent().parent().has('#' + jElt.attr('id') + '_hidden').length > 0) {

                            jElt.parent().parent().find('#' + jElt.attr('id') + '_hidden').val('');
                        } else {
                            jElt.parent().append("<input type='hidden' id='" + jElt.attr('id') + "_hidden' value='' />");
                        }
                    } else {
                        if (newValue == "") {
                            if (jElt.css('color') == 'rgb(0, 136, 204)')
                                jElt.css('color', '#DD1144');
                        } else {
                            if (jElt.css('color') == 'rgb(221, 17, 68)')
                                jElt.css('color', '#0088cc');

                        }

                        if (jElt.parent().parent().has('#' + jElt.attr('id') + '_hidden').length > 0) {

                            jElt.parent().parent().find('#' + jElt.attr('id') + '_hidden').val(newValue);
                        } else {
                            jElt.parent().append('<input type="hidden" id="' + jElt.attr('id') + '_hidden" value="' + newValue + '"/>');
                        }
                    }
                }
            }
        });

        if (isEmpty) {
            jElt.addClass("editable-empty");
            if (!showEmpty) {
                jElt.css("display", "none");
            }

        }
    });
}
