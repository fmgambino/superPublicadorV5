document.addEventListener("DOMContentLoaded", () => {
  const iframe = document.getElementById("toolFrame");

  iframe.addEventListener("load", () => {
    iframe.contentWindow.document.body.addEventListener("click", (e) => {
      const target = e.target.closest("a[href*='facebook.com']");
      if (target) {
        e.preventDefault();
        window.open(target.href, "_blank");
      }
    });
  });
});


let deferredPrompt = null;

function toggleMenu() {
  const menu = document.getElementById("menu");
  menu.classList.toggle("show");
}

/**
 * Updates all logos marked with data attributes based on the current theme.
 * Each <img> that should swap must have:
 *   data-logo-light="path/to/light-logo.png"
 *   data-logo-dark="path/to/dark-logo.png"
 * and an attribute data-logo for selection.
 */
function updateLogos(isDark) {
  const logos = document.querySelectorAll('[data-logo]');
  logos.forEach(img => {
    const lightSrc = img.dataset.logoLight;
    const darkSrc = img.dataset.logoDark;
    if (lightSrc && darkSrc) {
      img.src = isDark ? darkSrc : lightSrc;
    }
  });
}

function toggleTheme() {
  const isDark = document.body.classList.toggle("dark-mode");
  localStorage.setItem("darkMode", isDark);
  // Swap the theme toggle icon
  document.getElementById("themeToggle").textContent = isDark ? "☀️" : "🌙";
  // Update all logos on the page
  updateLogos(isDark);
}

function toggleLanguage() {
  const btn = document.getElementById("langToggle");
  const currentLang = document.documentElement.lang;
  const newLang = currentLang === "es" ? "en" : "es";
  document.documentElement.lang = newLang;
  localStorage.setItem("lang", newLang);
  document.getElementById("langTooltip").textContent =
    newLang === "es" ? "Idioma: Español" : "Language: English";
  document.getElementById("welcomeMessage").textContent =
    newLang === "es"
      ? "Bienvenido a la herramienta automática de publicación."
      : "Welcome to the auto posting tool.";
  btn.classList.add("clicked");
  setTimeout(() => btn.classList.remove("clicked"), 400);
}

function openPlansModal() {
  document.getElementById("plansModal").classList.remove("hidden");
}

function closePlansModal(event) {
  const modal = document.getElementById("plansModal");
  if (!event || event.target.id === "plansModal") {
    modal.classList.add("hidden");
  }
}

function dismissInstall() {
  document.getElementById("installBanner").classList.add("hidden");
  localStorage.setItem("dismissedInstall", "true");
}

window.addEventListener("DOMContentLoaded", () => {
  // Hide loader after brief display
  setTimeout(() => {
    document.getElementById("loader").style.display = "none";
  }, 1000);

  // Apply stored theme or default to dark-mode
  const storedDark = localStorage.getItem("darkMode");
  let isDark;
  if (storedDark === "false") {
    document.body.classList.remove("dark-mode");
    document.getElementById("themeToggle").textContent = "🌙";
    isDark = false;
  } else {
    document.body.classList.add("dark-mode");
    document.getElementById("themeToggle").textContent = "☀️";
    isDark = true;
  }
  // Swap logos initially
  updateLogos(isDark);

  // Apply stored language or default to Spanish
  const lang = localStorage.getItem("lang") || "es";
  document.documentElement.lang = lang;
  document.getElementById("langTooltip").textContent =
    lang === "es" ? "Idioma: Español" : "Language: English";
  document.getElementById("welcomeMessage").textContent =
    lang === "es"
      ? "Bienvenido a la herramienta automática de publicación."
      : "Welcome to the auto posting tool.";

  // Event listeners
  document.getElementById("themeToggle").addEventListener("click", toggleTheme);
  document.getElementById("langToggle").addEventListener("click", toggleLanguage);
  document.getElementById("installBtn").addEventListener("click", async () => {
    if (deferredPrompt) {
      deferredPrompt.prompt();
      const choice = await deferredPrompt.userChoice;
      if (choice.outcome === "accepted") {
        document.getElementById("installBanner").classList.add("hidden");
      }
      deferredPrompt = null;
    }
  });
});

// Capture beforeinstallprompt to show custom install banner
window.addEventListener("beforeinstallprompt", (e) => {
  e.preventDefault();
  deferredPrompt = e;

  if (localStorage.getItem("dismissedInstall") !== "true") {
    const banner = document.getElementById("installBanner");
    if (banner && !banner.querySelector('img[data-logo]')) {
      // Create a themed logo for the banner
      const logo = document.createElement("img");
      logo.setAttribute('data-logo', '');
      logo.dataset.logoLight = "images/logo-light.png";
      logo.dataset.logoDark = "images/logo-dark.png";
      // Set initial src based on current theme
      const isDarkNow = document.body.classList.contains('dark-mode');
      logo.src = isDarkNow ? logo.dataset.logoDark : logo.dataset.logoLight;
      banner.insertBefore(logo, banner.firstChild);
    }
    banner.classList.remove("hidden");
  }
});

// Register service worker if supported
if ("serviceWorker" in navigator) {
  window.addEventListener("load", () => {
    navigator.serviceWorker.register("service-worker.js");
  });
}
