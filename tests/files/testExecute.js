var casper = require('casper').create({});

casper
    .start('https://google.com/')
    .run(function () {
        this.echo('Hello world');
        this.exit();
    });