// URL variables
var visitedUrls = [],
    pendingUrls = [],
    addedUrls = [];

var allowedContentTypes = [
    /text/i,
    /html/i,
    /xml/i,
    /javascript/i,
    /json/i
];

var timeout = 30000;

// Create instances
var casper = require('casper').create(
    {
        pageSettings: {
            loadImages: false,
            loadPlugins: false
        },
        exitOnError: false,
        timeout: null,
        stepTimeout: null,
        // waitTimeout: timeout,
        // verbose: true,
        // logLevel: 'debug',
        onError: function (msg, backtrace) {
            say("ERROR!!! -> " + msg);
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

                if (allowed) {
                    say('Resource allowed: contentType = ' + contentType + ", url = " + response.url);
                } else {
                    response.abort();
                    say('Resource is not allowed: contentType = ' + contentType + ", url = " + response.url + ", aborting.");
                }

            }

        }
    });

/**
 * Requirements
 */
var system = require('system');
var utils = require('utils');
var spawn = require("child_process").spawn;

/**
 * System arguments
 */
var startUrl = casper.cli.get('url');
var allowedDomains = casper.cli.get('domains');
var width = casper.cli.get('width');
var height = casper.cli.get('height');

/**
 * Default values
 */
var allowedDomainsDefault = /.*/;
var widthDefault = 1980;
var heightDefault = 1280;

/**
 * Set defaults
 */
if (!allowedDomains) {
    allowedDomains = allowedDomainsDefault;
} else {
    allowedDomains = new RegExp(allowedDomains, 'i');
}

if (!width) {
    width = widthDefault;
}

if (!height) {
    height = heightDefault;
}

/**
 * Diplay run settings
 */
say("Start url = " + startUrl);
say("Allowed domains regex = " + allowedDomains);
say("Width = " + width + "px");
say("Height = " + height + "px");

// Start spidering
casper.start(startUrl, function () {
    spider(startUrl);
});

// Start the run
casper.run();

function processUrl(url) {
    if (addedUrls.indexOf(url) == -1) {
        _processUrl(url);
        addedUrls.push(url);
    }
}

function _processUrl(url) {

    say("URL=[" + url + ']');

}

function say(string) {
    console.log(string);
}

function dd(variable) {
    say(JSON.stringify(variable, null, 4));
}

function getHost(url) {

    var host = url.replace(/^[^:]+:\/+/, '');

    var firstSlashPosition = host.indexOf('/');

    if (firstSlashPosition != -1) {
        host = host.substr(0, firstSlashPosition);
    }

    say("Host=" + host + ", url=" + url);

    return host;
}

function isAllowedDomain(url) {

    var allowed = allowedDomains.test(getHost(url));

    if (allowed) {
        say('Allowed domain -> ' + url);
    } else {
        say('Not allowed domain -> ' + url);
    }

    return allowed;

}

// Spider from the given URL
function spider(url) {

    // Add the URL to the visited stack
    visitedUrls.push(url);

    casper.options.viewportSize = {
        width: width,
        height: height
    };

    casper.open(url)
        .then(function () {

            var status = this.status().currentHTTPStatus;

            if (status == 200) {

                say('Page open successfully: ' + url);

                var hrefs = this.evaluate(function () {

                    var getAbsoluteUrl = (function () {

                        var a;

                        return function (url) {
                            if (!a) a = document.createElement('a');
                            a.href = url;

                            return a.href;
                        };

                    })();

                    var hrefs = [];

                    Array.prototype.forEach.call(__utils__.findAll('a'), function (e) {

                        var absolute = getAbsoluteUrl(e.getAttribute('href'));

                        if (/(ht|f)tp(s)?:\/+/.test(absolute)) {
                            hrefs.push(absolute);
                        }

                    });

                    return hrefs;

                });

                Array.prototype.forEach.call(hrefs, function (link) {


                    if (pendingUrls.indexOf(link) == -1 && visitedUrls.indexOf(link) == -1) {
                        if (isAllowedDomain(link)) {
                            pendingUrls.push(link);
                            say('Pushed to queue ' + link);
                        } else {
                            say('Not pushed to queue, because domain is not allowed: ' + link);
                        }
                    } else {
                        say('Not pushed to queue, because already processed: ' + link);
                    }

                    processUrl(link);

                });

                if (pendingUrls.length > 0) {
                    var nextUrl = pendingUrls.shift();
                    spider(nextUrl);
                }

            }
            else {
                say('Can\'t open url: ' + url);
            }


        });

}

