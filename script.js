// ===== Navbar Scroll Effect =====
const navbar = document.getElementById('navbar');
window.addEventListener('scroll', () => {
  navbar.classList.toggle('scrolled', window.scrollY > 50);
});

// ===== Mobile Menu Toggle =====
const navToggle = document.getElementById('navToggle');
const navLinks = document.getElementById('navLinks');

navToggle.addEventListener('click', () => {
  navLinks.classList.toggle('open');
  navToggle.classList.toggle('active');
});

// Close menu on link click
navLinks.querySelectorAll('a').forEach(link => {
  link.addEventListener('click', () => {
    navLinks.classList.remove('open');
    navToggle.classList.remove('active');
  });
});

// ===== Active Nav Link on Scroll =====
const sections = document.querySelectorAll('section[id]');
const navItems = navLinks.querySelectorAll('a');

function updateActiveLink() {
  const scrollY = window.scrollY + 120;
  sections.forEach(section => {
    const top = section.offsetTop;
    const height = section.offsetHeight;
    const id = section.getAttribute('id');
    if (scrollY >= top && scrollY < top + height) {
      navItems.forEach(a => {
        a.classList.remove('active');
        if (a.getAttribute('href') === '#' + id) {
          a.classList.add('active');
        }
      });
    }
  });
}
window.addEventListener('scroll', updateActiveLink);

// ===== Scroll Reveal Animation =====
const fadeElements = document.querySelectorAll('.fade-in');

const observer = new IntersectionObserver((entries) => {
  entries.forEach((entry, i) => {
    if (entry.isIntersecting) {
      setTimeout(() => {
        entry.target.classList.add('visible');
      }, i * 80);
      observer.unobserve(entry.target);
    }
  });
}, { threshold: 0.12 });

fadeElements.forEach(el => observer.observe(el));

// ===== Contact Form =====
const contactForm = document.getElementById('contactForm');
contactForm.addEventListener('submit', async (e) => {
  e.preventDefault();
  const btn = contactForm.querySelector('button[type="submit"]');
  const originalHTML = btn.innerHTML;

  // Loading durumu
  btn.disabled = true;
  btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Gönderiliyor...';

  try {
    const formData = new FormData(contactForm);
    const response = await fetch('send_mail.php', {
      method: 'POST',
      body: formData
    });
    const data = await response.json();

    if (data.success) {
      btn.innerHTML = '<i class="fa-solid fa-check"></i> Gönderildi!';
      btn.style.background = 'linear-gradient(135deg, #22c55e, #16a34a)';
      btn.style.boxShadow = '0 4px 20px rgba(34,197,94,0.3)';
      contactForm.reset();
    } else {
      btn.innerHTML = '<i class="fa-solid fa-xmark"></i> ' + data.message;
      btn.style.background = 'linear-gradient(135deg, #ef4444, #dc2626)';
      btn.style.boxShadow = '0 4px 20px rgba(239,68,68,0.3)';
    }
  } catch (err) {
    btn.innerHTML = '<i class="fa-solid fa-xmark"></i> Bağlantı hatası!';
    btn.style.background = 'linear-gradient(135deg, #ef4444, #dc2626)';
    btn.style.boxShadow = '0 4px 20px rgba(239,68,68,0.3)';
  }

  setTimeout(() => {
    btn.innerHTML = originalHTML;
    btn.style.background = '';
    btn.style.boxShadow = '';
    btn.disabled = false;
  }, 3000);
});

// ===== Smooth scroll for anchor links =====
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
  anchor.addEventListener('click', function (e) {
    e.preventDefault();
    const target = document.querySelector(this.getAttribute('href'));
    if (target) {
      target.scrollIntoView({ behavior: 'smooth' });
    }
  });
});
