$(function(){
    var app = $('#app').data('app');
    var module = $('#app').data('module');

    $('#app').load('/' + app + '/'+ module);
    $('#app').addClass(app);
    var ws = $.websocket("ws://" + document.domain + ":" + window.location.port + "/ws", {
        open: function() {
            console.log('Connexion established.');
        },
        close: function() {
            console.log('Connexion closed.');
        },
        events: {
            switch: function(msg){
                console.log('Attempting to switch to ' + msg.url);
                $('#app').load(msg.url, function(response, status){
                    if(status == 'error') {
                        console.log('Failed to switch to ' + msg.url);
                    } else {
                        $('#app').removeClass().addClass(msg.url.split('/')[1]);
                        console.log('Switched to ' + msg.url);
                    }
                });
            }
        }
    });
});
