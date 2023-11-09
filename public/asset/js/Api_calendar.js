function start() {
  gapi.client
    .init({
      apiKey: "AIzaSyAJOE5ji9Sz-bj7ksBG8kWV9BXc1y_wk7E",
      discoveryDocs: [
        "https://www.googleapis.com/discovery/v1/apis/calendar/v3/rest",
      ],
      // Plus de paramètres de configuration si nécessaire
    })
    .then(function () {
      // Écoutez le clic sur votre élément de calendrier et appelez la fonction pour créer un événement
    });
}
gapi.load("client:auth2", start);
