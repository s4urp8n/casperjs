var casper = require('casper').create({});
var system = require('system');

casper
    .start('https://google.com/')
    .run(function () {
        this.echo(system.args[4]);
        this.exit();
    });