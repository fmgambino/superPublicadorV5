
document.addEventListener("DOMContentLoaded", () => {
  const iframe = document.getElementById("toolFrame");

  iframe?.addEventListener("load", () => {
    try {
      iframe.contentWindow.document.body.addEventListener("click", (e) => {
        const target = e.target.closest("a[href*='facebook.com']");
        if (target) {
          e.preventDefault();
          window.open(target.href, "_blank");
        }
      });
    } catch (err) {
      console.warn("No se puede acceder al contenido del iframe por políticas de seguridad.");
    }
  });

  const storedDark = localStorage.getItem("darkMode");
  const isDark = storedDark === "false" ? false : true;
  document.body.classList.toggle("dark-mode", isDark);
  document.getElementById("themeToggle").textContent = isDark ? "☀️" : "🌙";
  updateLogos(isDark);

  const lang = localStorage.getItem("lang") || "es";
  document.documentElement.lang = lang;
  document.getElementById("langTooltip").textContent = lang === "es" ? "Idioma: Español" : "Language: English";
  document.getElementById("welcomeMessage").textContent = lang === "es"
    ? "Bienvenido a la herramienta automática de publicación."
    : "Welcome to the auto posting tool.";

  setTimeout(() => {
    document.getElementById("loader").style.display = "none";
  }, 1000);

  const extensionInstalled = localStorage.getItem("extensionInstalled");
  const fbLoggedIn = localStorage.getItem("facebookLoggedIn");

  if (!extensionInstalled) {
    document.getElementById("extensionModal")?.classList.remove("hidden");
  } else if (!fbLoggedIn) {
    document.getElementById("facebookModal")?.classList.remove("hidden");
  }

  document.getElementById("themeToggle")?.addEventListener("click", toggleTheme);
  document.getElementById("langToggle")?.addEventListener("click", toggleLanguage);
  document.getElementById("installBtn")?.addEventListener("click", async () => {
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

function toggleMenu() {
  document.getElementById("menu")?.classList.toggle("show");
}

function updateLogos(isDark) {
  const logos = document.querySelectorAll("[data-logo]");
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
  document.getElementById("themeToggle").textContent = isDark ? "☀️" : "🌙";
  updateLogos(isDark);
}

function toggleLanguage() {
  const btn = document.getElementById("langToggle");
  const currentLang = document.documentElement.lang;
  const newLang = currentLang === "es" ? "en" : "es";
  document.documentElement.lang = newLang;
  localStorage.setItem("lang", newLang);
  document.getElementById("langTooltip").textContent = newLang === "es" ? "Idioma: Español" : "Language: English";
  document.getElementById("welcomeMessage").textContent = newLang === "es"
    ? "Bienvenido a la herramienta automática de publicación."
    : "Welcome to the auto posting tool.";
  btn.classList.add("clicked");
  setTimeout(() => btn.classList.remove("clicked"), 400);
}

function openPlansModal() {
  document.getElementById("plansModal")?.classList.remove("hidden");
}

function closePlansModal(event) {
  const modal = document.getElementById("plansModal");
  if (!event || event.target.id === "plansModal") {
    modal?.classList.add("hidden");
  }
}

function dismissInstall() {
  document.getElementById("installBanner")?.classList.add("hidden");
  localStorage.setItem("dismissedInstall", "true");
}

function openFacebookLogin() {
  const win = window.open("https://www.facebook.com/login", "_blank", "width=500,height=600");
  const checkLogin = setInterval(() => {
    if (win.closed) {
      clearInterval(checkLogin);
      localStorage.setItem("facebookLoggedIn", "true");
      closeFacebookModal();
    }
  }, 500);
}

function showFacebookModal() {
  if (!localStorage.getItem("facebookLoggedIn")) {
    document.getElementById("facebookModal")?.classList.remove("hidden");
  }
}

function closeFacebookModal() {
  document.getElementById("facebookModal")?.classList.add("hidden");
}

function closeExtensionModal() {
  document.getElementById("extensionModal")?.classList.add("hidden");
  localStorage.setItem("extensionInstalled", "true");
  setTimeout(() => {
    showFacebookModal();
  }, 800);
}

function leerInstrucciones() {
  const texto = "Para instalar la extensión, descargue el archivo ZIP, descomprímalo, abra Google Chrome, escriba chrome://extensions, active el modo desarrollador y haga clic en Cargar descomprimida. Seleccione la carpeta descomprimida.";
  const voz = new SpeechSynthesisUtterance(texto);
  voz.lang = "es-ES";
  voz.rate = 1;
  window.speechSynthesis.speak(voz);
}

let deferredPrompt = null;
window.addEventListener("beforeinstallprompt", (e) => {
  e.preventDefault();
  deferredPrompt = e;
  if (localStorage.getItem("dismissedInstall") !== "true") {
    const banner = document.getElementById("installBanner");
    if (banner && !banner.querySelector("img[data-logo]")) {
      const logo = document.createElement("img");
      logo.setAttribute("data-logo", "");
      logo.dataset.logoLight = "images/logo-light.png";
      logo.dataset.logoDark = "images/logo-dark.png";
      logo.src = document.body.classList.contains("dark-mode")
        ? logo.dataset.logoDark
        : logo.dataset.logoLight;
      banner.insertBefore(logo, banner.firstChild);
    }
    banner.classList.remove("hidden");
  }
});

if ("serviceWorker" in navigator) {
  window.addEventListener("load", () => {
    navigator.serviceWorker.register("service-worker.js");
  });
}
