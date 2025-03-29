document.addEventListener('DOMContentLoaded', function () {
    const select = document.getElementById('user-select-dropdown');
    const hiddenInput = document.querySelector('input[name="project_manager_id"]');
  
    if (select && hiddenInput) {
      select.addEventListener('change', function () {
        hiddenInput.value = this.value;
      });
    }
  });
  