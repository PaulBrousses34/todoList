var app = {
    // Méthode qui initialise notre objet "app"
    init: function() {
        console.log('app initialisée');
        // Ajout écouter 'click' sur image du cookie
        $('.list-group-item form button').on('click', app.deleteTask);
    },
    // Méthode qui va chercher le message
    deleteTask: function(e) {
        // Court-circuite l'envoi du form
        e.preventDefault();

        console.log('bouton de form cliqué');
        // Log de l'event "e" récupéré par la fonction
        var btn = $(e.target);
        console.log(btn);
        // La liste se trouve 2 parents plus
        // Il y a peut-être plus safe mais pour le moment ça fera l'affaire :)
        var listItem = btn.parent().parent().parent();
        // Envoi de la requête ajax au serveur
        $.ajax(
            // la variable 'deleteURL' est définie via Twig
            // list.html.twig (via une variable JS)
            deleteURL,
            {
                method: 'POST',
                data: {
                    'id': listItem.attr('data-id')
                }
            }
        // Ecouteur du retour de la requête en cas de succès
        ).done(function(data) {
            // data correspond au contenu renvoyé par la réponse
            console.log(data);
            // On supprime la ligne du DOM (la tâche n'existe plus en back)
            if(data.error == false) {
                // On y accède via le sélecteur CSS .list-group-item[data-id=2]
                $('.list-group-item[data-id=' + data.id + ']').remove();
            }
        });
    }
}

$(app.init());
