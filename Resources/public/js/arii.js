/*
Fonctions transverses
Solutions Open Source Paris SAS
*/

function FormatTime(currentTime) {
    var hours = currentTime.getHours();
    var minutes = currentTime.getMinutes();
    var seconds = currentTime.getSeconds();
    if (minutes < 10){
    minutes = "0" + minutes;
    }
    if (seconds < 10){
    seconds = "0" + seconds;
    }
return hours + ":" + minutes + ":" +  seconds;
}

