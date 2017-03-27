var system = require('system');

var startUrl = system.args[4];

var visitedUrls = [],
    pendingUrls = [];

var casper = require('casper').create({
    verbose: true,
    logLevel: 'debug'
});

var utils = require('utils');
var helpers = require('./helpers');

function getHost(url) {

    var getLocation = function (href) {
        var l = document.createElement("a");
        l.href = href;
        return l;
    };

    return getLocation(url).hostname;
}

function processUrl(url, content) {
    casper.echo(url);
}

function removeTrailingSlash(url) {
    return url.replace(/\/+$/, '');
}

function spider(url) {

    url = removeTrailingSlash(url);

    visitedUrls.push(url);

    casper.open(url).then(function () {

        processUrl(url, this.getHTML());

        var links = this.evaluate(function () {
            var links = [];
            Array.prototype.forEach.call(__utils__.findAll('a'), function (e) {
                links.push(e.getAttribute('href'));
            });
            return links;
        });

        var baseUrl = this.getGlobal('location').origin;

        for (var i in links) {

            links[i] = helpers.absoluteUri(baseUrl, links[i]);
            links[i] = removeTrailingSlash(links[i]);


            if (pendingUrls.indexOf(links[i]) == -1 && visitedUrls.indexOf(links[i]) == -1) {

                var startHost = getHost(startUrl);
                var currentHost = getHost(links[i]);

                if (startHost == currentHost) {
                    pendingUrls.push(links[i]);
                }

            }

        }

        if (pendingUrls.length > 0) {
            var nextUrl = pendingUrls.shift();
            spider(nextUrl);
        } else {
            this.exit();
        }

    });

}

casper.start(startUrl, function () {
    spider(startUrl);
});

casper.run();