document.getElementById('loginForm').addEventListener('submit', function(event) {
    event.preventDefault();

    const login = document.getElementById('login').value;
    const password = document.getElementById('password').value;

    let isValid = true;

    if (!login) {
        isValid = false;
        document.getElementById('loginError').textContent = 'O campo Login é obrigatório.';
    } else {
        document.getElementById('loginError').textContent = '';
    }

    if (!password) {
        isValid = false;
        document.getElementById('passwordError').textContent = 'O campo Senha é obrigatório.';
    } else {
        document.getElementById('passwordError').textContent = '';
    }

    if (isValid) {
        alert('Formulário enviado com sucesso!');
        // Aqui você pode adicionar o código para enviar o formulário
    }
});

    function forgotPassword() {
        window.location.href = 'recuperação-de-senha.html';
    }
    function redirectToLogin() {
        window.location.href = 'login.html';
    }
    function redirectToCadastro() {
        window.location.href = 'cadastro.html';
    }

    // Inicialize o Google One Tap
google.accounts.id.initialize({
    client_id: '1021941130204-qm8kgmtm6f70l0gm1bt85t21eg4rv20v.apps.googleusercontent.com',
    callback: handleCredentialResponse
  });
  // Manipula a resposta de credenciais
  function handleCredentialResponse(response) {
    if (response.credential) {
      // As credenciais do usuário estão disponíveis em response.credential
      console.log('Credenciais do usuário:', response.credential);
    } else {
      // O usuário não fez login
      console.log('O usuário não fez login.');
    }
  }
  // Adicione um ouvinte de eventos ao botão de login
  document.getElementById('login-button').addEventListener('click', function() {
    // Solicite as credenciais do usuário
    google.accounts.id.prompt(notification => {
      if (notification.isNotDisplayed() || notification.isSkippedMoment()) {
        // O usuário não visualizou ou pulou a notificação
        console.log('O usuário não visualizou ou pulou a notificação.');
      } else {
        // A notificação foi exibida e o usuário interagiu com ela
        console.log('O usuário interagiu com a notificação.');
      }
    });
  });