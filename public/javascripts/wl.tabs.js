$('[data-tabs-wl]').hide();

function tab_wl (obj) {
    $('[data-tabs-wl]').hide();
    $(obj).show();
}

let first = document.querySelectorAll('[data-tabs-wl]')[0];
if ( typeof(first) != 'undefined' ) {
    first.style.display = 'block';
}