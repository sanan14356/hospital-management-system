if (!window.hmsAppLoaded) {
    var script = document.createElement("script");
    script.src = "js/app.js";
    script.defer = true;
    document.head.appendChild(script);
}