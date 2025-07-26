document.getElementById('serie').onchange = function() {
    var input_new_serie_name = document.getElementById("new_serie_name");
    var input_new_serie_path = document.getElementById("new_serie_path");
    if(document.getElementById('serie').value != 'new_serie') {
        input_new_serie_name.style.display = 'none';
        input_new_serie_name.required = false
        input_new_serie_path.style.display = 'none';
        input_new_serie_path.required = false
    } else {
        input_new_serie_name.style.display = 'inline';
        input_new_serie_name.required = true;
        input_new_serie_path.style.display = 'inline';
        input_new_serie_path.required = true;
    }
};