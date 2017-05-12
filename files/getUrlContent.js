var system = require('system');
var args = system.args;
var link = args[4];
var width = args[5];
var height = args[6];
var userAgent = args[7];

var utils = require('utils');
var casper = require('casper').create({
    pageSettings: {
        loadImages: false,
        loadPlugins: false
    },
    userAgent: userAgent,
    onError: function (msg, backtrace) {

    },
    onResourceReceived: function (casper, response) {

        var contentType = response['contentType'];
        var stage = response['stage'];

        if (contentType && stage == 'start') {

            var allowed = false;

            for (var i in allowedContentTypes) {

                if (allowedContentTypes[i].test(contentType)) {
                    allowed = true;
                    break;
                }
            }

            if (!allowed) {
                response.abort();
            }

        }

    }
});

var allowedContentTypes = [
    /text/i,
    /html/i,
    /xml/i,
    /javascript/i,
    /json/i
];

casper.options.exitOnError = false;
casper.options.viewportSize = {
    width: width,
    height: height
};

casper.start(link)
    .thenEvaluate(function () {

        var getAbsoluteUrl = (function () {

            var a;

            return function (url) {
                if (!a) a = document.createElement('a');
                a.href = url;

                return a.href;
            };

        })();

        var getAbsoluteSrc = (function () {

            var img;

            return function (src) {
                if (!img) img = document.createElement('img');
                img.src = src;

                return img.src;
            };

        })();

        Array.prototype.forEach.call(__utils__.findAll('a[href]'), function (e) {
            e.setAttribute('href', getAbsoluteUrl(e.getAttribute('href')));
        });

        Array.prototype.forEach.call(__utils__.findAll('img[src]'), function (e) {
            e.setAttribute('src', getAbsoluteSrc(e.getAttribute('src')));
        });

    })
    .run(function () {
        this.echo(this.getHTML());
        this.exit();
    });