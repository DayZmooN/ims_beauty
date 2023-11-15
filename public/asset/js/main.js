// SHOW SERVICE DETAILS ON SOINS PAGE
document.addEventListener('DOMContentLoaded', function () {
    // Function to toggle the service detail and active class
    function toggleServiceDetail(serviceId) {
        // Hide all the service details
        document.querySelectorAll('.service-detail-content').forEach(function(detail) {
            detail.style.display = 'none';
        });

        // Remove 'active' class from all service names
        document.querySelectorAll('.service-name').forEach(function(serviceName) {
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

    // Activate the first service detail and name
    let firstService = document.querySelector('.service-name');
    if (firstService) {
        firstService.classList.add('active'); // Set the first element as active
        let firstServiceId = firstService.dataset.serviceId;
        toggleServiceDetail(firstServiceId);
    }

    // Attach the click event to each service name
    document.querySelectorAll('.service-name').forEach(function(serviceName) {
        serviceName.addEventListener('click', function() {
            toggleServiceDetail(serviceName.dataset.serviceId);
        });
    });
});

//ACCORDEON EFFECT TARIFS PAGE
document.addEventListener('DOMContentLoaded', function () {
    // Function to toggle the service detail and active class
    function toggleServiceDetail(serviceId) {
        // Hide all the service details
        document.querySelectorAll('.service-detail-content').forEach(function(detail) {
            detail.style.display = 'none';
        });

        // Remove 'active' class from all service names
        document.querySelectorAll('.service-name').forEach(function(serviceName) {
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

    // Activate the first service detail and name
    let firstService = document.querySelector('.service-name');
    if (firstService) {
        firstService.classList.add('active'); // Set the first element as active
        let firstServiceId = firstService.dataset.serviceId;
        toggleServiceDetail(firstServiceId);
    }

    // Attach the click event to each service name
    document.querySelectorAll('.service-name').forEach(function(serviceName) {
        serviceName.addEventListener('click', function() {
            toggleServiceDetail(serviceName.dataset.serviceId);
        });
    });

    // Toggle services for categories
    function toggleServices(categoryId) {
        var servicesDiv = document.getElementById('services-' + categoryId);
        var icon = document.getElementById('icon-' + categoryId);

        if (servicesDiv.style.display === 'none' || servicesDiv.style.display === '') {
            servicesDiv.style.display = 'flex';
        } else {
            servicesDiv.style.display = 'none';
        }
    }

    // Attach the click event to each category header
    document.querySelectorAll('.category-header').forEach(function(header) {
        header.addEventListener('click', function() {
            toggleServices(header.getAttribute('data-category-id'));
        });
    });
});

//DASHBOARD 
document.addEventListener('DOMContentLoaded', function () {
    // Function to show/hide appointments and settings and toggle the active class
    function toggleSections(showAppointments, showSettings) {
        const appointmentsSection = document.getElementById('my-appointments');
        const settingsSection = document.getElementById('settings');
        const showAppointmentsLink = document.getElementById('show-appointments');
        const showSettingsLink = document.getElementById('show-settings');

        if (showAppointments) {
            appointmentsSection.style.display = 'block';
            settingsSection.style.display = 'none';
            showAppointmentsLink.parentElement.classList.add('active');
            showSettingsLink.parentElement.classList.remove('active');
        } else {
            appointmentsSection.style.display = 'none';
            settingsSection.style.display = 'block';
            showAppointmentsLink.parentElement.classList.remove('active');
            showSettingsLink.parentElement.classList.add('active');
        }
    }

    // Initial state: show appointments, hide settings, set first link as active
    toggleSections(true, false);

    // Attach click events to links
    const showAppointmentsLink = document.getElementById('show-appointments');
    const showSettingsLink = document.getElementById('show-settings');

    showAppointmentsLink.addEventListener('click', function (e) {
        e.preventDefault();
        toggleSections(true, false);
    });

    showSettingsLink.addEventListener('click', function (e) {
        e.preventDefault();
        toggleSections(false, true);
    });
});

