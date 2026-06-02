/* ===============================
    REGISTER MODAL
=============================== */
document.addEventListener('DOMContentLoaded', () => {
    window.openRegisterModal = function () {
        const modal = document.getElementById('registerModal');
        if (!modal) return;

        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    };

    window.closeRegisterModal = function () {
        const modal = document.getElementById('registerModal');
        if (!modal) return;

        modal.style.display = 'none';
        document.body.style.overflow = '';
    };

    /* ===============================
        CAPTCHA REGISTER
    =============================== */
    window.reloadCaptcha = function (e) {
        if (e) e.preventDefault();
        const img = document.querySelector('.captchaImage');
        if (img) {
            img.src = 'captcha/securimage_show.php?' + Date.now();
        }
    };

    /* ===============================
        DROPDOWN CONTA (LOGADO)
    =============================== */
    const dropdown = document.querySelector('.dropdown-account');

    if (dropdown) {
        dropdown.addEventListener('click', (e) => {
            e.stopPropagation();
            dropdown.classList.toggle('open');
        });

        document.addEventListener('click', () => {
            dropdown.classList.remove('open');
        });

        const accountMenu = dropdown.querySelector('.account-menu');
        if (accountMenu) {
            accountMenu.addEventListener('click', (e) => {
                e.stopPropagation();
            });
        }
    }
});

/* ===============================
    LOGIN CAPTCHA (TOP LOGIN)
=============================== */
window.opencaptcha = function () {
    let overlay = document.getElementById('login-captcha-overlay');

    if (!overlay) {
        overlay = document.createElement('div');
        overlay.id = 'login-captcha-overlay';
        overlay.innerHTML = `
            <div class="login-captcha-box">
                <button class="captcha-close" onclick="closeLoginCaptcha()">×</button>
                <h3>Validação de Segurança</h3>
                <img id="login-captcha-img" src="captcha/securimage_show.php" alt="captcha">
                <input type="text" id="login-captcha-input" placeholder="Digite o captcha" maxlength="5">
                <div class="captcha-actions">
                    <button type="button" onclick="reloadLoginCaptcha()">Recarregar</button>
                    <button type="button" onclick="submitLoginWithCaptcha()">Confirmar</button>
                </div>
            </div>
        `;
        document.body.appendChild(overlay);
    }
    overlay.style.display = 'flex';
};

window.closeLoginCaptcha = function () {
    const overlay = document.getElementById('login-captcha-overlay');
    if (overlay) overlay.style.display = 'none';
};

window.reloadLoginCaptcha = function () {
    const img = document.getElementById('login-captcha-img');
    if (img) {
        img.src = 'captcha/securimage_show.php?' + Date.now();
    }
};

window.submitLoginWithCaptcha = function () {
    const input = document.getElementById('login-captcha-input');
    const captchaField = document.getElementById('ucp_captcha');
    const form = document.getElementById('top-login-form');

    if (!input || !captchaField || !form) return;

    const value = input.value.trim();
    if (value.length < 4) {
        alert('Digite o captcha corretamente.');
        return;
    }

    captchaField.value = value;
    form.submit();
};

/* ===============================
    HEADER MOBILE
=============================== */
window.toggleMobileMenu = function () {
    const header = document.querySelector('.header');
    if (!header) return;
    header.classList.toggle('mobile-open');
};

document.addEventListener('click', (e) => {
    const header = document.querySelector('.header');
    const toggle = document.querySelector('.menu-toggle');
    if (!header || !toggle) return;
    if (!header.contains(e.target) && !toggle.contains(e.target)) {
        header.classList.remove('mobile-open');
    }
});

/* ===============================
    PAGINAÇÃO FLUTUANTE
=============================== */
(function () {
    document.addEventListener('DOMContentLoaded', () => {
        const dots = document.querySelectorAll('.page-nav-dot');
        const sections = Array.from(dots).map(dot => {
            const targetSel = dot.dataset.target;
            return {
                dot,
                target: document.querySelector(targetSel)
            };
        });

        function scrollToTarget(target) {
            const headerOffset = 120;
            const top = target.getBoundingClientRect().top + window.scrollY - headerOffset;
            window.scrollTo({ top, behavior: 'smooth' });
        }

        dots.forEach(dot => {
            dot.addEventListener('click', e => {
                e.preventDefault();
                const target = document.querySelector(dot.dataset.target);
                if (target) scrollToTarget(target);
            });
        });

        function updateActiveByScroll() {
            const scrollPos = window.scrollY + 200;
            let current = null;
            sections.forEach(s => {
                if (!s.target) return;
                if (s.target.offsetTop <= scrollPos) current = s;
            });
            if (current) {
                dots.forEach(d => d.classList.remove('active'));
                current.dot.classList.add('active');
            }
        }

        window.addEventListener('scroll', updateActiveByScroll);
        window.addEventListener('load', updateActiveByScroll);
    });
})();

/* ===============================
    COUNTDOWN (PHP VARIABLES REQ)
=============================== */
// Nota: Certifique-se de definir essas variáveis no seu arquivo PHP principal antes de chamar o app.js
function startCountdown(cAno, cMes, cDia, cHor, cMin, sumH) {
    const cdDays = document.getElementById('cd-days');
    const cdHours = document.getElementById('cd-hours');
    const cdMinutes = document.getElementById('cd-minutes');
    const cdSeconds = document.getElementById('cd-seconds');

    if (!cdDays) return;

    const targetTime = new Date(cAno, cMes - 1, cDia, cHor + sumH, cMin, 0).getTime();

    function pad(n) { return n < 10 ? '0' + n : '' + n; }

    function updateCountdown() {
        const now = Date.now();
        const diff = targetTime - now;

        if (diff <= 0) {
            cdDays.textContent = '00'; cdHours.textContent = '00';
            cdMinutes.textContent = '00'; cdSeconds.textContent = '00';
            return;
        }

        let seconds = Math.floor(diff / 1000);
        const days = Math.floor(seconds / 86400);
        seconds -= days * 86400;
        const hours = Math.floor(seconds / 3600);
        seconds -= hours * 3600;
        const minutes = Math.floor(seconds / 60);
        seconds -= minutes * 60;

        cdDays.textContent = pad(days);
        cdHours.textContent = pad(hours);
        cdMinutes.textContent = pad(minutes);
        cdSeconds.textContent = pad(seconds);
    }

    updateCountdown();
    setInterval(updateCountdown, 1000);
}

/* ===============================
    PRELOADER & DISCORD
=============================== */
document.addEventListener("DOMContentLoaded", function () {
    const preloadConfig = { minTime: 2.5, maxTime: 6, withOnload: true, timeInterval: 0.3 };
    const preloader = document.getElementById("preloader");
    if (!preloader) return;

    let startTime = Date.now();
    let finished = false;

    function hidePreloader() {
        if (finished) return;
        finished = true;
        preloader.classList.add("preloader-hide");
        setTimeout(() => {
            if (preloader && preloader.parentNode) preloader.parentNode.removeChild(preloader);
            setTimeout(() => { window.openDiscord(); }, 400);
        }, 600);
    }

    const checkInterval = setInterval(() => {
        let elapsed = (Date.now() - startTime) / 1000;
        if (elapsed >= preloadConfig.maxTime) {
            clearInterval(checkInterval);
            hidePreloader();
        }
        if (preloadConfig.withOnload && window.pageLoaded && elapsed >= preloadConfig.minTime) {
            clearInterval(checkInterval);
            hidePreloader();
        }
    }, preloadConfig.timeInterval * 1000);

    window.addEventListener("load", () => {
        window.pageLoaded = true;
        let elapsed = (Date.now() - startTime) / 1000;
        if (elapsed >= preloadConfig.minTime) hidePreloader();
    });
});

window.openDiscord = function() {
    const pop = document.getElementById('discordPopup');
    if(pop) pop.style.display = 'flex';
};

window.closeDiscord = function() {
    const pop = document.getElementById('discordPopup');
    if(pop) pop.style.display = 'none';
};

/* ===============================
    NEWS MODAL
=============================== */
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('newsModal');
    if (!modal) return;
    
    const modalImg = modal.querySelector('.news-modal__image');
    const modalTitle = modal.querySelector('.news-modal__title');
    const modalText = modal.querySelector('.news-modal__text');
    const modalDate = modal.querySelector('.news-modal__date');
    const closeBtn = modal.querySelector('.news-modal__close');
    const overlay = modal.querySelector('.news-modal__overlay');

    document.querySelectorAll('.news-btn').forEach(btn => {
        btn.addEventListener('click', e => {
            e.preventDefault();
            modalImg.style.backgroundImage = `url('${btn.dataset.image}')`;
            modalTitle.textContent = btn.dataset.title;
            modalText.innerHTML = btn.dataset.text.replace(/\n/g, "<br>");
            modalDate.textContent = btn.dataset.date;
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
    });

    window.closeNewsModal = function() {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    };

    if(closeBtn) closeBtn.addEventListener('click', window.closeNewsModal);
    if(overlay) overlay.addEventListener('click', window.closeNewsModal);
});

/* ===============================
    SWIPER & FORMS (JQUERY)
=============================== */
$(document).ready(function() {
    if ($('.ranking-swiper').length > 0) {
        new Swiper('.ranking-swiper', {
            slidesPerView: 2,
            spaceBetween: 30,
            observer: true,
            observeParents: true,
            watchOverflow: true,
            pagination: { el: '.swiper-pagination', clickable: true },
            navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
            breakpoints: { 0: { slidesPerView: 1 }, 768: { slidesPerView: 2 } }
        });
    }

    $(document).on('submit', '.registerForm', function (e) {
        e.preventDefault();
        $.ajax({
            url: './?engine=create_account',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function (r) {
                if (r.act === 'OK' && r.url) {
                    window.closeRegisterModal();
                    setTimeout(function () { window.location.href = r.url; }, 300);
                } else {
                    alert(r.msg || 'Erro ao criar conta');
                }
            },
            error: function () { alert('Erro de comunicação com o servidor.'); }
        });
        return false;
    });
});

/* ===============================
    UCP MODALS
=============================== */
window.openUcpChangePass = function() {
    document.getElementById('ucpChangePassModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
};
window.closeUcpChangePass = function() {
    document.getElementById('ucpChangePassModal').style.display = 'none';
    document.body.style.overflow = '';
};
window.openUcpUnstuck = function() {
    document.getElementById('ucpUnstuckModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
};
window.closeUcpUnstuck = function() {
    document.getElementById('ucpUnstuckModal').style.display = 'none';
    document.body.style.overflow = '';
};
window.openUcpEmailChange = function() {
    document.getElementById('ucpEmailChangeModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
};
window.closeUcpEmailChange = function() {
    document.getElementById('ucpEmailChangeModal').style.display = 'none';
    document.body.style.overflow = '';
};