function insert_user_id_from_dropdown() {
    if (document.getElementById('widget_ignore_user').textContent ==
        'Add ignored user IDs in here.') {
        document.getElementById('widget_ignore_user').textContent = '';
    }
    document.getElementById('widget_ignore_user').value =
    document.getElementById('widget_ignore_user').value + '\n' +
    document.lbakut_widget_form.user.value;
}

function lbakut_show(id) {
    document.getElementById(id).style.display = 'block';
}

function lbakut_hide(id) {
    document.getElementById(id).style.display = 'none';
}


jQuery(document).ready(function(){
    jQuery('.ip_address[title]').tooltip({'position': 'center right', 'tipClass': 'lbakut_tooltip'});
    jQuery('.script_name[title]').tooltip({'position': 'center left', 'tipClass': 'lbakut_tooltip'});
    jQuery('.browser[title]').tooltip({'position': 'center right', 'tipClass': 'lbakut_tooltip'});
})