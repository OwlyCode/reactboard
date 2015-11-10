var Github = {
    enabled: false,
    fetch: function() {
        if (!Github.enabled) {
            return;
        }

        $.get('/github/feed', {}, function(prs) {
            console.log(prs);
            $('#app.github ul.prs').html('');

            for (var i in prs) {
                $state = '<span>[' + prs[i].status.state + ']</span>';
                $img = '<img style="width: 64px" src="' + prs[i].user.avatar_url + '"/>';
                $li = $('<li>' + prs[i].title + '(' + prs[i].user.login + ')' + '</li>');

                $li.prepend($state);
                $li.prepend($img);
                $('#app.github ul.prs').append($li);
            }


            setTimeout(function() {
                Github.fetch();
            }, 30000);
        });
    }
}

$('#app').on('github.index.activate', function() {
    Github.enabled = true;
    Github.fetch();
});

$('#app').on('github.index.deactivate', function() {
    Github.enabled = false;
});
