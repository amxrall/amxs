document.addEventListener("DOMContentLoaded", () => {
    // HEADER SCROLL
    const header = document.querySelector("header");

    function handleHeaderScroll() {
        if (!header) return;
        if (window.scrollY > 30) {
            header.classList.add("scrolled");
        } else {
            header.classList.remove("scrolled");
        }
    }

    window.addEventListener("scroll", handleHeaderScroll);
    handleHeaderScroll();

    // MENU MOBILE
    const mobileOpenBtn = document.querySelector("header button.lg\\:hidden:not(#openLoginPanel)");
    const mobileOverlay = document.querySelector(".fixed.inset-0.z-40.transition-opacity.duration-500.lg\\:hidden");
    const mobilePanel = document.querySelector(
        ".fixed.top-0.right-0.z-50.h-full.w-\\[280px\\].transition-transform.duration-500.ease-\\[cubic-bezier\\(0\\.23\\,1\\,0\\.32\\,1\\)\\].lg\\:hidden"
    );
    const mobileCloseBtn = mobilePanel ? mobilePanel.querySelector("button") : null;
    const mobileLinks = mobilePanel ? mobilePanel.querySelectorAll("a[href^='#']") : [];

    function openMobileMenu() {
        if (!mobileOverlay || !mobilePanel) return;
        mobileOverlay.classList.remove("opacity-0", "pointer-events-none");
        mobileOverlay.classList.add("opacity-100");
        mobilePanel.classList.remove("translate-x-full");
        mobilePanel.classList.add("translate-x-0");
        document.body.classList.add("overflow-hidden");
    }

    function closeMobileMenu() {
        if (!mobileOverlay || !mobilePanel) return;
        mobileOverlay.classList.add("opacity-0", "pointer-events-none");
        mobileOverlay.classList.remove("opacity-100");
        mobilePanel.classList.add("translate-x-full");
        mobilePanel.classList.remove("translate-x-0");
        document.body.classList.remove("overflow-hidden");
    }

    if (mobileOpenBtn) mobileOpenBtn.addEventListener("click", openMobileMenu);
    if (mobileCloseBtn) mobileCloseBtn.addEventListener("click", closeMobileMenu);
    if (mobileOverlay) mobileOverlay.addEventListener("click", closeMobileMenu);
    mobileLinks.forEach((link) => link.addEventListener("click", closeMobileMenu));

    // SCROLL SUAVE
    document.querySelectorAll("a[href^='#']").forEach((link) => {
        link.addEventListener("click", (e) => {
            const href = link.getAttribute("href");
            const target = document.querySelector(href);
            if (!target) return;
            e.preventDefault();
            target.scrollIntoView({ behavior: "smooth", block: "start" });
        });
    });

    // REVEAL
    const animatedElements = document.querySelectorAll(`
        .reveal,
        .reveal-scale,
        .reveal-left,
        .reveal-right,
        .game-card,
        .game-card-ornate,
        .pulse-ring
    `);

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                entry.target.classList.add("active", "in-view");
                const parentSection = entry.target.closest("section");
                if (parentSection) parentSection.classList.add("section-active");
            });
        },
        { threshold: 0.15, rootMargin: "0px 0px -80px 0px" }
    );

    animatedElements.forEach((el) => observer.observe(el));

    // SEÇÃO VOZ
    const voiceCard = document.querySelector(".game-card-ornate");

    if (voiceCard) {
        const voiceSection = voiceCard.closest("section");

        const voiceObserver = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        voiceCard.classList.add("active", "voice-active");
                        if (voiceSection) voiceSection.classList.add("voice-section-active", "section-active");
                    } else {
                        voiceCard.classList.remove("voice-active");
                        if (voiceSection) voiceSection.classList.remove("voice-section-active");
                    }
                });
            },
            { threshold: 0.35 }
        );

        voiceObserver.observe(voiceCard);
    }

    // PARTÍCULAS
    const particlesContainer = document.getElementById("particles-container");

    if (particlesContainer) {
        function createParticle() {
            const particle = document.createElement("div");
            particle.className = "particle";

            const isGold = Math.random() > 0.5;
            const size = (Math.random() * 2.8 + 1).toFixed(2);
            const left = (Math.random() * 100).toFixed(4);
            const duration = (Math.random() * 8 + 10).toFixed(4);
            const delay = (Math.random() * 5).toFixed(4);
            const color = isGold ? "rgb(231, 182, 35)" : "rgb(44, 150, 88)";

            particle.style.left = `${left}%`;
            particle.style.width = `${size}px`;
            particle.style.height = `${size}px`;
            particle.style.background = color;
            particle.style.boxShadow = `0 0 ${Number(size) * 3}px ${color.replace("rgb", "rgba").replace(")", ", 0.6)")}`;
            particle.style.animationDuration = `${duration}s`;
            particle.style.animationDelay = `${delay}s`;

            particlesContainer.appendChild(particle);

            setTimeout(() => {
                particle.remove();
            }, (Number(duration) + Number(delay)) * 1000 + 1000);
        }

        for (let i = 0; i < 18; i++) createParticle();
        setInterval(createParticle, 900);
    }

    // COUNTDOWN
    const countdownSection = document.getElementById("countdown");

    if (countdownSection) {
        const numberEls = countdownSection.querySelectorAll(".tabular-nums");

        if (numberEls.length >= 4) {
            const targetDate = new Date("2026-12-31T23:59:59-03:00").getTime();

            function updateCountdown() {
                const now = Date.now();
                let distance = targetDate - now;
                if (distance < 0) distance = 0;

                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance / (1000 * 60 * 60)) % 24);
                const minutes = Math.floor((distance / (1000 * 60)) % 60);
                const seconds = Math.floor((distance / 1000) % 60);

                numberEls[0].textContent = String(days).padStart(2, "0");
                numberEls[1].textContent = String(hours).padStart(2, "0");
                numberEls[2].textContent = String(minutes).padStart(2, "0");
                numberEls[3].textContent = String(seconds).padStart(2, "0");
            }

            updateCountdown();
            setInterval(updateCountdown, 1000);
        }
    }

    // FORÇA REVEAL
    document.querySelectorAll(".reveal, .reveal-scale").forEach((el) => {
        const rect = el.getBoundingClientRect();
        if (rect.top < window.innerHeight) el.classList.add("active");
    });

    // PAINEL LOGIN
    const settingsBtn = document.getElementById("openLoginPanel") || document.querySelector('button[title="Painel Admin"]');
    const loginPanel = document.getElementById("loginPanel");
    const loginPanelOverlay = document.getElementById("loginPanelOverlay");
    const loginPanelClose = document.getElementById("loginPanelClose");

    function openLoginPanel(e) {
        if (e) e.preventDefault();
        if (!loginPanel || !loginPanelOverlay) return;

        loginPanelOverlay.classList.remove("opacity-0", "pointer-events-none");
        loginPanelOverlay.classList.add("opacity-100", "pointer-events-auto");

        loginPanel.classList.remove("translate-x-full");
        loginPanel.classList.add("translate-x-0");

        document.body.classList.add("overflow-hidden");
    }

    function closeLoginPanel(e) {
        if (e) e.preventDefault();
        if (!loginPanel || !loginPanelOverlay) return;

        loginPanelOverlay.classList.add("opacity-0", "pointer-events-none");
        loginPanelOverlay.classList.remove("opacity-100", "pointer-events-auto");

        loginPanel.classList.add("translate-x-full");
        loginPanel.classList.remove("translate-x-0");

        document.body.classList.remove("overflow-hidden");
    }

    if (settingsBtn && loginPanel && loginPanelOverlay) {
        settingsBtn.addEventListener("click", openLoginPanel);
    }

    if (loginPanelClose) loginPanelClose.addEventListener("click", closeLoginPanel);
    if (loginPanelOverlay) loginPanelOverlay.addEventListener("click", closeLoginPanel);

    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape") closeLoginPanel();
    });

    // DROPDOWN LOGADO
    const dropdown = document.querySelector(".dropdown-account");

    if (dropdown) {
        dropdown.addEventListener("click", (e) => {
            e.stopPropagation();
            dropdown.classList.toggle("open");
        });

        document.addEventListener("click", () => {
            dropdown.classList.remove("open");
        });

        const accountMenu = dropdown.querySelector(".account-menu");
        if (accountMenu) {
            accountMenu.addEventListener("click", (e) => e.stopPropagation());
        }
    }
});

/* CAPTCHA REGISTER */
window.reloadCaptcha = function (e) {
    if (e) e.preventDefault();
    const img = document.querySelector(".captchaImage");
    if (img) img.src = "captcha/securimage_show.php?" + Date.now();
};

/* LOGIN PANEL - GLOBAL */
window.submitPanelLogin = function () {
    const form = document.getElementById("top-login-form");
    if (!form) {
        alert("Formulário de login não encontrado.");
        return;
    }

    const loginInput = form.querySelector('input[name="ucp_login"]');
    const passInput = form.querySelector('input[name="ucp_passw"]');
    const captchaField = document.getElementById("ucp_captcha");

    if (!loginInput || !passInput) {
        alert("Campos de login não encontrados.");
        return;
    }

    if (!loginInput.value.trim() || !passInput.value.trim()) {
        alert("Preencha login e senha.");
        return;
    }

    if (captchaField) {
        window.openLoginCaptcha();
    } else {
        form.submit();
    }
};

window.openLoginCaptcha = function () {
    let overlay = document.getElementById("login-captcha-overlay");

    if (!overlay) {
        overlay = document.createElement("div");
        overlay.id = "login-captcha-overlay";
        overlay.innerHTML = `
            <div class="login-captcha-box">
                <button type="button" class="captcha-close" onclick="window.closeLoginCaptcha()">×</button>
                <h3>Validação de Segurança</h3>
                <img id="login-captcha-img" src="captcha/securimage_show.php" alt="captcha">
                <input type="text" id="login-captcha-input" placeholder="Digite o captcha" maxlength="5">
                <div class="captcha-actions">
                    <button type="button" onclick="window.reloadLoginCaptcha()">Recarregar</button>
                    <button type="button" onclick="window.submitLoginWithCaptcha()">Confirmar</button>
                </div>
            </div>
        `;
        document.body.appendChild(overlay);
    }

    const input = document.getElementById("login-captcha-input");
    const img = document.getElementById("login-captcha-img");

    if (input) input.value = "";
    if (img) img.src = "captcha/securimage_show.php?" + Date.now();

    overlay.style.display = "flex";
};

window.closeLoginCaptcha = function () {
    const overlay = document.getElementById("login-captcha-overlay");
    if (overlay) overlay.style.display = "none";
};

window.reloadLoginCaptcha = function () {
    const img = document.getElementById("login-captcha-img");
    if (img) img.src = "captcha/securimage_show.php?" + Date.now();
};

window.submitLoginWithCaptcha = function () {
    const input = document.getElementById("login-captcha-input");
    const captchaField = document.getElementById("ucp_captcha");
    const form = document.getElementById("top-login-form");

    if (!input || !captchaField || !form) {
        alert("Captcha ou formulário não encontrado.");
        return;
    }

    const value = input.value.trim();

    if (value.length < 4) {
        alert("Digite o captcha corretamente.");
        return;
    }

    captchaField.value = value;
    window.closeLoginCaptcha();
    form.submit();
};