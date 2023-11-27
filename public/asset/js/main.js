document.addEventListener('DOMContentLoaded', function() {
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
    
    //Handle Add to cart on Détails & Tarifs pages
    handleCartAddition();
});

function handleDropdownMenus() {
    const userIcon = document.querySelector('.user-icon');
    if (userIcon) {
        userIcon.addEventListener('click', function(e) {
            e.preventDefault();
            this.nextElementSibling.style.display = this.nextElementSibling.style.display === 'block' ? 'none' : 'block';
        });
    }

    window.addEventListener('click', function(e) {
        if (!e.target.matches('.user-icon, .user-icon *')) {
            document.querySelectorAll(".dropdown-content").forEach(dropdown => {
                if (dropdown.style.display === 'block') {
                    dropdown.style.display = 'none';
                }
            });
        }
    });
}

function handleServiceDetails() {
    const toggleServiceDetail = (serviceId) => {
        document.querySelectorAll('.service-detail-content').forEach(detail => detail.style.display = 'none');
        document.querySelectorAll('.service-name').forEach(serviceName => serviceName.classList.remove('active'));

        const detailToShow = document.getElementById('service-detail-' + serviceId);
        const serviceNameToShow = document.getElementById('service-name-' + serviceId);
        if (detailToShow) detailToShow.style.display = 'block';
        if (serviceNameToShow) serviceNameToShow.classList.add('active');
    };

    const firstService = document.querySelector('.service-name');
    if (firstService) {
        firstService.classList.add('active');
        toggleServiceDetail(firstService.dataset.serviceId);
        document.querySelectorAll('.service-name').forEach(serviceName => {
            serviceName.addEventListener('click', () => toggleServiceDetail(serviceName.dataset.serviceId));
        });
    }
}

function handleAccordionEffect() {
    document.querySelectorAll('.category-header').forEach(header => {
        header.addEventListener('click', () => {
            const servicesDiv = document.getElementById('services-' + header.getAttribute('data-category-id'));
            if (servicesDiv) {
                servicesDiv.style.display = servicesDiv.style.display === 'none' || servicesDiv.style.display === '' ? 'flex' : 'none';
            }
        });
    });
}

function handleDashboardSections() {
    const toggleSections = (showAppointments, showSettings) => {
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
    };

    const displaySectionBasedOnHash = () => {
        const hash = window.location.hash;
        if (hash === '#my-appointments') {
            toggleSections(true, false);
        } else if (hash === '#settings') {
            toggleSections(false, true);
        } else {
            toggleSections(true, false);
        }
    };

    const showAppointmentsLink = document.getElementById('show-appointments');
    const showSettingsLink = document.getElementById('show-settings');

    if (showAppointmentsLink) {
        showAppointmentsLink.addEventListener('click', function(e) {
            e.preventDefault();
            window.location.hash = 'my-appointments';
        });
    }

    if (showSettingsLink) {
        showSettingsLink.addEventListener('click', function(e) {
            e.preventDefault();
            window.location.hash = 'settings';
        });
    }

    displaySectionBasedOnHash();
    window.addEventListener('hashchange', displaySectionBasedOnHash);
}


function handleUserDashboardUpdate() {
    var updateButton = document.getElementById('update-user-button');
    var userForm = document.getElementById('update-user-form');

    if (updateButton && userForm) {
        var updateUrl = userForm.getAttribute('data-update-url');

        updateButton.addEventListener('click', function(e) {
            e.preventDefault();
            var formData = new FormData(userForm);

            handleAjaxRequest(updateUrl, { method: 'POST', body: formData }, function(data) {
                window.location.reload(); // Force reload regardless of the response
            }, function(error) {
                console.error("Update error:", error); // Error log
            });
        });
    }
}

function handleCartInteractions() {
    // Toggle calendar display
    document.querySelectorAll('.open-calendar').forEach(button => {
        button.addEventListener('click', e => {
            e.preventDefault();
            const serviceId = e.currentTarget.closest('.service-item').dataset.serviceId;
            toggleCalendar(serviceId);
        });
    });

    // Remove items from cart
    document.querySelectorAll('.remove-from-cart-link').forEach(link => {
        link.addEventListener('click', e => {
            e.preventDefault();
            const url = e.currentTarget.href;
            const serviceId = e.currentTarget.closest('.service-item').dataset.serviceId;

            handleAjaxRequest(url, { method: 'GET' }, data => {
                if (data.success) {
                    document.querySelector(`.service-item[data-service-id="${serviceId}"]`).remove();
                    updateCartItemCount(-1);
                }
            });
        });
    });
}

function toggleCalendar(serviceId) {
    const calendar = document.getElementById(`calendar-${serviceId}`);
    if (calendar) {
        calendar.style.display = calendar.style.display === 'none' ? 'block' : 'none';
    }
}

function updateCartItemCount(change) {
    const cartItemCountElement = document.querySelector('.cart-item-count');
    if (cartItemCountElement) {
        const currentCount = parseInt(cartItemCountElement.textContent) || 0;
        const newCount = Math.max(currentCount + change, 0);
        cartItemCountElement.textContent = newCount > 0 ? newCount : '';
    }
}

function handleCartAddition() {
    document.querySelectorAll('.add-to-cart-link').forEach(link => {
        link.addEventListener('click', e => {
            e.preventDefault();
            handleAjaxRequest(e.currentTarget.href, { method: 'GET' }, data => {
                if (data.success) {
                    updateCartItemCount(1);
                }
            });
        });
    });
}