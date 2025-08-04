//Je récupère l'info du clic sur ma div avec une class like
document.querySelector('.like').addEventListener('click', function () {
    //je récupère l'id de l'article concerné par le clic
    const articleId = this.dataset.id;

    //Je fais le fetch pour acceder à l'url de la route avec l'ajout de l'id de l'article    
    fetch(`/article/like/${articleId}`, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'    //indique au serveur qu'il s'agit d'une requete AJAX
        }
    })
    .then(response => {
        if (response.redirected) {
            // Si la reponse est une redirection car l'utilisateur n'est pas connecté
            alert("Tu dois être connectée pour liker cet article.");
            window.location.href = response.url; // on redirige vers /login
            return;
        }

        return response.text();     //Sinon je demande une réponse sous forme de texte html
})
  
    .then(nbreLikes => {
        document.getElementById('like-count').textContent = nbreLikes;  //nbreLikes est le retour que ma route fait dans return new Response((string) count($article->getLikes()));
    })
    .catch(error => console.error('Erreur AJAX :', error));
});