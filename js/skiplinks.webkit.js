(function (linkList, i, URI) {
    if (!!(URI = document.documentURI)) {
        URI = URI.split('#')[0];
        document.addEventListener("DOMContentLoaded", function () {
            document.removeEventListener("DOMContentLoaded", arguments.callee, false);
            linkList = document.links;
            for (i in linkList) {
                if (!!linkList[i].hash) {
                    if (linkList[i].hash.match(/^#./)) {
                        if ((URI + linkList[i].hash) == linkList[i].href) {
                            linkList[i].addEventListener("click", function (e, f, g) {
                                f = document.getElementById(this.hash.slice(1));
                                if (!(g = f.getAttribute('tabIndex'))) f.setAttribute('tabIndex', -1);
                                f.focus();
                                if (!g) f.removeAttribute('tabIndex');
                            }, false);
                        }
                    }
                }
            }
        }, false);
    }
    return true;
})();