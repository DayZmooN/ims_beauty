function handleAjaxRequest(url, options, onSuccess, onError) {
    // Ensure headers are set for AJAX requests
    let fetchOptions = {
        ...options,
        headers: {
            ...options.headers,
            'X-Requested-With': 'XMLHttpRequest'
        }
    };

    fetch(url, fetchOptions)
        .then(function(response) {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(function(data) {
            if (data.success) {
                displayFlashMessage(data.message, true);
            } else {
                displayFlashMessage(data.message, false);
            }
            if (onSuccess) {
                onSuccess(data);
            }
        })
        .catch(function(error) {
            displayFlashMessage(error.message, false);
            if (onError) {
                onError(error);
            }
        });
}

    function updateCartItemCount(change) {
        var cartItemCountElement = document.querySelector('.cart-item-count');
        if (cartItemCountElement) {
            var currentCount = parseInt(cartItemCountElement.textContent) || 0;
            var newCount = Math.max(currentCount + change, 0); // Ensure count doesn't go negative
            cartItemCountElement.textContent = newCount > 0 ? newCount : '';
        }
    }

    function displayFlashMessage(message, isSuccess) {
        var alertClass = isSuccess ? 'alert-success' : 'alert-danger';
        var alertHtml = document.createElement('div');
        alertHtml.className = 'alert ' + alertClass;
        alertHtml.textContent = message;

        document.querySelector('.flash-messages-container').appendChild(alertHtml);
        setTimeout(function() {
            alertHtml.style.opacity = '0'; // Trigger fade out
            setTimeout(function() {
                alertHtml.remove();
            }, 2000); // Time for fade-out effect to complete
        }, 15000);
    }
