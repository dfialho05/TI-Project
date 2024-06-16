// Função para enviar requisição HTTP para o Raspberry Pi
function botaoRasperiPi() { 
    var xhttp = new XMLHttpRequest();

    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var estado = document.getElementById("valorAtuador2").getAttribute("data-valor");
            console.log(estado);

            if (estado == "Ligado") {
                estado = "Desligado";
            } else {
                estado = "Ligado";
            }

            var xhttpPost = new XMLHttpRequest();
            xhttpPost.open("POST", "api/api.php", true);
            xhttpPost.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

            var now = new Date();
            var formattedDate = now.getFullYear() + '/' + ('0' + (now.getMonth() + 1)).slice(-2) + '/' + ('0' + now.getDate()).slice(-2) + ' ' + ('0' + now.getHours()).slice(-2) + ':' + ('0' + now.getMinutes()).slice(-2);

            xhttpPost.send('nome=atuadorNo2&valor=' + estado + '&hora=' + encodeURIComponent(formattedDate));
        }
    };

    xhttp.open("GET", "api/api.php?nome=atuadorNo2", true);
    xhttp.send();
}


document.addEventListener("DOMContentLoaded", function() {
    // Obtém o elemento botão
    var buttonPI = document.getElementById("btnAtuador2");

    // Adiciona um listener para o evento de clique
    buttonPI.addEventListener("click", function() {
        botaoRasperiPi(); // Chama a função botaoRasperiPi() quando o botão é clicado
    });
});

// Função para enviar requisição HTTP para o Arduino
function botaoArduino() {
    var xhttp = new XMLHttpRequest();

    // 
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var estado = document.getElementById("valorLedArduino2").getAttribute("data-valor");
            console.log(estado);

            if (estado == "Ligado") {
                estado = "Desligado";
            } else {
                estado = "Ligado";
            }

            var xhttpPost = new XMLHttpRequest();
            xhttpPost.open("POST", "api/api.php", true);
            xhttpPost.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

            var now = new Date();
            var formattedDate = now.getFullYear() + '/' + ('0' + (now.getMonth() + 1)).slice(-2) + '/' + ('0' + now.getDate()).slice(-2) + ' ' + ('0' + now.getHours()).slice(-2) + ':' + ('0' + now.getMinutes()).slice(-2);

            xhttpPost.send('nome=ledArduino2&valor=' + estado + '&hora=' + encodeURIComponent(formattedDate));

            // Atualiza o atributo data-valor após enviar o novo estado
            document.getElementById("valorLedArduino2").setAttribute("data-valor", estado);
        }
    };

    xhttp.open("GET", "api/api.php?nome=ledArduino2", true);
    xhttp.send();
}

document.addEventListener("DOMContentLoaded", function() {
    // Obtém o elemento botão
    var buttonARD = document.getElementById("btnLedArduino2");

    // Adiciona um listener para o evento de clique
    buttonARD.addEventListener("click", function() {
        botaoArduino(); // Chama a função botaoArduino() quando o botão é clicado
    });
});



