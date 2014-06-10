var Twitter = {
    enabled: false,
    sinceId: 0,
    fetch: function() {
        if (!Twitter.enabled) {
            return;
        }

        $.get('/twitter/feed', { sinceId: Twitter.sinceId }, function(statuses) {

            if (statuses.length > 0) {
                Twitter.sinceId = statuses[0].id_str;
            }

            for (var i=statuses.length-1;i>=0;i--) {
                var date = new Date(statuses[i].created_at);

                $li = $('<li style="display: none"></li>');
                $img = $('<img alt="user picture"/>').attr('src', statuses[i].user.profile_image_url)
                $author = $('<span class="author"></span>').html(statuses[i].user.name);
                $date = $('<span class="date"></span>').html(date.toLocaleDateString(undefined, {month: '2-digit', day:'2-digit', year:'numeric'}) + ' ' + date.toLocaleTimeString());
                $content = $('<span class="content"></span>').html(statuses[i].text);

                $li.append($img).append($date).append($author).append($content).append($('<div class="clearfix"></div>'));
                $('#app.twitter ul').prepend($li);
            }

            $('#app.twitter ul li').each(function(index){
                if (index > 9) {
                    $(this).fadeOut(500, function(){
                        $(this).remove();
                    });
                }
            });

            setTimeout(function(){
                $('#app.twitter ul li').fadeIn(500);
            }, 500);

            setTimeout(function() {
                Twitter.fetch();
            }, 15000);
        });
    }
}

$('#app').on('twitter.index.activate', function() {
    Twitter.enabled = true;
    Twitter.fetch();
});

$('#app').on('twitter.index.deactivate', function() {
    Twitter.enabled = false;
    Twitter.sinceId = 0;
});
