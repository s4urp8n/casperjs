var system = require('system');
var args = system.args;
var url = args[1];
var width = args[2];
var height = args[3];
var userAgent = args[4];

var page = require('webpage').create();

page.viewportSize = {
    width: width,
    height: height
};

page.settings.userAgent = userAgent;

function onPageReady() {

    var htmlContent = page.evaluate(function () {
        return document.documentElement.outerHTML;
    });

    console.log(htmlContent);

    phantom.exit();
}

page.open(url, function (status) {
    function checkReadyState() {
        setTimeout(function () {
            var readyState = page.evaluate(function () {
                return document.readyState;
            });

            if ("complete" === readyState) {
                onPageReady();
            } else {
                checkReadyState();
            }
        });
    }

    checkReadyState();
});