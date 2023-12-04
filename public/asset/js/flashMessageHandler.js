// Fonction pour gérer les requêtes AJAX
function handleAjaxRequest(url, options, onSuccess, onError) {
  // Configure les en-têtes pour les requêtes AJAX
  let fetchOptions = {
    ...options,
    headers: {
      ...options.headers,
      "X-Requested-With": "XMLHttpRequest",
    },
  };

  // Effectue une requête AJAX avec l'URL et les options fournies
  fetch(url, fetchOptions)
    .then(function (response) {
      // Vérifie si la réponse du serveur est valide
      if (!response.ok) {
        throw new Error("Réponse du réseau invalide");
      }
      // Convertit la réponse en JSON
      return response.json();
    })
    .then(function (data) {
      // Affiche un message flash en fonction de la réponse
      if (data.success) {
        displayFlashMessage(data.message, true);
      } else {
        displayFlashMessage(data.message, false);
      }
      // Appelle la fonction onSuccess si fournie, avec les données de la réponse
      if (onSuccess) {
        onSuccess(data);
      }
    })
    .catch(function (error) {
      // Affiche un message flash d'erreur en cas d'erreur
      displayFlashMessage(error.message, false);
      // Appelle la fonction onError si fournie, avec l'erreur
      if (onError) {
        onError(error);
      }
    });
}

// Fonction pour mettre à jour le nombre d'articles dans le panier
function updateCartItemCount(change) {
  var cartItemCountElement = document.querySelector(".cart-item-count");
  if (cartItemCountElement) {
    // Obtient le nombre actuel d'articles dans le panier depuis l'élément HTML
    var currentCount = parseInt(cartItemCountElement.textContent) || 0;
    // Calcule le nouveau nombre d'articles en ajoutant le changement, en évitant le négatif
    var newCount = Math.max(currentCount + change, 0);
    // Met à jour l'élément HTML avec le nouveau nombre (ou vide s'il est zéro)
    cartItemCountElement.textContent = newCount > 0 ? newCount : "";
  }
}

// Fonction pour afficher un message flash
function displayFlashMessage(message, isSuccess) {
  // Détermine la classe CSS du message en fonction de son succès ou de son échec
  var alertClass = isSuccess ? "alert-success" : "alert-danger error";
  // Crée un élément div pour le message flash avec la classe appropriée
  var alertHtml = document.createElement("div");
  alertHtml.className = "alert " + alertClass;
  // Ajoute le texte du message à l'élément
  alertHtml.textContent = message;

  // Obtient le conteneur des messages flash dans le document
  var flashMessagesContainer = document.querySelector(
    ".flash-messages-container"
  );
  // Ajoute le message flash au conteneur
  flashMessagesContainer.appendChild(alertHtml);
  // Affiche le conteneur lorsqu'un nouveau message est ajouté
  flashMessagesContainer.style.display = "flex";

  // Définit un minuteur pour le fondu du message
  setTimeout(function () {
    alertHtml.style.opacity = "0"; // Déclenche la disparition en fondu
    setTimeout(function () {
      alertHtml.remove();
      // Cache le conteneur s'il n'y a plus de messages
      if (!flashMessagesContainer.hasChildNodes()) {
        flashMessagesContainer.style.display = "none";
      }
    }, 2000); // Temps nécessaire pour que l'effet de fondu se termine
  }, 15000); // Durée d'affichage du message flash (15 secondes)
}

// Attend que le document soit complètement chargé avant d'exécuter le code
document.addEventListener("DOMContentLoaded", function () {
  var flashMessagesContainer = document.querySelector(
    ".flash-messages-container"
  );
  // Cache le conteneur s'il n'y a pas de messages initialement
  if (!flashMessagesContainer.hasChildNodes()) {
    flashMessagesContainer.style.display = "none";
  }
});