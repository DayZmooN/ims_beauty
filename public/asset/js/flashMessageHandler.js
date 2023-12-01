function handleAjaxRequest(url, options, onSuccess, onError) {
  // Ensure headers are set for AJAX requests
  let fetchOptions = {
    ...options,
    headers: {
      ...options.headers,
      "X-Requested-With": "XMLHttpRequest",
    },
  };

  fetch(url, fetchOptions)
    .then(function (response) {
      if (!response.ok) {
        throw new Error("Network response was not ok");
      }
      return response.json();
    })
    .then(function (data) {
      if (data.success) {
        displayFlashMessage(data.message, true);
      } else {
        displayFlashMessage(data.message, false);
      }
      if (onSuccess) {
        onSuccess(data);
      }
    })
    .catch(function (error) {
      displayFlashMessage(error.message, false);
      if (onError) {
        onError(error);
      }
    });
}

function updateCartItemCount(change) {
  var cartItemCountElement = document.querySelector(".cart-item-count");
  if (cartItemCountElement) {
    var currentCount = parseInt(cartItemCountElement.textContent) || 0;
    var newCount = Math.max(currentCount + change, 0); // Ensure count doesn't go negative
    cartItemCountElement.textContent = newCount > 0 ? newCount : "";
  }
}

function displayFlashMessage(message, isSuccess) {
  var alertClass = isSuccess ? "alert-success" : "alert-danger error";
  var alertHtml = document.createElement("div");
  alertHtml.className = "alert " + alertClass;
  alertHtml.textContent = message;

  var flashMessagesContainer = document.querySelector(
    ".flash-messages-container"
  );
  flashMessagesContainer.appendChild(alertHtml);
  flashMessagesContainer.style.display = "flex"; // Show container when a new message is added

  setTimeout(function () {
    alertHtml.style.opacity = "0"; // Trigger fade out
    setTimeout(function () {
      alertHtml.remove();
      if (!flashMessagesContainer.hasChildNodes()) {
        flashMessagesContainer.style.display = "none"; // Hide container if no messages
      }
    }, 2000); // Time for fade-out effect to complete
  }, 15000);
}

document.addEventListener("DOMContentLoaded", function () {
  var flashMessagesContainer = document.querySelector(
    ".flash-messages-container"
  );
  if (!flashMessagesContainer.hasChildNodes()) {
    flashMessagesContainer.style.display = "none";
  }
});
