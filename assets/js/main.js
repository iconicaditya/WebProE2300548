/**
 * Main JavaScript File
 * EduSkill Marketplace System (EMS)
 * 
 * This file contains global JavaScript functionality for the application.
 * - No business logic yet
 * - No CRUD operations yet
 * - Basic utility functions and event listeners only
 * - Vanilla JavaScript only (no jQuery, no external libraries except Bootstrap)
 * 
 * This file will be included on all pages via the footer.php include.
 */

// ============================================
// 1. DOCUMENT READY / INITIALIZATION
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    console.log('EduSkill Marketplace System loaded');
    
    // Initialize any global event listeners here
    initializeEventListeners();
    
    // Initialize tooltips (Bootstrap)
    initializeBootstrapTooltips();
});

// ============================================
// 2. BOOTSTRAP INITIALIZATION
// ============================================

/**
 * Initialize Bootstrap tooltips
 * Bootstrap tooltips need to be initialized via JavaScript
 */
function initializeBootstrapTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

// ============================================
// 3. GLOBAL EVENT LISTENERS
// ============================================

/**
 * Initialize global event listeners
 */
function initializeEventListeners() {
    // Add click handler for navigation links
    const navLinks = document.querySelectorAll('.navbar-nav a');
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            // This will be expanded with actual navigation logic later
            console.log('Navigation clicked: ' + this.textContent);
        });
    });
    
    // Add handler for all buttons
    const buttons = document.querySelectorAll('button');
    buttons.forEach(button => {
        button.addEventListener('click', function() {
            // This will be expanded with form handling logic later
            console.log('Button clicked: ' + this.textContent);
        });
    });
}

// ============================================
// 4. UTILITY FUNCTIONS
// ============================================

/**
 * Show an alert message to the user
 * @param {string} message - The message to display
 * @param {string} type - Alert type: 'success', 'error', 'info', 'warning'
 */
function showAlert(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-' + type + ' alert-dismissible fade show';
    alertDiv.setAttribute('role', 'alert');
    
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    const container = document.querySelector('body');
    container.insertBefore(alertDiv, container.firstChild);
}

/**
 * Get URL parameters
 * @param {string} paramName - The parameter name to retrieve
 * @returns {string|null} The parameter value or null if not found
 */
function getUrlParameter(paramName) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(paramName);
}

/**
 * Format a date string to readable format
 * @param {string} dateString - The date string to format
 * @returns {string} Formatted date
 */
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('en-US', options);
}

// ============================================
// 5. FORM UTILITIES
// ============================================

/**
 * Clear all form fields
 * @param {string} formId - The ID of the form to clear
 */
function clearForm(formId) {
    const form = document.getElementById(formId);
    if (form) {
        form.reset();
    }
}

/**
 * Disable form submission button during processing
 * @param {string} buttonId - The ID of the submit button
 * @param {boolean} disable - True to disable, false to enable
 */
function toggleFormButton(buttonId, disable = true) {
    const button = document.getElementById(buttonId);
    if (button) {
        button.disabled = disable;
        if (disable) {
            button.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
        } else {
            button.innerHTML = button.getAttribute('data-original-text') || 'Submit';
        }
    }
}

// ============================================
// 6. NOTES
// ============================================

/*
 * EXPANSION AREAS (to be implemented in later phases):
 * 
 * - Authentication handlers (login, logout, registration)
 * - Form validation functions
 * - API/AJAX calls for CRUD operations
 * - Role-based UI modifications
 * - Dynamic content loading
 * - Data manipulation and processing
 * 
 * Current phase: Framework setup only
 */

console.log('EMS Main JavaScript loaded successfully');
