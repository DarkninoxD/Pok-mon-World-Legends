var Notify = {
    ErrorMessage: function(message, fade_out_time, container) {
        return Notify.InfoMessage(message, fade_out_time, 'error', container);
    },
    SuccessMessage: function(message, fade_out_time, container) {
        return Notify.InfoMessage(message, fade_out_time, 'success', container);
    },
    InfoMessage: function(message, fade_out_time, additional_class, container) {
        Notify.RemoveMessage();
        container = container || $('body');
        if (additional_class === true) additional_class = 'error';
        var autoHideBox = $("<div id=\"fader\"><div class=\"autoHideBox " + additional_class + "\"><p>" + message + "</p></div></div>").appendTo(container);
        if (additional_class == 'error' || additional_class == 'success') {
            fade_out_time = fade_out_time || 2000;
            autoHideBox.bind('click', function() {
                $(this).remove();
            }).delay(fade_out_time).fadeOut('slow', function() {
                $(this).remove();
            });
        }
    },
    RemoveMessage: function() {
        $('#fader').remove();
    }
};
var Game = {
    timeDiff: null,
    timeStart: null,
    timers: [],
    prograssBars: [],
    serverTimesnow: 0,
    goPage: function(link) {
        return document.location.href = link;
    },
    setImageTitles: function() {
        $('img').each(function() {
            var alt = $(this).attr('alt');
            if (!$(this).attr('title') && alt != '') $(this).attr('title', alt)
        })
    },
    addTimer: function(element, endTime) {
        var timer = [];
        timer.element = element;
        timer.endTime = endTime;
        Game.timers.push(timer);
    },
    getTime: function(element) {
        if (!element.html() || element.html().length == 0) return -1;
        if (element.html().indexOf('<a ') != -1) return -1;
        var part = element.html().split(":");
        for (var j = 1; j < 3; ++j) {
            if (part[j].charAt(0) == "0") part[j] = part[j].substring(1, part[j].length);
        }
        var hours, days;
        if (isNaN(part[0])) {
            var dayPart = part[0].split((/[a-z\s]+/i));
            hours = parseInt(dayPart[1], 10);
            days = parseInt(dayPart[0], 10);
        } else {
            hours = parseInt(part[0], 10);
            days = 0;
        }
        var minutes = parseInt(part[1], 10),
            seconds = parseInt(part[2], 10),
            time = days * 3600 * 24 + hours * 60 * 60 + minutes * 60 + seconds;
        return time;
    },
    getLocalTime: function() {
        var now = new Date();
        return Math.floor(now.getTime() / 1000)
    },
    startTimer: function() {
        var serverTime = Game.getTime($("#currentTime"));
        Game.timeDiff = serverTime - Game.getLocalTime();
        Game.timeStart = serverTime;
        ++Game.serverTimesnow;
        field = document.getElementsByTagName('div');
        for (var i = 0; i < field.length; ++i) {
            if (field[i].className == 'progress') {
                Game.prograssBars.push(field[i]);
            }
        }
        $('span.timer').each(function() {
            startTime = Game.getTime($(this));
            if (startTime >= 0) Game.addTimer($(this), (serverTime + startTime))
        });
        if (typeof window.ticker == 'undefined') {
            window.ticker = window.setInterval(function() {
                Game.tick();
            }, 1000);
            Game.tick();
        }
    },
    incrementDate: function() {
        currentDate = $('#currentDate').html();
        splitDate = currentDate.split('/');
        date = splitDate[0];
        month = splitDate[1] - 1;
        year = splitDate[2];
        dateObject = new Date(year, month, date);
        dateObject.setDate(dateObject.getDate() + 1);
        dateString = '';
        date = dateObject.getDate();
        month = dateObject.getMonth() + 1;
        year = dateObject.getFullYear();
        if (date < 10) dateString += '0';
        dateString += date + '/';
        if (month < 10) dateString += '0';
        dateString += month + '/';
        dateString += year;
        $('#currentDate').html(dateString);
    },
    formatTime: function(element, time, clamp) {
        var hours = Math.floor(time / 3600);
        if (clamp) hours = hours % 24;
        var minutes = Math.floor(time / 60) % 60;
        var seconds = time % 60;
        var timeString = hours + ':';
        if (minutes < 10) timeString += '0';
        timeString += minutes + ':';
        if (seconds < 10) timeString += '0';
        timeString += seconds;
        $(element).html(timeString);
        if ($(element).attr('id') == 'currentTime' && timeString == '0:00:00') Game.incrementDate()
    },
    tickTime: function() {
        var serverTime = $("#currentTime");
        if (serverTime !== null) {
            var time = Game.getLocalTime() + Game.timeDiff;
            Game.formatTime(serverTime, time, true);
        }
    },
    tickTimer: function(timer) {
        var time = timer.endTime - (Game.getLocalTime() + Game.timeDiff);
        if (time < 0) {
            document.location.reload();
            Game.formatTime(timer.element, 0, false);
            return true;
        }
        Game.formatTime(timer.element, time, false);
        return false;
    },
    tick: function() {
        Game.tickTime();
        currTime = ++Game.serverTimesnow;
        for (var i = 0; i < Game.prograssBars.length; ++i) {
            progressbar = Game.prograssBars[i];
            start = parseInt(progressbar.getAttribute('start'));
            end = parseInt(progressbar.getAttribute('end'));
            maxwidth = parseInt(progressbar.getAttribute('maxwidth'));
            plus = end - start;
            plug = currTime - start;
            porcent = Math.ceil((plug * 100) / plus);
            currWidth = Math.max(0, Math.min(maxwidth, Math.ceil((currTime - start) / (end - start) * maxwidth)));
            if (porcent < 100) {
                progressbar.innerHTML = porcent + '%';
                progressbar.style.width = porcent + '%';
            }
        }
        for (var timer = 0; timer < Game.timers.length; ++timer) {
            var remove = Game.tickTimer(Game.timers[timer]);
            if (remove) Game.timers.splice(timer, 1);
        }
    },
    selectAll: function(form, checked) {
        for (var i = 0; i < form.length; ++i) form.elements[i].checked = checked;
    },
    resizeIGMField: function(type) {
        field = $('[name=message]')[0];
        old_size = parseInt(field.getAttribute('rows'), 10);
        if (type == 'bigger') {
            field.setAttribute('rows', old_size + 3)
        } else if (type == 'smaller') {
            if (old_size >= 4) field.setAttribute('rows', old_size - 3)
        }
    },
    textCounter: function(field, countfield, charlimit) {
        if (field.val().length > charlimit) field.val(field.val().substring(0, charlimit));
        try {
            $("#" + countfield).html(field.val().length + '/' + charlimit);
        } catch (e) {}
    },
    autoresizeTextarea: function(selector, max_rows) {
        var textarea = $(selector);
        if (!textarea.length) return;
        max_rows = max_rows || 40;
        var current_rows = textarea[0].rows;
        textarea.keydown(function() {
            var rows = this.value.split('\n');
            var row_count = rows.length;
            for (var x = 0; x < rows.length; ++x) {
                if (rows[x].length >= textarea[0].cols) row_count += Math.floor(rows[x].length / textarea[0].cols);
            }
            row_count += 2;
            row_count = Math.min(row_count, max_rows);
            if (row_count > current_rows) this.rows = row_count
        });
    },
    buyPack: function(id, type) {
        $.colorbox({
            href: './ajax.php?act=get_pack&id=' + id + '&type=' + type,
            width: 646,
            height: 220
        });
    },
    changeSound: function(element) {
        $.ajax({
            url: './ajax.php?act=change-sound',
            type: "POST",
            success: function() {
                if ($(element).data('sound') != 1) $(element).removeClass('sound-off').addClass('sound-on').data('sound', '1').attr('title', 'Som ligado');
                else $(element).removeClass('sound-on').addClass('sound-off').data('sound', '0').attr('title', 'Som desligado');
                setTimeout(function() {
                    Tipped.refresh(".sound-off, .sound-on");
                }, 2000);
                console.log($(element));
            }
        });
        return false;
    },
    getDailyBonus: function(element) {
        $.ajax({
            url: './ajax.php?act=daily-bonus',
            type: "POST",
            success: function(response) {
                var message = response.split(' | ');
                if (message[0] == 'error') Notify.ErrorMessage(message[1]);
                else if (message[0] == 'information') {
                    Notify.ErrorMessage(message[1]);
                    $(element).slideUp();
                    clearInterval(daily_bonus);
                } else {
                    $(element).slideUp();
                    Notify.SuccessMessage(message[1]);
                    clearInterval(daily_bonus);
                }
            }
        });
        return false;
    },
    catchLoot: function(element) {
        $.ajax({
            url: './ajax.php?act=poke-loot',
            type: "POST",
            success: function(response) {
                var message = response.split(' | ');
                if (message[1] == undefined) message[1] = 'O presente escapou! :(';
                
                if (message[0] == 'error') Notify.ErrorMessage(message[1], 5000);
                else if (message[0] == 'information') {
                    Notify.ErrorMessage(message[1]);
                    $(element).hide(300, function () {
                        $(element).remove();
                    });
                } else {
                    $(element).hide(300, function () {
                        $(element).remove();
                    });
                    Notify.SuccessMessage(message[1]);
                }
            }
        });
        return false;
    }
}

Array.prototype.maxIndex = function () {
    let max = 0;

    for (let i = 0; i < this.length; i++) {
        if (this[max] < this[i]) {
            max = i;
        }
    }

    return max;
}

Array.prototype.rand = function () {
    return this[Math.floor(Math.random() * this.length)];
}

function wlSound (sound, volume, loop = true) {
    if (volume > 0) {
        var sound = new Howl({
              src: ['public/sounds/'+sound+'.mp3'],
              autoplay: true,
              loop: loop,
              volume: (volume/100),
              html5: true,
              onloaderror: function (msg) {
                  //a
              }
        });
    }
}

$(document).ready(function(e) {
    Game.startTimer();
    $('input[type=checkbox]#select_all').click(function(event) {
        if (this.checked) $('.checkbox').each(function() {
            this.checked = true;
        });
        else $('.checkbox').each(function() {
            this.checked = false;
        });
    });
    Tipped.create("*.tip_bottom-left", {
        hook: {
            target: 'bottommiddle',
            tooltip: 'topleft'
        },
        radius: 3
    });
    Tipped.create("*.tip_bottom-middle", {
        hook: {
            target: 'bottommiddle',
            tooltip: 'topmiddle'
        },
        radius: 3
    });
    Tipped.create("*.tip_bottom-right", {
        hook: {
            target: 'bottommiddle',
            tooltip: 'topright'
        },
        radius: 3
    });
    Tipped.create("*.tip_top-left", {
        hook: {
            target: 'topmiddle',
            tooltip: 'bottomleft'
        },
        radius: 3
    });
    Tipped.create("*.tip_top-middle", {
        hook: {
            target: 'topmiddle',
            tooltip: 'bottommiddle'
        },
        radius: 3
    });
    Tipped.create("*.tip_top-right", {
        hook: {
            target: 'topmiddle',
            tooltip: 'bottomright'
        },
        radius: 3
    });
    Tipped.create("*.tip_left-top", {
        hook: {
            target: 'leftmiddle',
            tooltip: 'righttop'
        },
        radius: 3
    });
    Tipped.create("*.tip_left-middle", {
        hook: {
            target: 'leftmiddle',
            tooltip: 'rightmiddle'
        },
        radius: 3
    });
    Tipped.create("*.tip_left-bottom", {
        hook: {
            target: 'leftmiddle',
            tooltip: 'rightbottom'
        },
        radius: 3
    });
    Tipped.create("*.tip_right-top", {
        hook: {
            target: 'rightmiddle',
            tooltip: 'lefttop'
        },
        radius: 3
    });
    Tipped.create("*.tip_right-middle", {
        hook: {
            target: 'rightmiddle',
            tooltip: 'leftmiddle'
        },
        radius: 3
    });
    Tipped.create("*.tip_right-bottom", {
        hook: {
            target: 'rightmiddle',
            tooltip: 'leftbottom'
        },
        radius: 3
    });
    Tipped.show('*.tip_show');
});