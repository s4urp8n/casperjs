var system = require('system');
var args = system.args;
var link = args[4];
var width = args[5];
var height = args[6];
var userAgent = args[7];

var casper = require('casper').create({
    pageSettings: {
        loadImages: false,
        loadPlugins: false
    },
    userAgent: userAgent
});

casper.options.waitTimeout = 600000;
casper.options.exitOnError = true;
casper.options.viewportSize = {
    width: width,
    height: height
};

casper.start(link)
    .run(function () {
        this.echo(this.getHTML());
        this.exit();
    });