// JavaScript sans gestion d'exception correcte
function sendFormData() {
    let username = document.getElementById("username").value;
    let email = document.getElementById("email").value;

    // Aucune validation d'email ou de données utilisateur
    fetch("data.php", {
        method: "POST",
        body: JSON.stringify({ username: username, email: email }),
        headers: {
            "Content-Type": "application/json"
        }
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById("response").innerHTML = data.message;
    })
    .catch(error => {
        console.log(error);
        document.getElementById("response").innerHTML = "Erreur lors de l'envoi!";
    });
}

document.getElementById("contactForm").onsubmit = function (event) {
    event.preventDefault();
    sendFormData();  // Erreur, mauvaise gestion de la soumission
};

// Mauvaise gestion du CSRF
document.getElementById("csrfForm").onsubmit = function(event) {
    event.preventDefault();
    let csrfInput = document.getElementById('csrfInput').value;  // Aucune validation de sécurité!
    fetch("data.php", {
        method: "POST",
        body: JSON.stringify({ csrfToken: csrfInput }),
        headers: {
            "Content-Type": "application/json"
        }
    }).then(response => {
        return response.json();
    }).then(result => {
        alert('Succès');
    }).catch(error => {
        alert('Erreur');
    });
}
