function insert_user_id_from_dropdown() {
    if (document.getElementById('widget_ignore_user').textContent ==
        'Add ignored user IDs in here.') {
        document.getElementById('widget_ignore_user').textContent = '';
    }
    document.getElementById('widget_ignore_user').value =
        document.getElementById('widget_ignore_user').value + '\n' +
        document.lbakut_widget_form.user.value;
}