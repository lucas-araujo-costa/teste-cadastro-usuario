document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("registration-form");

  const passwordInput = document.getElementById("password");
  passwordInput.addEventListener("input", function (event) {
    let valor = event.target.value;
    valor = valor.replace(/[^0-9]/g, "");
    event.target.value = valor;
  });

  form.addEventListener("submit", function (event) {
    event.preventDefault();
    let isValid = true;
    clearErrors();

    const name = document.getElementById("name").value.trim();
    const email = document.getElementById("email").value.trim();
    const password = passwordInput.value;

    if (!name) {
      showError("name-error", "O campo Nome é obrigatório.");
      isValid = false;
    }

    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!email || !emailPattern.test(email)) {
      showError("email-error", "O Email deve ser um endereço válido.");
      isValid = false;
    }

    if (password.length < 8) {
      showError("password-error", "A Senha deve conter pelo menos 8 números.");
      isValid = false;
    }

    if (isValid) {
      const formData = new FormData(this);
      fetch("php/config.php", {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            alert(data.message);
            form.reset();
          } else {
            alert(data.message);
          }
        })
        .catch((error) => {
          console.error("Erro ao enviar os dados:", error);
          alert("Ocorreu um erro ao enviar os dados.");
        });
    }
  });

  function showError(elementId, message) {
    const errorElement = document.getElementById(elementId);
    if (errorElement) {
      errorElement.textContent = message;
    }
  }

  function clearErrors() {
    const errorElements = document.querySelectorAll(".error-message");
    errorElements.forEach((el) => (el.textContent = ""));
  }
});
