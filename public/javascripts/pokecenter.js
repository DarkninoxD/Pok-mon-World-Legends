var calcelSortable = false;
var inSortable = false;

function moreInfo(id) {
    $.ajax({
        method: "POST",
        url: "ajax.php?act=box&option=more-info",
        data: {id: id}
    }).done(function (data) {
        var box_dialog = $("<div>" + data + "</div>").appendTo("body");
        box_dialog.dialog({
            title: 'Informações detalhadas',
            width: 415,
            cache: false,
            close: function () {
                $(this).dialog("close");
                $(this).remove();
            }
        });
    });

}


$(document).ready(function () {

    $(".slot-options").menu();
    $(".box-menu")
            .mouseover(function () {
                ($(this).children('.slot-options')).css('display', 'block');
            })
            .mouseout(function () {
                ($(this).children('.slot-options')).css('display', 'none');
            });

    $("#hand ul").sortable({
        connectWith: ".connectedSortable",
        placeholder: "ui-state-highlight",
        containment: '#containmentSortable',
        cancel: '.disabled-item',
        revert: true,
        delay: 50,
        update: function (event, ui) {

        },
        receive: function (event, ui) {
            let hand = $(this).sortable("serialize").split('&').join('').split('pkm[]=');
            let h;

            for (let i = 0; i < hand.length; i++) {
                h = hand[i];
                
                $('input[name="pokemon[]"][value="' + h + '"]').attr('checked', false);
            }

            if (event.target.childElementCount > 6) {
                calcelSortable = true;
                ui.sender.sortable("cancel");
                return;
            }
        },
        start: function (event, ui) {
            inSortable = true;
            try {
                dropmenuobj.style.visibility = "hidden";
            } catch (e) {
            }
            ui.item.find('.options').hide("blind", {}, 200);
        },
        stop: function (event, ui) {
            inSortable = false;
            ui.item.find('.options').show("blind", {}, 200);
        },
        create: function (event, ui) {
            $(".slot").sortable({
                connectWith: ".connectedSortable",
                placeholder: "ui-state-highlight",
                containment: '#containmentSortable',
                revert: true,
                delay: 50,
                receive: function (event, ui) {
                    var sender = ui.sender.context.id;

                    let slot = $(this).sortable("serialize").split('pkm[]=').join('');
                    $('input[name="pokemon[]"][value="' + slot + '"]').attr('checked', true);

                    if (event.target.childElementCount > 1) {
                        calcelSortable = true;
                        ui.sender.sortable("cancel");
                        return;
                    }
                },
                start: function (event, ui) {
                    inSortable = true;
                    try {
                        dropmenuobj.style.visibility = "hidden";
                    } catch (e) {
                    }
                    ui.item.find('.options').hide("blind", {}, 200);
                },
                stop: function (event, ui) {
                    inSortable = false;
                    ui.item.find('.options').show("blind", {}, 200);
                },
                create: function (event, ui) {
                    $('#containmentSortable').show();
                }
            }).disableSelection();
        }
    }).disableSelection();
});

$(function () {
    function double_slot () {
        $(".slot .ui-state-default").dblclick(function () {
            let id = $(this).attr('id').replace('pkm_', '');

            $("#hand ul").append(this);
            $('input[name="pokemon[]"][value="' + id + '"]').attr('checked', false);

            double_hand();
        });
    }
    
    function double_hand () {
        $("#hand ul .ui-state-default").not('.disabled-item').dblclick(function () {
            let disp = 0;
            let id = $(this).attr('id').replace('pkm_', '');

            for (let i = 1; i < 7; i++) {
                slot = $("#slot_" + i);

                if (!$('#slot_' + i + ' div').length) {
                    disp = i;
                    break;
                }
            }

            $("#slot_" + disp).append(this);
            $('input[name="pokemon[]"][value="' + id + '"]').attr('checked', true);

            double_slot();
        });
    }

    double_hand();
});