const darkcheck = document.getElementById('darkmode');
const themeButton = document.getElementById('themeButton');

// Se o checkbox estiver marcado muda o theme do site
if (darkcheck.checked) { 
    console.log("clicked");
    setTimeout(() => {
        var element = document.body;
        element.dataset.bsTheme =
            element.dataset.bsTheme == "dark" ? "light" : "dark";


        const sensores = document.getElementsByClassName("sensor");
        Array.prototype.forEach.call(sensores, function (sensorArray) {
            sensorArray.classList.toggle('dark');
        });

        const atuadores = document.getElementsByClassName("atuador");
        Array.prototype.forEach.call(atuadores, function (atuadorArray) {
            atuadorArray.classList.toggle('dark');
        });

        const imgDark = document.querySelectorAll('.darkImgs');
        imgDark.forEach(function(imgDarkArray) {
            imgDarkArray.classList.toggle('dark');
        });

        themeButton.innerHTML = '<img src="assets/imgs/sun.png" alt="Dark Mode" class="btn-img">'
    }, 50);
}

