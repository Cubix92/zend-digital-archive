function Timeline(data, perPage) {
    this.data = data;
    this.perPage = perPage;
    this.actualPage = 0;
    this.nextEntries = function() {
        var entries = this.data.slice(this.actualPage, this.actualPage + this.perPage);
        this.actualPage = this.actualPage + this.perPage;
        return entries;
    };
    this.renderEntries = function() {
        var entries = this.nextEntries();
        entries.forEach(function(entry, index) {
            var isEven = (index % 2 === 1) ? 'class="timeline-inverted"' : '';
            var image = entry.image ? '<p><img src="'+ entry.image.substring(7) +'" alt="lorem pixel"></p>\n' : '';
            var tags = '';

            entry.tags.forEach(function(tag) {
                tags += '<a class="btn btn-blk" href="#">' + tag.name + '</a>\n';
            });

            var template =
                '    <li ' + isEven + '>\n' +
                '        <div class="tl-circ">' + entry.category.shortcut + '</div>\n' +
                '        <div class="timeline-panel">\n' +
                '            <div class="tl-heading">\n' +
                '                <h4>' + entry.title +
                '                    <a href="' + entry.url + '" target="_blank" class="youtube-link pull-right">\n' +
                '                        <img width="48" src="/img/youtube_social_icon_red.png">\n' +
                '                    </a>\n' +
                '                </h4>\n' +
                '                <p><small class="text-muted"><span class="glyphicon glyphicon-time"></span> ' +
                                    entry.date_published + '</small></p>\n' +
                '            </div>\n' +
                '            <div class="tl-body">\n' + image +
                '                <br><p>\n' + entry.content + '</p>' +
                '                <hr>\n' +
                '                <p class="text-left tags">' + tags + '</p>\n' +
                '            </div>\n' +
                '        </div>\n' +
                '    </li>';

            $("#timeline").append(template);
        });
    };
}

function timelineInit(data)
{
    var win = $(window);
    var doc = $(document);
    var timeline = new Timeline(data, 2);

    timeline.renderEntries();

    $(window).scroll(function() {
        if (doc.height() - win.height() <= Math.ceil(win.scrollTop())) {
            timeline.renderEntries();
        }
    });
}

$(document).ready(function() {
    $.get( "/api/notes", function(response) {
        timelineInit(response.data);
    });

    $('.js-tags a').click(function() {

        if ($(this).attr("disabled")) {
            $(this).attr("disabled", false);
            $(this).removeClass('checked');
        } else {
            $(this).attr("disabled", true);
            $(this).addClass('checked');
        }

        var tags = [];

        $('.js-tags a.checked').each(function(index, element) {
            tags.push('tags[]=' + $(element).data('id'));
        });

        $.get( "/api/notes?" + tags.join('&'), function(response) {
            $('#timeline').empty();
            timelineInit(response.data);
        });
    });
});