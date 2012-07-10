
function greeting() {
    var now = new Date();
    var hrs = now.getHours();
    var msg = "";

    if (hrs >  0) msg = "Mornin' Sunshine!"; // REALLY early
    if (hrs >  6) msg = "Good Morning";      // After 6am
    if (hrs > 12) msg = "Good Afternoon";    // After 12pm
    if (hrs > 17) msg = "Good Evening";      // After 5pm
    if (hrs > 22) msg = "Go to bed!";        // After 10pm
    return msg;
}


function post_to_url(path, params, method) {
    method = method || "post"; // Set method to post by default, if not specified.

    // The rest of this code assumes you are not using a library.
    // It can be made less wordy if you use one.
    var form = document.createElement("form");
    form.setAttribute("method", method);
    form.setAttribute("action", path);

    for(var key in params) {
        if(params.hasOwnProperty(key)) {
            var hiddenField = document.createElement("input");
            hiddenField.setAttribute("type", "hidden");
            hiddenField.setAttribute("name", key);
            hiddenField.setAttribute("value", params[key]);

            form.appendChild(hiddenField);
         }
    }

    document.body.appendChild(form);
    form.submit();
}

function command(c) {
    post_to_url("/dakhila/index.php", { 'command': c }, "post");
}

function deleteme(objname, id) {
    post_to_url("/dakhila/index.php", { 'command': 'deleteobj', 'objName':objname, 'objId':id }, "post");
}

function familyFeeCheck(id) {
    post_to_url("/dakhila/index.php", { 'command': 'FamilyFeeCheck', 'ID':id }, "post");
}

