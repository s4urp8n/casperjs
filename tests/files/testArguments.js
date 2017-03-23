var casper = require('casper').create({});
var system = require('system');

casper
    .start('https://google.com/')
    .run(function () {
        this.echo(system.args[4]);
        this.echo(system.args[5]);
        this.echo(system.args[6]);
        this.echo(system.args[7]);
        this.echo(system.args[8]);
        this.exit();
    });