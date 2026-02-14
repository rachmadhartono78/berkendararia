// ============================================
// BERKENDARA RIA - Custom JavaScript
// ============================================

document.addEventListener('DOMContentLoaded', function () {
  'use strict';

  // --- Navbar Scroll Effect ---
  const navbar = document.getElementById('navbar');
  const backToTop = document.getElementById('backToTop');

  window.addEventListener('scroll', function () {
    const scrollY = window.scrollY;

    // Navbar shrink
    if (scrollY > 80) {
      navbar.classList.add('scrolled');
    } else {
      navbar.classList.remove('scrolled');
    }

    // Back to top visibility
    if (scrollY > 600) {
      backToTop.classList.add('visible');
    } else {
      backToTop.classList.remove('visible');
    }
  });

  // --- Back to Top ---
  backToTop.addEventListener('click', function () {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });

  // --- Mobile Hamburger Menu ---
  const hamburger = document.getElementById('hamburger');
  const navLinks = document.getElementById('navLinks');

  hamburger.addEventListener('click', function () {
    hamburger.classList.toggle('active');
    navLinks.classList.toggle('open');
  });

  // Close menu on link click
  navLinks.querySelectorAll('a').forEach(function (link) {
    link.addEventListener('click', function () {
      hamburger.classList.remove('active');
      navLinks.classList.remove('open');
    });
  });

  // --- Scroll Reveal (Intersection Observer) ---
  const revealElements = document.querySelectorAll('.reveal, .reveal-left, .reveal-right');

  const revealObserver = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
        revealObserver.unobserve(entry.target);
      }
    });
  }, {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
  });

  revealElements.forEach(function (el) {
    revealObserver.observe(el);
  });

  // --- Toast Notification ---
  function showToast(message, type) {
    var container = document.getElementById('toastContainer');
    var toast = document.createElement('div');
    toast.className = 'toast ' + (type || 'success');
    toast.textContent = message;
    container.appendChild(toast);

    setTimeout(function () {
      toast.style.opacity = '0';
      toast.style.transform = 'translateX(100px)';
      toast.style.transition = 'all 0.3s ease';
      setTimeout(function () {
        if (toast.parentNode) {
          toast.parentNode.removeChild(toast);
        }
      }, 300);
    }, 4000);
  }

  // --- Subscribe Form AJAX ---
  var form = document.getElementById('subscribeForm');
  var btnSubmit = document.getElementById('btnSubmit');

  if (form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();

      var nama = document.getElementById('inputNama').value.trim();
      var email = document.getElementById('inputEmail').value.trim();
      var noHp = document.getElementById('inputHp').value.trim();

      // Basic validation
      if (!nama) {
        showToast('Nama tidak boleh kosong!', 'error');
        return;
      }
      if (!email || !isValidEmail(email)) {
        showToast('Masukkan email yang valid!', 'error');
        return;
      }
      if (!noHp) {
        showToast('No. WhatsApp tidak boleh kosong!', 'error');
        return;
      }

      // Disable button
      btnSubmit.disabled = true;
      btnSubmit.textContent = '⏳ Mengirim...';

      // Send AJAX
      var xhr = new XMLHttpRequest();
      xhr.open('POST', 'php/subscribe.php', true);
      xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

      xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
          btnSubmit.disabled = false;
          btnSubmit.textContent = '🏍️ Daftar Sekarang';

          if (xhr.status === 200) {
            try {
              var response = JSON.parse(xhr.responseText);
              if (response.success) {
                showToast(response.message, 'success');
                form.reset();
              } else {
                showToast(response.message, 'error');
              }
            } catch (err) {
              showToast('Terjadi kesalahan. Coba lagi nanti.', 'error');
            }
          } else {
            showToast('Server error (' + xhr.status + '). Pastikan server & MySQL aktif.', 'error');
          }
        }
      };

      xhr.onerror = function () {
        btnSubmit.disabled = false;
        btnSubmit.textContent = '🏍️ Daftar Sekarang';
        showToast('Gagal terhubung ke server. Cek koneksi dan pastikan server aktif.', 'error');
      };

      var data = 'nama=' + encodeURIComponent(nama) +
        '&email=' + encodeURIComponent(email) +
        '&no_hp=' + encodeURIComponent(noHp);
      xhr.send(data);
    });
  }

  function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
  }

  // --- Active nav link highlight on scroll ---
  var sections = document.querySelectorAll('section[id]');

  window.addEventListener('scroll', function () {
    var scrollPos = window.scrollY + 120;

    sections.forEach(function (section) {
      var top = section.offsetTop;
      var height = section.offsetHeight;
      var id = section.getAttribute('id');

      if (scrollPos >= top && scrollPos < top + height) {
        navLinks.querySelectorAll('a').forEach(function (a) {
          a.style.color = '';
        });
        var activeLink = navLinks.querySelector('a[href="#' + id + '"]');
        if (activeLink) {
          activeLink.style.color = 'var(--primary-light)';
        }
      }
    });
  });
});
