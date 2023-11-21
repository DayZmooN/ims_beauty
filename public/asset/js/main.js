// SHOW SERVICE DETAILS ON SOINS PAGE
document.addEventListener('DOMContentLoaded', function () {
    // Function to toggle the service detail and active class
    function toggleServiceDetail(serviceId) {
        // Hide all the service details
        document.querySelectorAll('.service-detail-content').forEach(function (detail) {
            detail.style.display = 'none';
        });

        // Remove 'active' class from all service names
        document.querySelectorAll('.service-name').forEach(function (serviceName) {
            serviceName.classList.remove('active');
        });

        // Show the selected service detail
        var detailToShow = document.getElementById('service-detail-' + serviceId);
        if (detailToShow) {
            detailToShow.style.display = 'block';
        }

        // Add 'active' class to the selected service name
        var serviceNameToShow = document.getElementById('service-name-' + serviceId);
        if (serviceNameToShow) {
            serviceNameToShow.classList.add('active');
        }
    }

    // Activate the first service detail and name, if available
    let firstService = document.querySelector('.service-name');
    if (firstService) {
        firstService.classList.add('active'); // Set the first element as active
        let firstServiceId = firstService.dataset.serviceId;
        toggleServiceDetail(firstServiceId);

        // Attach the click event to each service name
        document.querySelectorAll('.service-name').forEach(function (serviceName) {
            serviceName.addEventListener('click', function () {
                toggleServiceDetail(serviceName.dataset.serviceId);
            });
        });
    }
});

// ACCORDEON EFFECT TARIFS PAGE
document.addEventListener('DOMContentLoaded', function () {
    // Toggle services for categories
    function toggleServices(categoryId) {
        var servicesDiv = document.getElementById('services-' + categoryId);
        var icon = document.getElementById('icon-' + categoryId);

        if (servicesDiv) {
            servicesDiv.style.display = servicesDiv.style.display === 'none' || servicesDiv.style.display === '' ? 'flex' : 'none';
        }
    }

    // Attach the click event to each category header
    document.querySelectorAll('.category-header').forEach(function (header) {
        header.addEventListener('click', function () {
            toggleServices(header.getAttribute('data-category-id'));
        });
    });
});

// DASHBOARD
document.addEventListener('DOMContentLoaded', function () {
    // Function to show/hide appointments and settings and toggle the active class
    function toggleSections(showAppointments, showSettings) {
        const appointmentsSection = document.getElementById('my-appointments');
        const settingsSection = document.getElementById('settings');
        const showAppointmentsLink = document.getElementById('show-appointments');
        const showSettingsLink = document.getElementById('show-settings');

        if (appointmentsSection && settingsSection && showAppointmentsLink && showSettingsLink) {
            appointmentsSection.style.display = showAppointments ? 'flex' : 'none';
            settingsSection.style.display = showSettings ? 'flex' : 'none';
            showAppointmentsLink.parentElement.classList.toggle('active', showAppointments);
            showSettingsLink.parentElement.classList.toggle('active', showSettings);
        }
    }

    // Function to display the correct section based on URL hash
    function displaySectionBasedOnHash() {
        const hash = window.location.hash;
        if (hash === '#my-appointments') {
            toggleSections(true, false);
        } else if (hash === '#settings') {
            toggleSections(false, true);
        } else {
            // Default view
            toggleSections(true, false);
        }
    }

    // Attach click events to internal navigation links
    const showAppointmentsLink = document.getElementById('show-appointments');
    const showSettingsLink = document.getElementById('show-settings');

    if (showAppointmentsLink) {
        showAppointmentsLink.addEventListener('click', function (e) {
            e.preventDefault();
            window.location.hash = 'my-appointments';
        });
    }

    if (showSettingsLink) {
        showSettingsLink.addEventListener('click', function (e) {
            e.preventDefault();
            window.location.hash = 'settings';
        });
    }

    // Display the correct section based on the URL hash when the page loads
    displaySectionBasedOnHash();

    // Update content when hash changes (e.g., when user clicks header links)
    window.addEventListener('hashchange', displaySectionBasedOnHash);
});