// Base API path
const API_BASE = 'api';

// State variables
let isLoginMode = true;
let currentUser = null;
let currentSearchTerm = "";

// DOM Elements
const authSection = document.getElementById('auth-section');
const dashboardSection = document.getElementById('dashboard-section');
const authForm = document.getElementById('auth-form');
const toggleAuthBtn = document.getElementById('toggle-auth-btn');
const contactsContainer = document.getElementById('contacts-container');
const searchInput = document.getElementById('search-input');
const modal = document.getElementById('contact-modal');
const contactForm = document.getElementById('contact-form');

// Initialization
document.addEventListener('DOMContentLoaded', () => {
    // Check if user is already logged in
    const storedUser = localStorage.getItem('contactManagerUser');
    if (storedUser) {
        currentUser = JSON.parse(storedUser);
        showDashboard();
    }

    // Event Listeners
    authForm.addEventListener('submit', handleAuthSubmit);
    toggleAuthBtn.addEventListener('click', toggleAuthMode);
    document.getElementById('logout-btn').addEventListener('click', handleLogout);
    
    // Live Search with basic debounce
    let timeout = null;
    searchInput.addEventListener('keyup', (e) => {
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            currentSearchTerm = e.target.value.trim();
            loadContacts();
        }, 300);
    });

    document.getElementById('show-add-modal-btn').addEventListener('click', () => openModal());
    document.getElementById('close-modal-btn').addEventListener('click', closeModal);
    contactForm.addEventListener('submit', handleContactSubmit);
});

// --- UI / STATE MANAGEMENT ---

function toggleAuthMode() {
    isLoginMode = !isLoginMode;
    document.getElementById('auth-title').innerText = isLoginMode ? 'Login' : 'Sign Up';
    document.getElementById('auth-submit-btn').innerText = isLoginMode ? 'Log In' : 'Register';
    document.getElementById('auth-toggle-text').innerText = isLoginMode ? "Don't have an account?" : "Already have an account?";
    document.getElementById('toggle-auth-btn').innerText = isLoginMode ? "Sign Up" : "Log In";
    document.getElementById('register-fields').style.display = isLoginMode ? 'none' : 'block';
    
    // Toggle required attributes
    document.getElementById('firstName').required = !isLoginMode;
    document.getElementById('lastName').required = !isLoginMode;
    document.getElementById('auth-error').innerText = "";
    authForm.reset();
}

function showDashboard() {
    authSection.style.display = 'none';
    dashboardSection.style.display = 'block';
    document.getElementById('welcome-msg').innerText = `Welcome, ${currentUser.firstName}!`;
    loadContacts();
}

function handleLogout() {
    localStorage.removeItem('contactManagerUser');
    currentUser = null;
    dashboardSection.style.display = 'none';
    authSection.style.display = 'block';
    authForm.reset();
    document.getElementById('search-input').value = "";
    currentSearchTerm = "";
}

// --- AJAX CALLS (JSON via Fetch) ---

async function apiCall(endpoint, payload) {
    try {
        const response = await fetch(`${API_BASE}/${endpoint}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        const rawText = await response.text();
        console.log(`[apiCall ${endpoint}] Status: ${response.status} ${response.statusText}`);
        console.log(`[apiCall ${endpoint}] Raw response:`, rawText);
        try {
            return JSON.parse(rawText);
        } catch (parseError) {
            console.error(`[apiCall ${endpoint}] JSON parse failed:`, parseError);
            console.error(`[apiCall ${endpoint}] Response was not valid JSON`);
            return { error: "Server returned invalid JSON. Check console for raw response." };
        }
    } catch (error) {
        console.error(`[apiCall ${endpoint}] Network error:`, error);
        console.error(`[apiCall ${endpoint}] Error name:`, error.name);
        console.error(`[apiCall ${endpoint}] Error message:`, error.message);
        return { error: "Network or server error occurred." };
    }
}

async function handleAuthSubmit(e) {
    e.preventDefault();
    const errorDiv = document.getElementById('auth-error');
    errorDiv.innerText = "Processing...";

    const login = document.getElementById('login').value.trim();
    const password = document.getElementById('password').value.trim();
    
    let payload = { login, password };
    let endpoint = 'Login.php';

    if (!isLoginMode) {
        payload.firstName = document.getElementById('firstName').value.trim();
        payload.lastName = document.getElementById('lastName').value.trim();
        endpoint = 'Register.php';
    }

    const data = await apiCall(endpoint, payload);

    if (data.error && data.error.length > 0) {
        errorDiv.innerText = data.error;
    } else {
        errorDiv.innerText = "";
        currentUser = { id: data.id, firstName: data.firstName, lastName: data.lastName };
        localStorage.setItem('contactManagerUser', JSON.stringify(currentUser));
        showDashboard();
    }
}

async function loadContacts() {
    const errorDiv = document.getElementById('contact-error');
    errorDiv.innerText = "Loading contacts...";
    contactsContainer.innerHTML = "";

    const payload = { userId: currentUser.id, search: currentSearchTerm };
    const data = await apiCall('SearchContacts.php', payload);

    errorDiv.innerText = "";

    if (data.error && data.error !== "No Records Found") {
        errorDiv.innerText = data.error;
        return;
    }

    if (!data.results || data.results.length === 0) {
        contactsContainer.innerHTML = "<p>No contacts found.</p>";
        return;
    }

    data.results.forEach(contact => {
        const card = document.createElement('div');
        card.className = 'contact-card';
        card.innerHTML = `
            <div class="contact-name">${escapeHTML(contact.firstName)} ${escapeHTML(contact.lastName)}</div>
            <div class="contact-detail">📞 ${escapeHTML(contact.phone) || 'N/A'}</div>
            <div class="contact-detail">✉️ ${escapeHTML(contact.email) || 'N/A'}</div>
            <div class="contact-actions">
                <button class="btn-secondary" onclick='openModal(${JSON.stringify(contact).replace(/'/g, "\\'")})'>Edit</button>
                <button class="btn-secondary" style="color: var(--error-color); border-color: var(--error-color);" onclick="deleteContact(${contact.id})">Delete</button>
            </div>
        `;
        contactsContainer.appendChild(card);
    });
}

// --- MODAL & CRUD LOGIC ---

function openModal(contact = null) {
    const errorDiv = document.getElementById('modal-error');
    errorDiv.innerText = "";
    contactForm.reset();

    if (contact) {
        document.getElementById('modal-title').innerText = "Edit Contact";
        document.getElementById('contact-id').value = contact.id;
        document.getElementById('contactFirstName').value = contact.firstName;
        document.getElementById('contactLastName').value = contact.lastName;
        document.getElementById('contactPhone').value = contact.phone;
        document.getElementById('contactEmail').value = contact.email;
    } else {
        document.getElementById('modal-title').innerText = "Add Contact";
        document.getElementById('contact-id').value = "";
    }
    
    modal.style.display = 'flex';
    modal.setAttribute('aria-hidden', 'false');
}

function closeModal() {
    modal.style.display = 'none';
    modal.setAttribute('aria-hidden', 'true');
}

async function handleContactSubmit(e) {
    e.preventDefault();
    const errorDiv = document.getElementById('modal-error');
    errorDiv.innerText = "Saving...";

    const id = document.getElementById('contact-id').value;
    const contactFirstName = document.getElementById('contactFirstName').value.trim();
    const contactLastName = document.getElementById('contactLastName').value.trim();
    const phone = document.getElementById('contactPhone').value.trim();
    const email = document.getElementById('contactEmail').value.trim();

    // --- ADD THIS PHONE VALIDATION ---
    // This regex only allows numbers, spaces, plus signs, dashes, and parentheses
    const phoneRegex = /^[0-9\s\+\-\(\)]*$/; 
    
    if (phone !== "" && !phoneRegex.test(phone)) {
        errorDiv.innerText = "Invalid phone number. Please use only numbers and standard symbols (-, +, (), spaces).";
        return; // Stop the submission
    }
    // ---------------------------------

    const payload = {
        userId: currentUser.id,
        contactFirstName,
        contactLastName,
        phone,
        email
    };

    let endpoint = 'AddContact.php';
    if (id) {
        payload.id = parseInt(id);
        endpoint = 'UpdateContact.php';
    }

    const data = await apiCall(endpoint, payload);

    if (data.error && data.error.length > 0) {
        errorDiv.innerText = data.error;
    } else {
        closeModal();
        loadContacts(); // Refresh grid
    }
}

async function deleteContact(contactId) {
    if (!confirm("Are you sure you want to delete this contact?")) return;

    const payload = { userId: currentUser.id, id: contactId };
    const data = await apiCall('DeleteContact.php', payload);

    if (data.error && data.error.length > 0) {
        document.getElementById('contact-error').innerText = data.error;
    } else {
        loadContacts(); // Refresh grid
    }
}

// Utility to prevent basic XSS when rendering dynamic HTML
function escapeHTML(str) {
    if (!str) return '';
    return str.replace(/[&<>'"]/g, 
        tag => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            "'": '&#39;',
            '"': '&quot;'
        }[tag])
    );
}