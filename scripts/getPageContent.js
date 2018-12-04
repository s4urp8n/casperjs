var system = require('system');
var args = system.args;
var url = args[1];
var width = args[2];
var height = args[3];
var userAgent = args[4];
var timeout = args[5];

var page = require('webpage').create();

page.open(url, function () {
    setTimeout(function () {
        console.log(page.content);
        phantom.exit();
    }, timeout);
});

