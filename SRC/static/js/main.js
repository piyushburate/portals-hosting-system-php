let btnLoad = (btn, bool) => {
    $(btn).attr("disabled", bool);
    $(btn).toggleClass("loading-btn", bool);
}

var copyText = (txt) => {
    var copy = $('<input>').val(txt);
    $("body").append(copy);
    copy.select();
    document.execCommand("copy");
    document.activeElement.blur();
    copy.remove()
}

var pushLink = (link) => {
    history.pushState(null, null, link);
}

var goTo = (link) => {
    location.href = link;
}

window.onpopstate = () => { location.reload(); }

var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
});

var makeRequest = async (url) => {
    var p = await fetch(url);
    var response = await p.text();
    return response;
}

var statusColors, portals;

document.addEventListener("DOMContentLoaded", async () => {
    statusColors = JSON.parse(await makeRequest("/static/json/status-colors.json"));
    portals = JSON.parse(await makeRequest("/static/json/portals.json"));
})
function userStatus(portal_type, user_status) {
    return portals[portal_type]["user_status"][user_status][0];
}
function userStatusColor(portal_type, user_status) {
    return statusColors[portals[portal_type]["user_status"][user_status][1]];
}