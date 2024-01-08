document.addEventListener("DOMContentLoaded", function () {
  // Dropdown Menu Interactions
  handleDropdownMenus();

  // Services Detail Toggle on Soins Page
  handleServiceDetails();

  // Accordion Effect on Tarifs Page
  handleAccordionEffect();

  // Dashboard Section Toggle
  handleDashboardSections();

  // New function for User Dashboard Update
  handleUserDashboardUpdate();

  handleCartInteractions();

  // Handle Add to cart on Détails & Tarifs pages
  handleCartAddition();
});
// Cookie consent
document.addEventListener("DOMContentLoaded", function () {
  // Check if user has given consent for cookies
  var consentGiven = localStorage.getItem("cookieConsent");
  if (!consentGiven) {
    // If consent is not given, display the cookie consent container
    document.getElementById("cookieConsentContainer").style.display = "block";
  }

  // Event listener for accepting cookies
  document
    .getElementById("acceptCookies")
    .addEventListener("click", function () {
      // Set a flag in local storage to record the consent
      localStorage.setItem("cookieConsent", "true");
      // Hide the cookie consent container
      document.getElementById("cookieConsentContainer").style.display = "none";
    });

  // Event listener for declining cookies (optional)
  document
    .getElementById("declineCookies")
    .addEventListener("click", function () {
      // Hide the cookie consent container
      document.getElementById("cookieConsentContainer").style.display = "none";
      // Implement what happens when cookies are declined (optional)
    });
});

// Function to handle dropdown menus
function handleDropdownMenus() {
  const userIcon = document.querySelector(".user-icon");
  if (userIcon) {
    userIcon.addEventListener("click", function (e) {
      e.preventDefault();
      // Toggle the display of the next sibling element (dropdown menu)
      this.nextElementSibling.style.display =
        this.nextElementSibling.style.display === "block" ? "none" : "block";
    });
  }

  // Close dropdown menus when clicking outside
  window.addEventListener("click", function (e) {
    if (!e.target.matches(".user-icon, .user-icon *")) {
      document.querySelectorAll(".dropdown-content").forEach((dropdown) => {
        if (dropdown.style.display === "block") {
          dropdown.style.display = "none";
        }
      });
    }
  });
}

// Function to handle service details toggle
function handleServiceDetails() {
  const toggleServiceDetail = (serviceId) => {
    // Hide all service details
    document
      .querySelectorAll(".service-detail-content")
      .forEach((detail) => (detail.style.display = "none"));
    // Remove 'active' class from all service names
    document
      .querySelectorAll(".service-name")
      .forEach((serviceName) => serviceName.classList.remove("active"));

    // Show the selected service detail and mark its name as 'active'
    const detailToShow = document.getElementById("service-detail-" + serviceId);
    const serviceNameToShow = document.getElementById(
      "service-name-" + serviceId
    );
    if (detailToShow) detailToShow.style.display = "block";
    if (serviceNameToShow) serviceNameToShow.classList.add("active");
  };

  // Initially, set the first service as active
  const firstService = document.querySelector(".service-name");
  if (firstService) {
    firstService.classList.add("active");
    toggleServiceDetail(firstService.dataset.serviceId);

    // Add click event listeners to all service names
    document.querySelectorAll(".service-name").forEach((serviceName) => {
      serviceName.addEventListener("click", () =>
        toggleServiceDetail(serviceName.dataset.serviceId)
      );
    });
  }
}

// Function to handle accordion effect
function handleAccordionEffect() {
  document.querySelectorAll(".category-header").forEach((header) => {
    header.addEventListener("click", () => {
      const servicesDiv = document.getElementById(
        "services-" + header.getAttribute("data-category-id")
      );
      if (servicesDiv) {
        // Toggle the display of services (flex or none)
        servicesDiv.style.display =
          servicesDiv.style.display === "none" ||
          servicesDiv.style.display === ""
            ? "flex"
            : "none";
      }
    });
  });
}

// Function to handle dashboard section toggling
function handleDashboardSections() {
  const toggleSections = (showAppointments, showSettings) => {
    // Get elements related to sections
    const appointmentsSection = document.getElementById("my-appointments");
    const settingsSection = document.getElementById("settings");
    const showAppointmentsLink = document.getElementById("show-appointments");
    const showSettingsLink = document.getElementById("show-settings");

    if (
      appointmentsSection &&
      settingsSection &&
      showAppointmentsLink &&
      showSettingsLink
    ) {
      // Toggle the display of sections and set 'active' class on links
      appointmentsSection.style.display = showAppointments ? "flex" : "none";
      settingsSection.style.display = showSettings ? "flex" : "none";
      showAppointmentsLink.parentElement.classList.toggle(
        "active",
        showAppointments
      );
      showSettingsLink.parentElement.classList.toggle("active", showSettings);
    }
  };

  // Function to display section based on hash in URL
  const displaySectionBasedOnHash = () => {
    const hash = window.location.hash;
    if (hash === "#my-appointments") {
      toggleSections(true, false);
    } else if (hash === "#settings") {
      toggleSections(false, true);
    } else {
      toggleSections(true, false);
    }
  };

  // Event listeners for clicking section links
  const showAppointmentsLink = document.getElementById("show-appointments");
  const showSettingsLink = document.getElementById("show-settings");

  if (showAppointmentsLink) {
    showAppointmentsLink.addEventListener("click", function (e) {
      e.preventDefault();
      window.location.hash = "my-appointments";
    });
  }

  if (showSettingsLink) {
    showSettingsLink.addEventListener("click", function (e) {
      e.preventDefault();
      window.location.hash = "settings";
    });
  }

  // Display section based on hash when the page loads or hash changes
  displaySectionBasedOnHash();
  window.addEventListener("hashchange", displaySectionBasedOnHash);
}

// Function to handle user dashboard update
function handleUserDashboardUpdate() {
  var updateButton = document.getElementById("update-user-button");
  var userForm = document.getElementById("update-user-form");

  if (updateButton && userForm) {
    var updateUrl = userForm.getAttribute("data-update-url");

    // Event listener for update button click
    updateButton.addEventListener("click", function (e) {
      e.preventDefault();
      var formData = new FormData(userForm);

      // Send an AJAX request to update user data
      handleAjaxRequest(
        updateUrl,
        { method: "POST", body: formData },
        function (data) {
          // Reload the page regardless of the response
          window.location.reload();
        },
        function (error) {
          console.error("Update error:", error); // Log error if update fails
        }
      );
    });
  }
}

// Function to toggle calendar display
function toggleCalendar(serviceId) {
  const calendar = document.getElementById(`calendar-${serviceId}`);
  if (calendar) {
    // Toggle the display of the calendar (block or none)
    calendar.style.display =
      calendar.style.display === "none" ? "block" : "none";
  }
}

// Function to handle cart interactions
function handleCartInteractions() {
  // Toggle calendar display for services
  document.querySelectorAll(".open-calendar").forEach((button) => {
    button.addEventListener("click", (e) => {
      e.preventDefault();
      // Get the service ID associated with the clicked button
      const serviceId =
        e.currentTarget.closest(".service-item").dataset.serviceId;
      toggleCalendar(serviceId);
    });
  });
  // Remove items from cart
  document.querySelectorAll(".remove-from-cart-link").forEach((link) => {
    link.addEventListener("click", (e) => {
      e.preventDefault();
      const url = e.currentTarget.href;
      // Get the service ID associated with the clicked link
      const serviceId =
        e.currentTarget.closest(".service-item").dataset.serviceId;

      // Send an AJAX request to remove the item from the cart
      handleAjaxRequest(url, { method: "GET" }, (data) => {
        if (data.success) {
          // Remove the corresponding service item from the cart
          document
            .querySelector(`.service-item[data-service-id="${serviceId}"]`)
            .remove();
          // Update the cart item count
          updateCartItemCount(-1);
        }
      });
    });
  });
}

// Function to handle adding items to cart
function handleCartAddition() {
  document.querySelectorAll(".add-to-cart-link").forEach((link) => {
    link.addEventListener("click", (e) => {
      e.preventDefault();
      // Send an AJAX request to add the item to the cart
      handleAjaxRequest(e.currentTarget.href, { method: "GET" }, (data) => {
        if (data.success) {
          // Update the cart item count (add 1)
          updateCartItemCount(1);
        }
      });
    });
  });
}
