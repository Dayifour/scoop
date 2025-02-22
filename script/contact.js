var to = "codingteam@telegmail.com";
var subject = "Nouveau Message";

document.getElementById('sendBtn').addEventListener('click', function() {
  var nom = document.getElementById('nom').value;
  var adresse = document.getElementById('adresse').value;
  var message = "Nom : " + nom + "\nAdresse : " + adresse + "\n\n" + document.getElementById('message').value;

  fetch('https://codingmailer.onrender.com/send-email', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      to: to,
      subject: subject,
      message: message
    })
  })
  .then(function(response) {
    if (response.ok) {
      alert('E-mail envoyé avec succès.');
      // Réinitialiser les champs du formulaire
      document.getElementById('nom').value = '';
      document.getElementById('adresse').value = '';
      document.getElementById('message').value = '';
      document.getElementById('errorContainer').textContent = ''; // Effacer le message d'erreur précédent
    } else {
      response.json().then(function(data) {
        var errorMessage = data && data.message ? data.message : 'Erreur lors de l\'envoi de l\'e-mail.';
        document.getElementById('errorContainer').textContent = 'Erreur : ' + errorMessage;
      });
    }
  })
  .catch(function(error) {
    console.log('Erreur lors de la requête :', error);
    document.getElementById('errorContainer').textContent = 'Erreur lors de la requête : ' + error;
  });
});