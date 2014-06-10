$(function(){
    var app = $('#app').data('app');
    var module = $('#app').data('module');

    $('#app').load('/' + app + '/'+ module);
    $('#app').addClass(app).trigger(app + '.' + module + '.activate');
    var ws = $.websocket("ws://" + document.domain + ":" + window.location.port + "/ws", {
        open: function() {
            console.log('Connexion established.');
        },
        close: function() {
            console.log('Connexion closed.');
        },
        events: {
            switch: function(msg){
                var tokens = msg.url.split('/');
                var app = tokens[1];
                var module = tokens[2];
                console.log('Attempting to switch to ' + msg.url);
                $('#app').load(msg.url, function(response, status){
                    if(status == 'error') {
                        console.log('Failed to switch to ' + msg.url);
                    } else {
                        $('#app').trigger($('#app').data('app') + '.' + $('#app').data('module') + '.deactivate');
                        $('#app').removeClass($('#app').data('app')).addClass(msg.url.split('/')[1]);
                        $('#app').data('app', app).data('module', module);
                        $('#app').trigger(app + '.' + module + '.activate');
                        console.log('Switched to ' + msg.url);
                    }
                });
            }
        }
    });
});
