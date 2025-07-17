// assets/js/scripts.js

// Fungsi untuk menampilkan preview gambar
function previewImage(input, targetId) {
  const file = input.files[0];
  const preview = document.getElementById(targetId);
  if (file && preview) {
    const reader = new FileReader();
    reader.onload = function (e) {
      preview.src = e.target.result;
    };
    reader.readAsDataURL(file);
  }
}

// Konfirmasi sebelum hapus
function confirmDelete(message = 'Yakin ingin menghapus data ini?') {
  return confirm(message);
}

// Notifikasi (jika ada flash message via session)
document.addEventListener('DOMContentLoaded', () => {
  const alert = document.querySelector('.alert[data-autohide]');
  if (alert) {
    setTimeout(() => alert.remove(), 3000);
  }
});

// Highlight menu aktif (jika digunakan di navbar sidebar)
document.addEventListener('DOMContentLoaded', () => {
  const currentPath = window.location.pathname;
  document.querySelectorAll('.nav-link').forEach(link => {
    if (link.href.includes(currentPath)) {
      link.classList.add('active');
    }
  });
});