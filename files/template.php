var system = require('system');
var args = system.args;
var link = args[4];

var casper = require('casper').create({
    pageSettings: {
        loadImages: false,
        loadPlugins: false
    },
    clientScripts: ["jquery-1.9.1.min.js"],
    userAgent: 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.99 Safari/537.36'
});

casper.options.waitTimeout = 600000;
casper.options.exitOnError = true;
casper.options.viewportSize = {
    width: 1200,
    height: 768
};

casper.start(link)
    .then(function () {
        this.wait(7000);
    })
    .thenEvaluate(function () {
        var getAbsoluteUrl = (function () {

            var a;

            return function (url) {
                if (!a) {
                    a = document.createElement('a');
                }
                a.href = url;

                return a.href;
            };
        })();

        $('a[href]').each(function () {
            $(this).attr('href', getAbsoluteUrl($(this).attr('href')));
        });
    })
    .run(function () {
        this.echo(this.getHTML());
        this.exit();
    });