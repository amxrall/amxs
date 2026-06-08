document.addEventListener('DOMContentLoaded', () => {

    /* ===============================
       REGISTER MODAL
    =============================== */
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

    if (overlay) {
        overlay.remove();
    }

    overlay = document.createElement('div');
    overlay.id = 'login-captcha-overlay';

    overlay.innerHTML = `
        <div class="login-captcha-box">

            <button class="captcha-close" onclick="closeLoginCaptcha()">×</button>

            <h3>Validação de Segurança</h3>

            <img id="login-captcha-img"
                 src="captcha/securimage_show.php?${Date.now()}"
                 alt="captcha">

            <input type="text"
                   id="login-captcha-input"
                   placeholder="Digite o captcha"
                   maxlength="5">

            <div class="captcha-actions">
                <button type="button" onclick="reloadLoginCaptcha()">
                    Recarregar
                </button>

                <button type="button" onclick="
var input=document.getElementById('login-captcha-input');
var captcha=document.getElementById('ucp_captcha');
var form=document.getElementById('top-login-form');

if(!input || !captcha || !form){
    alert('Formulário de login não encontrado.');
} else {
    captcha.value = input.value.trim();
    form.submit();
}
">
Confirmar
</button>
            </div>

        </div>
    `;

    document.body.appendChild(overlay);
    overlay.style.display = 'flex';
};

window.closeLoginCaptcha = function () {
    const overlay = document.getElementById('login-captcha-overlay');

    if (overlay) {
        overlay.style.display = 'none';
    }
};

window.reloadLoginCaptcha = function () {
    const img = document.getElementById('login-captcha-img');

    if (img) {
        img.src = 'captcha/securimage_show.php?' + Date.now();
    }
};

window.submitLoginWithCaptcha = function () {

    alert('entrou na função');

    const input = document.getElementById('login-captcha-input');
    const captchaField = document.getElementById('ucp_captcha');
    const form = document.getElementById('top-login-form');

    if (!input || !captchaField || !form) {
        alert('Formulário não encontrado');
        return;
    }

    const value = input.value.trim();

    if (value.length < 4) {
        alert('Digite o captcha corretamente.');
        return;
    }

    captchaField.value = value;

    $.ajax({
        type: 'POST',
        url: form.getAttribute('action'),
        data: $(form).serialize(),

        success: function (response) {

            alert(response);
            console.log(response);

            if (
                response === 'OK' ||
                response.indexOf('"act":"OK"') !== -1 ||
                response.indexOf("'act':'OK'") !== -1
            ) {
                location.reload();
                return;
            }
        },

        error: function (xhr) {
            alert('Erro no login: ' + xhr.status);
            console.log(xhr);
        }
    });
};

/* ===============================
   HEADER MOBILE
=============================== */
window.toggleMobileMenu = function () {
    const header = document.querySelector('.header');
    if (!header) return;

    header.classList.toggle('mobile-open');
};

/* Fecha ao clicar fora */
document.addEventListener('click', (e) => {
    const header = document.querySelector('.header');
    const toggle = document.querySelector('.menu-toggle');

    if (!header || !toggle) return;

    if (!header.contains(e.target)) {
        header.classList.remove('mobile-open');
    }
});
