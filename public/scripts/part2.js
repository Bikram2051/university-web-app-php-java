"use strict";

/**
 * COS60004 — Assignment Part 3
 * Main site JS: enquiry form → payment page data flow, validations,
 * price calculator, and hero slider.
 * - No inline/embedded JS in HTML.
 * - Vanilla JS only.
 * - Single DOMContentLoaded, guarded per-page.
 * 
 * ASSIGNMENT 3 UPDATE: Client-side validation disabled via debug flag
 */

// Debug flag for Assignment 3 - set to true to disable client-side validation
const debug = true;

document.addEventListener("DOMContentLoaded", () => {
  initializeEnquiryPage();
  initializePaymentPage();
  initializeHeroSlider();
});

/* ========= Enquiry (enquire.php) ========= */

function initializeEnquiryPage() {
  if (!document.getElementById("enquiry-form")) return;
  setupEnquiryForm();
}

function setupEnquiryForm() {
  const form = document.getElementById("enquiry-form");
  const errorContainer = document.getElementById("error-message");
  if (!form) return;

  // Always setup price calculator (not validation)
  setupPriceCalculator();

  form.addEventListener("submit", (e) => {
    e.preventDefault();

    // Show loading state on submit button
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn ? showLoadingState(submitBtn) : null;

    try {
      // ASSIGNMENT 3: Skip validation if debug is true
      let errors = [];
      if (!debug) {
        errors = validateEnquiryForm();
      }
      
      if (errors.length === 0) {
        // Store on client first (assignment requirement)
        storeEnquiryData();

        // Proceed to payment.php via form submission
        setTimeout(() => {
          form.submit();
        }, 300);
      } else {
        displayErrors(errorContainer, errors);
        if (submitBtn && originalText !== null) hideLoadingState(submitBtn, originalText);
      }
    } catch (err) {
      handleFormError(err, "enquiry form");
      if (submitBtn && originalText !== null) hideLoadingState(submitBtn, originalText);
    }
  });
}

function setupPriceCalculator() {
  const productSelect = document.getElementById("product");
  const quantityInput = document.getElementById("quantity");
  const featureCheckboxes = document.querySelectorAll('input[name="features[]"]');
  const totalDisplay = document.getElementById("estimated-total");
  if (!productSelect || !totalDisplay) return;

  function calculateTotal() {
    let basePrice = 0;
    let featuresTotal = 0;

    const selectedOption = productSelect.options[productSelect.selectedIndex];
    if (selectedOption?.dataset.price) {
      basePrice = parseFloat(selectedOption.dataset.price);
    }

    featureCheckboxes.forEach((cb) => {
      if (cb.checked && cb.dataset.price) {
        featuresTotal += parseFloat(cb.dataset.price);
      }
    });

    const qty = parseInt(quantityInput?.value, 10) || 1;
    const total = (basePrice + featuresTotal) * qty;

    totalDisplay.textContent = total.toFixed(2);
    return total;
  }

  productSelect.addEventListener("change", calculateTotal);
  quantityInput?.addEventListener("input", calculateTotal);
  featureCheckboxes.forEach((cb) => cb.addEventListener("change", calculateTotal));

  // Initial calculation
  calculateTotal();
}

function validateEnquiryForm() {
  // ASSIGNMENT 3: Skip validation if debug is true
  if (debug) return [];

  const errors = [];

  const firstName = document.getElementById("firstname")?.value.trim();
  const lastName = document.getElementById("lastname")?.value.trim();
  const email = document.getElementById("email")?.value.trim();
  const phone = document.getElementById("phone")?.value.trim();
  if (!firstName) errors.push("First name is required");
  if (!lastName) errors.push("Last name is required");
  if (!email) errors.push("Email is required");
  if (!phone) errors.push("Phone number is required");

  const street = document.getElementById("street")?.value.trim();
  const suburb = document.getElementById("suburb")?.value.trim();
  const state = document.getElementById("state")?.value;
  const postcode = document.getElementById("postcode")?.value.trim();
  if (!street) errors.push("Street address is required");
  if (!suburb) errors.push("Suburb is required");
  if (!state) errors.push("State is required");
  if (!postcode) errors.push("Postcode is required");

  const product = document.getElementById("product")?.value;
  const quantity = parseInt(document.getElementById("quantity")?.value, 10);
  if (!product) errors.push("Please select a product");
  if (!quantity || quantity < 1) errors.push("Quantity must be at least 1");

  if (state && postcode && !validateStatePostcode(state, postcode)) {
    errors.push("Postcode does not match selected state");
  }

  return errors;
}

function validateStatePostcode(state, postcode) {
  const firstDigit = postcode.charAt(0);
  const rules = {
    VIC: ["3", "8"],
    NSW: ["1", "2"],
    QLD: ["4", "9"],
    NT: ["0"],
    WA: ["6"],
    SA: ["5"],
    TAS: ["7"],
    ACT: ["0"],
  };
  return rules[state]?.includes(firstDigit) || false;
}

function displayErrors(container, errors) {
  if (!container) return;
  if (errors.length === 0) {
    container.innerHTML = "";
    container.style.display = "none";
    return;
  }
  container.innerHTML = `
    <div class="error-message">
      <h4>Please correct the following errors:</h4>
      <ul>${errors.map((e) => `<li>${e}</li>`).join("")}</ul>
    </div>`;
  container.style.display = "block";
}

function storeEnquiryData() {
  const data = {
    personal: {
      firstname: document.getElementById("firstname")?.value.trim() || "",
      lastname: document.getElementById("lastname")?.value.trim() || "",
      email: document.getElementById("email")?.value.trim() || "",
      phone: document.getElementById("phone")?.value.trim() || "",
      contactMethod: document.querySelector('input[name="contact_method"]:checked')?.value || "",
    },
    address: {
      street: document.getElementById("street")?.value.trim() || "",
      suburb: document.getElementById("suburb")?.value.trim() || "",
      state: document.getElementById("state")?.value || "",
      postcode: document.getElementById("postcode")?.value.trim() || "",
    },
    product: {
      productId: document.getElementById("product")?.value || "",
      productName: document.getElementById("product")?.options[document.getElementById("product").selectedIndex]?.text || "",
      quantity: parseInt(document.getElementById("quantity")?.value, 10) || 1,
      features: Array.from(document.querySelectorAll('input[name="features[]"]:checked')).map((cb) => ({
        value: cb.value,
        name: cb.parentElement.textContent.trim(),
        price: parseFloat(cb.dataset.price) || 0,
      })),
      basePrice: parseFloat(document.getElementById("product")?.options[document.getElementById("product").selectedIndex]?.dataset.price) || 0,
      totalPrice: parseFloat(document.getElementById("estimated-total")?.textContent) || 0,
    },
    comments: document.getElementById("comments")?.value.trim() || "",
  };

  sessionStorage.setItem("echosphereEnquiryData", JSON.stringify(data));
}

/* ========= Payment (payment.php) ========= */

function initializePaymentPage() {
  if (!document.getElementById("payment-form")) return;

  loadOrderSummary();
  setupPaymentValidation();
  setupCancelButton();
}

function loadOrderSummary() {
  const stored = sessionStorage.getItem("echosphereEnquiryData");
  if (!stored) {
    window.location.href = "enquire.php";
    return;
  }
  const data = JSON.parse(stored);

  const customerInfo = document.getElementById("customer-info");
  customerInfo && (customerInfo.innerHTML = `
      <p><strong>Name:</strong> ${data.personal.firstname} ${data.personal.lastname}</p>
      <p><strong>Email:</strong> ${data.personal.email}</p>
      <p><strong>Phone:</strong> ${data.personal.phone}</p>
      <p><strong>Address:</strong> ${data.address.street}, ${data.address.suburb}, ${data.address.state} ${data.address.postcode}</p>
      <p><strong>Contact Preference:</strong> ${data.personal.contactMethod}</p>
    `);

  const productInfo = document.getElementById("product-info");
  if (productInfo) {
    const featuresList = data.product.features.length > 0
      ? data.product.features.map((f) => `<li>${f.name}</li>`).join("")
      : "<li>No additional features selected</li>";
    productInfo.innerHTML = `
      <p><strong>Product:</strong> ${data.product.productName}</p>
      <p><strong>Quantity:</strong> ${data.product.quantity}</p>
      <div class="selected-features">
        <strong>Selected Features:</strong>
        <ul>${featuresList}</ul>
      </div>`;
  }

  const priceDetails = document.getElementById("price-details");
  if (priceDetails) {
    const featuresTotal = data.product.features.reduce((s, f) => s + f.price, 0);
    const subtotal = (data.product.basePrice + featuresTotal) * data.product.quantity;
    priceDetails.innerHTML = `
      <p><strong>Base Price:</strong> $${data.product.basePrice.toFixed(2)}</p>
      <p><strong>Additional Features:</strong> $${featuresTotal.toFixed(2)}</p>
      <p><strong>Quantity:</strong> ${data.product.quantity}</p>
      <p class="total-amount"><strong>Total Amount:</strong> $${subtotal.toFixed(2)}</p>`;
  }
}

function setupPaymentValidation() {
  const form = document.getElementById("payment-form");
  const errorContainer = document.getElementById("payment-error-message");
  if (!form) return;

  form.addEventListener("submit", (e) => {
    e.preventDefault();
    
    // ASSIGNMENT 3: Skip validation if debug is true
    const errors = debug ? [] : validatePaymentForm();
    
    if (errors.length === 0) {
      prepareFormSubmission();
      form.submit();
    } else {
      displayErrors(errorContainer, errors);
    }
  });
}

function validatePaymentForm() {
  // ASSIGNMENT 3: Skip validation if debug is true
  if (debug) return [];

  const errors = [];

  const cardType = document.getElementById("card-type")?.value;
  const cardName = document.getElementById("card-name")?.value.trim();
  const cardNumber = document.getElementById("card-number")?.value.replace(/\s/g, "") || "";
  const cardExpiry = document.getElementById("card-expiry")?.value.trim();
  const cardCVV = document.getElementById("card-cvv")?.value.trim();

  if (!cardType) errors.push("Please select a credit card type");

  if (!cardName) {
    errors.push("Name on card is required");
  } else if (!/^[A-Za-z\s]+$/.test(cardName)) {
    errors.push("Name on card can only contain letters and spaces");
  } else if (cardName.length > 40) {
    errors.push("Name on card cannot exceed 40 characters");
  }

  if (!cardNumber) {
    errors.push("Credit card number is required");
  } else {
    const cardValidation = validateCardNumber(cardNumber, cardType);
    if (!cardValidation.isValid) errors.push(cardValidation.error);
  }

  if (!cardExpiry) {
    errors.push("Expiry date is required");
  } else if (!/^\d{2}-\d{2}$/.test(cardExpiry)) {
    errors.push("Expiry date must be in MM-YY format (e.g., 12-25)");
  } else if (!validateExpiryDate(cardExpiry)) {
    errors.push("Credit card has expired or expiry date is invalid");
  }

  if (!cardCVV) {
    errors.push("CVV is required");
  } else if (!/^\d{3}$/.test(cardCVV)) {
    errors.push("CVV must be exactly 3 digits");
  }

  return errors;
}

function validateCardNumber(number, type) {
  const clean = number.replace(/\D/g, "");
  switch (type) {
    case "visa":
      if (!/^4/.test(clean)) return { isValid: false, error: "Visa cards must start with 4" };
      if (clean.length !== 16) return { isValid: false, error: "Visa cards must have 16 digits" };
      break;
    case "mastercard":
      if (!/^5[1-5]/.test(clean)) return { isValid: false, error: "MasterCard must start with 51-55" };
      if (clean.length !== 16) return { isValid: false, error: "MasterCard must have 16 digits" };
      break;
    case "amex":
      if (!/^3[47]/.test(clean)) return { isValid: false, error: "American Express must start with 34 or 37" };
      if (clean.length !== 15) return { isValid: false, error: "American Express must have 15 digits" };
      break;
    default:
      return { isValid: false, error: "Invalid card type" };
  }
  return { isValid: true };
}

function validateExpiryDate(expiry) {
  const [month, year] = expiry.split("-").map(Number);
  const now = new Date();
  const currentYear = now.getFullYear() % 100;
  const currentMonth = now.getMonth() + 1;
  if (month < 1 || month > 12) return false;
  if (year < currentYear) return false;
  if (year === currentYear && month < currentMonth) return false;
  return true;
}

function setupCancelButton() {
  const cancelButton = document.getElementById("cancel-order");
  if (!cancelButton) return;
  cancelButton.addEventListener("click", () => {
    sessionStorage.removeItem("echosphereEnquiryData");
    window.location.href = "index.php";
  });
}

function prepareFormSubmission() {
  const form = document.getElementById("payment-form");
  const stored = sessionStorage.getItem("echosphereEnquiryData");
  if (!form || !stored) return;

  const data = JSON.parse(stored);
  Object.keys(data).forEach((section) => {
    const val = data[section];
    if (val && typeof val === "object" && !Array.isArray(val)) {
      Object.keys(val).forEach((field) => {
        const hidden = document.createElement("input");
        hidden.type = "hidden";
        hidden.name = `${section}_${field}`;
        hidden.value = val[field];
        form.appendChild(hidden);
      });
    }
  });
}

/* ========= Shared helpers ========= */

function showLoadingState(button) {
  const originalText = button.textContent;
  button.innerHTML = '<span class="loading-spinner"></span> Processing...';
  button.disabled = true;
  return originalText;
}

function hideLoadingState(button, originalText) {
  button.textContent = originalText;
  button.disabled = false;
}

function handleFormError(error, context) {
  console.error(`Form Error in ${context}:`, error);
  const errorContainer = document.getElementById("error-message") || document.getElementById("payment-error-message");
  if (errorContainer) {
    errorContainer.innerHTML = `
      <div class="error-message">
        <h4>System Error</h4>
        <p>Sorry, something went wrong. Please try again.</p>
      </div>`;
    errorContainer.style.display = "block";
  }
}

/* ========= Hero Slider (index.php) ========= */

function initializeHeroSlider() {
  const slider = document.querySelector(".hero-slider");
  if (!slider) return;
  new HeroSlider();
}

class HeroSlider {
  constructor() {
    this.currentSlide = 0;
    this.slides = [];
    this.dots = [];
    this.interval = null;
    this.setupSlider();
  }

  setupSlider() {
    this.slides = document.querySelectorAll(".slide");
    this.dots = document.querySelectorAll(".slider-dot");
    if (this.slides.length === 0 || this.dots.length === 0) return;

    this.dots.forEach((dot, i) => dot.addEventListener("click", () => this.goToSlide(i)));

    this.goToSlide(0);
    this.startAutoSlide();

    const slider = document.querySelector(".hero-slider");
    slider.addEventListener("mouseenter", () => this.stopAutoSlide());
    slider.addEventListener("mouseleave", () => this.startAutoSlide());
  }

  goToSlide(i) {
    this.slides.forEach((s) => s.classList.remove("active"));
    this.dots.forEach((d) => d.classList.remove("active"));
    if (this.slides[i]) {
      this.slides[i].classList.add("active");
      this.dots[i]?.classList.add("active");
      this.currentSlide = i;
    }
  }

  nextSlide() {
    const next = (this.currentSlide + 1) % this.slides.length;
    this.goToSlide(next);
  }

  startAutoSlide() {
    this.stopAutoSlide();
    this.interval = setInterval(() => this.nextSlide(), 5000);
  }

  stopAutoSlide() {
    if (this.interval) {
      clearInterval(this.interval);
      this.interval = null;
    }
  }
}
/* ========= End of File ========= */