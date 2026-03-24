document.addEventListener('DOMContentLoaded', function() {
  // Sidebar section toggle
  document.querySelectorAll('.section-header').forEach(header => {
    header.addEventListener('click', function() {
      const section = this.parentElement;
      const list = section.querySelector('.modules-list');
      const toggle = this.querySelector('.section-toggle');
      if (list.style.display === 'none' || !list.style.display) {
        list.style.display = '';
        toggle.style.transform = 'rotate(0deg)';
      } else {
        list.style.display = 'none';
        toggle.style.transform = 'rotate(-90deg)';
      }
    });
  });

  // Module click (highlight)
  document.querySelectorAll('.module').forEach(mod => {
    mod.addEventListener('click', function() {
      document.querySelectorAll('.module').forEach(m => m.classList.remove('active'));
      this.classList.add('active');
      // TODO: Load video/quiz/project content dynamically
    });
  });

  // Navigation buttons (dummy)
  document.querySelector('.prev-btn').addEventListener('click', function() {
    alert('Go to previous module (implement logic)');
  });
  document.querySelector('.next-btn').addEventListener('click', function() {
    alert('Go to next module (implement logic)');
  });
});
