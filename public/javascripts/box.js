var calcelSortable = false;
var inSortable = false;
var lock = false;

function moreInfo(id) {
    $.ajax({
        method: "POST",
        url: "ajax.php?act=box&option=more-info",
        data: {id: id}
    }).done(function (data) {
        var box_dialog = $("<div>" + data + "</div>").appendTo("body");
        box_dialog.dialog({
            title: 'Informações detalhadas',
            width: 439,
            cache: false,
            resizable: false,
            close: function () {
                $(this).dialog("close");
                $(this).remove();
            }
        });
    });

}

function configBox(box) {
    $.ajax({
        method: "POST",
        url: "ajax.php?act=box&option=options",
        data: {box: box}
    })
            .done(function (data) {
                var box_dialog = $("<div>" + data + "</div>").appendTo("body");
                box_dialog.dialog({
                    title: 'Configurações da Box ' + box,
                    width: 280,
                    cache: false,
                    resizable: false,
                    close: function () {
                        $(this).dialog("close");
                        $(this).remove();
                    }
                });
            });

}

$(document).ready(function () {

    $(".slot-options").menu();
    
    $(".box-menu").hover(function () {
        if (!lock) {
            $(this).children('.slot-options').css('display', 'block');
            lock = true;
        } else {
            $(this).children('.slot-options').css('display', 'none');
            lock = false;
        }
    });

    $(".box-menu").bind('touchstart', function () {
        $('.slot-options').css('display', 'none');
        
        if (!lock) {
            $(this).children('.slot-options').css('display', 'block');
            lock = true;
        } else {
            $(this).children('.slot-options').css('display', 'none');
            lock = false;
        }
    });

    $(".colorbox-equip").colorbox({
        width: "520",
        height: "330",
        iframe: true
    });

    $(".colorbox-sell").colorbox({
        width: "600",
        height: "500",
        iframe: true,
        onClosed: function () {
            $(".box-menu").children('.slot-options').css('display', 'none');
            lock = false;
        }
    });
    
    $(".colorbox-release").colorbox({
        width: "500",
        height: "250",
        iframe: true,
        onClosed: function () {
            $(".box-menu").children('.slot-options').css('display', 'none');
            lock = false;
        }
    });

    $("#hand ul").sortable({
        connectWith: ".connectedSortable",
        placeholder: "ui-state-highlight",
        containment: '#containmentSortable',
        revert: true,
        delay: 50,
        update: function (event, ui) {
            if (!calcelSortable) {
                $.post('ajax.php?act=box&option=order', $(this).sortable("serialize"), function (data) {
                    if (data != '1') {
                        try {
                            calcelSortable = true;
                            ui.sender.sortable("cancel");
                            location.reload();
                        } catch (e) {
                        }
                    }
                });
            } else {
                calcelSortable = !calcelSortable;
            }
        },
        receive: function (event, ui) {
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

                    if (event.target.childElementCount > 1) {
                        calcelSortable = true;
                        ui.sender.sortable("cancel");
                        return;
                    }

                    $.post('ajax.php?act=box&option=bringaway', 'target=' + event.target.id + '&item=' + ui.item.context.id,
                            function (data) {
                                if (data == "0") {
                                    location.reload();
                                }
                            });
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
                    $("#pokemon_equip_slot").sortable({
                        connectWith: ".connectedSortable",
                        placeholder: "ui-state-highlight",
                        containment: '#containmentSortable',
                        revert: true,
                        delay: 50,
                        receive: function (event, ui) {
                            ui.item.find('.colorbox-equip').click();
                            calcelSortable = true;
                            ui.sender.sortable("cancel");
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
                            $("#pokemon_sell_slot").sortable({
                                connectWith: ".connectedSortable",
                                placeholder: "ui-state-highlight",
                                containment: '#containmentSortable',
                                revert: true,
                                delay: 50,
                                receive: function (event, ui) {
                                    ui.item.find('.colorbox-sell').click();
                                    calcelSortable = true;
                                    ui.sender.sortable("cancel");
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
                                    $("#pokemon_release_slot").sortable({
                                        connectWith: ".connectedSortable",
                                        placeholder: "ui-state-highlight",
                                        containment: '#containmentSortable',
                                        revert: true,
                                        delay: 50,
                                        receive: function (event, ui) {
                                            ui.item.find('.colorbox-release').click();
                                            calcelSortable = true;
                                            ui.sender.sortable("cancel");
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
                        }
                    }).disableSelection();

                    $('#containmentSortable').show();
                }
            }).disableSelection();
        }
    }).disableSelection();
});