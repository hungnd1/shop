$(window).load (function(){
    $('#tp_hidden_form_register').hide();
    $('#bt_tp_hide').hide();
});

$(document).ready(function(){
    $('#bt_tp_show').click(function(){
        $('#bt_tp_show').hide();
        $('#form_login').hide();
        $('#tp_hidden_form_register').show('slow');
        $('#bt_tp_hide').show('slow');
    });
    $('#bt_tp_hide').click(function(){
        $('#bt_tp_show').show();
        $('#form_login').show('slow');
        $('#tp_hidden_form_register').hide();
        $('#bt_tp_hide').hide();
    });
});