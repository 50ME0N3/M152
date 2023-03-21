async function reachUploadFunction() {
    let description = document.getElementById('idDescriptionPost').value;
    let file = document.getElementById('idFile').files;

    tempData = {};

    tempData.description = description;
    tempData.file = file;

    data = new FormData();

    data.append('description', description);
    for (let i = 0; i < file.length; i++) {
        data.append('file[' + i + ']', file[i]);
    }

    for (const value of data.values()) {
        console.log(value);
    }


    let xhr = new XMLHttpRequest();
    xhr.open('POST', 'controllers/post_controller.php?action=UploadWithAjax', true);
    xhr.send(data);

    xhr.onload = function () {
        console.log(this.responseText);
        //location.href = "index.php?uc=home";

    };

    xhr.onerror = function () {
        alert(`Network Error`);
    };

}