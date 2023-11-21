// // document.addEventListener("DOMContentLoaded", function () {
// //   // Supposons que chaque service dans le panier ait un attribut data avec la durée
// //   const services = document.querySelectorAll(".service-row");
// //   let servicesData = [];

// //   services.forEach((serviceElement) => {
// //     let name = serviceElement.dataset.name;
// //     let duration = parseInt(serviceElement.dataset.duration, 10); // Durée en minutes
// //     servicesData.push({ name, duration });
// //   });

// //   // Affichage des créneaux disponibles
// //   fetchAvailableSlots(servicesData);
// // });

// // function fetchAvailableSlots(servicesData) {
// //   fetch("URL_DU_BACKEND_POUR_RECUPERER_CRENEAUX", {
// //     method: "POST",
// //     headers: {
// //       "Content-Type": "application/json",
// //     },
// //     body: JSON.stringify({ services: servicesData }),
// //   })
// //     .then((response) => response.json())
// //     .then((slots) => {
// //       displaySlots(slots);
// //     })
// //     .catch((error) => console.error("Erreur:", error));
// // }

// // function displaySlots(slots) {
// //   const slotsContainer = document.getElementById("slotsContainer");
// //   slotsContainer.innerHTML = ""; // Effacer les créneaux précédents

// //   slots.forEach((slot) => {
// //     const slotElement = document.createElement("div");
// //     slotElement.textContent = `${slot.start} - ${slot.end}`;
// //     slotsContainer.appendChild(slotElement);
// //   });
// // }

// function initGoogleAPI() {
//   gapi.load("client:auth2", () => {
//     gapi.client
//       .init({
//         apiKey: "AIzaSyAJOE5ji9Sz-bj7ksBG8kWV9BXc1y_wk7E",
//         clientId:
//           "207164739117-trp0er85ho9ggk8dqnpd1e3jjl1pk5hb.apps.googleusercontent.com",
//         discoveryDocs: [
//           "https://www.googleapis.com/discovery/v1/apis/calendar/v3/rest",
//         ],
//         scope: "https://www.googleapis.com/auth/calendar",
//       })
//       .then(() => {
//         // Une fois que l'API Google est initialisée, vous pouvez ajouter votre code ici

//         // Code pour récupérer les horaires disponibles et les afficher
//         const now = new Date();
//         const oneWeekLater = new Date(now);
//         oneWeekLater.setDate(now.getDate() + 7);

//         gapi.client.calendar.events
//           .list({
//             calendarId: "karimryahi01@gmail.com",
//             timeMin: now.toISOString(),
//             timeMax: oneWeekLater.toISOString(),
//             maxResults: 100,
//             orderBy: "startTime",
//             singleEvents: true,
//           })
//           .then((response) => {
//             const events = response.result.items;

//             // Récupérer toutes les durées des services et calculer les créneaux disponibles pour chacun
//             const serviceDurations = document.querySelectorAll(
//               "[data-service-duration]"
//             );
//             serviceDurations.forEach((element) => {
//               let serviceDuration = parseInt(
//                 element.dataset.serviceDuration,
//                 10
//               );
//               displayAvailability(now, oneWeekLater, events, serviceDuration);
//             });
//           })
//           .catch((error) => {
//             console.error(
//               "Erreur lors de la récupération des événements :",
//               error
//             );
//           });

//         // Code pour ajouter un événement au calendrier
//         document
//           .querySelector("form")
//           .addEventListener("submit", function (event) {
//             event.preventDefault();

//             const selectedSlot = document.querySelector(
//               'input[name="selectedTimeSlot"]:checked'
//             );
//             if (!selectedSlot) {
//               alert("Veuillez sélectionner un créneau.");
//               return;
//             }

//             const slotValue = selectedSlot.value;

//             // Créer l'événement pour Google Calendar
//             const eventObj = {
//               summary: "Rendez-vous Service",
//               start: {
//                 dateTime: slotValue,
//                 timeZone: "Europe/Paris", // Remplacez par le fuseau horaire souhaité
//               },
//               end: {
//                 dateTime: slotValue,
//                 timeZone: "Europe/Paris", // Assurez-vous que cela correspond à 'start.timeZone'
//               },
//             };

//             // Autorisation pour ajouter l'événement à votre calendrier
//             gapi.auth2
//               .getAuthInstance()
//               .signIn()
//               .then(() => {
//                 const request = gapi.client.calendar.events.insert({
//                   calendarId: "karimryahi01@gmail.com", // Remplacez par votre calendrier
//                   resource: eventObj,
//                 });

//                 request.execute((responseEvent) => {
//                   if (responseEvent.error) {
//                     console.error(
//                       "Erreur lors de l'ajout de l'événement :",
//                       responseEvent.error
//                     );
//                     alert(
//                       "Une erreur s'est produite. Veuillez réessayer plus tard."
//                     );
//                   } else {
//                     alert(
//                       "Rendez-vous ajouté avec succès à votre calendrier !"
//                     );
//                   }
//                 });
//               });
//           });
//       });
//   });
// }

// document.addEventListener("DOMContentLoaded", () => {
//   initGoogleAPI(); // Appel de la fonction d'initialisation lorsque le DOM est prêt
// });

// function displayAvailability(start, end, events, serviceDuration) {
//   const availabilityContainer = document.getElementById(
//     "availability-container"
//   );
//   availabilityContainer.innerHTML = ""; // Effacer les données précédentes

//   let current = new Date(start);
//   while (current < end) {
//     let dayStart = new Date(current.setHours(9, 0, 0, 0));
//     let dayEnd = new Date(current.setHours(18, 0, 0, 0));

//     let dailyEvents = events.filter((event) => {
//       let eventStart = new Date(event.start.dateTime);
//       let eventEnd = new Date(event.end.dateTime);
//       return eventStart < dayEnd && eventEnd > dayStart;
//     });

//     let dayAvailability = calculateDailyAvailability(
//       dayStart,
//       dayEnd,
//       dailyEvents,
//       serviceDuration
//     );
//     dayAvailability.forEach((slot) =>
//       appendSlotToContainer(slot, availabilityContainer)
//     );

//     current.setDate(current.getDate() + 1); // Passer au jour suivant
//   }
// }

// function calculateDailyAvailability(dayStart, dayEnd, events, serviceDuration) {
//   let slots = [];
//   let slotStart = new Date(dayStart);

//   events.forEach((event) => {
//     let eventStart = new Date(event.start.dateTime);
//     let eventEnd = new Date(event.end.dateTime);

//     while (slotStart < eventStart) {
//       let slotEnd = new Date(slotStart.getTime() + serviceDuration * 60000);
//       if (slotEnd <= eventStart && slotEnd <= dayEnd) {
//         slots.push({ start: new Date(slotStart), end: new Date(slotEnd) });
//       }
//       slotStart = slotEnd;
//     }

//     slotStart = eventEnd > slotStart ? new Date(eventEnd) : slotStart;
//   });

//   while (slotStart < dayEnd) {
//     let slotEnd = new Date(slotStart.getTime() + serviceDuration * 60000);
//     if (slotEnd <= dayEnd) {
//       slots.push({ start: new Date(slotStart), end: new Date(slotEnd) });
//     }
//     slotStart = slotEnd;
//   }

//   return slots;
// }
// // ajout recentpour l'envoi
// function appendSlotToContainer(slot, container, serviceId) {
//   const slotLabel = document.createElement("label");
//   slotLabel.className = "slot-label";

//   // Formatez la date et l'heure pour inclure le jour, le mois, l'année, l'heure et les minutes
//   const options = {
//     year: "numeric",
//     month: "long",
//     day: "numeric",
//     hour: "numeric",
//     minute: "numeric",
//   };
//   const formattedStartDate = slot.start.toLocaleDateString(undefined, options);
//   const formattedEndDate = slot.end.toLocaleTimeString(undefined, options);
//   slotLabel.textContent = `${formattedStartDate} - ${formattedEndDate}`;

//   const slotRadio = document.createElement("input");
//   slotRadio.type = "radio";
//   slotRadio.name = "selectedTimeSlot";
//   slotRadio.value = slot.start.toISOString(); // Utilisez un format de date qui convient à votre backend

//   slotLabel.prepend(slotRadio);

//   container.appendChild(slotLabel);
// }
// function addEventToCalendar(startTime, serviceId) {
//   const durationElement = document.querySelector(
//     `[data-service-id="${serviceId}"] [data-service-duration]`
//   );
//   const serviceDuration = durationElement
//     ? parseInt(durationElement.dataset.serviceDuration, 10)
//     : 60;
//   const endTime = new Date(startTime.getTime() + serviceDuration * 60000);

//   const event = {
//     summary: "Rendez-vous Service",
//     start: {
//       dateTime: startTime.toISOString(),
//       timeZone: "Europe/Paris",
//     },
//     end: {
//       dateTime: endTime.toISOString(),
//       timeZone: "Europe/Paris",
//     },
//   };

//   gapi.client.calendar.events
//     .insert({
//       calendarId: "karimryahi01@gmail.com",
//       resource: event,
//     })
//     .then(function (response) {
//       if (response.status === 200) {
//         alert("Événement ajouté avec succès");
//       } else {
//         alert("Erreur lors de l'ajout de l'événement");
//       }
//     });
// }

//pour enoyer un event

// function initClient() {
//   gapi.client
//     .init({
//       apiKey: "AIzaSyAJOE5ji9Sz-bj7ksBG8kWV9BXc1y_wk7E",
//       discoveryDocs: [
//         "https://www.googleapis.com/discovery/v1/apis/calendar/v3/rest",
//       ],
//       clientId:
//         "207164739117-trp0er85ho9ggk8dqnpd1e3jjl1pk5hb.apps.googleusercontent.com",
//       scope: "https://www.googleapis.com/auth/calendar.events",
//     })
//     .then(function () {
//       // L'API est prête à être utilisée
//       document
//         .getElementById("addEventButton")
//         .addEventListener("click", addEventToCalendar);
//     });
// }

// // Fonction pour ajouter un événement au calendrier
// function addEventToCalendar() {
//   var event = {
//     summary: "Nom de l'événement",
//     description: "Description de l'événement",
//     start: {
//       dateTime: "2023-11-27T10:00:00",
//       timeZone: "Europe/Paris",
//     },
//     end: {
//       dateTime: "2023-11-27T12:00:00",
//       timeZone: "Europe/Paris",
//     },
//   };

//   var request = gapi.client.calendar.events.insert({
//     calendarId: "primary", // Utilisez 'primary' pour le calendrier par défaut de l'utilisateur
//     resource: event,
//   });

//   request.execute(function (event) {
//     console.log(
//       "Événement ajouté avec succès. ID de l'événement : " + event.id
//     );
//   });

//   request.then(null, function (error) {
//     console.error("Erreur lors de l'ajout de l'événement : " + error);
//   });
// }

// // Charger l'API client et authentifier l'utilisateur
// gapi.load("client", initClient);

//fonction envoi
// const { google } = require("googleapis");
// require("dotenv").config({ path: "C:/wamp/www/ims_beauty/.env.local" });

// const GOOGLE_PRIVATE_KEY = process.env.private_key.replace(/\\n/g, "\n");
// const GOOGLE_CLIENT_EMAIL = process.env.client_email;
// const SCOPES = ["https://www.googleapis.com/auth/calendar"];

// // Authentification avec les informations d'identification de service
// const auth = new google.auth.JWT(
//   GOOGLE_CLIENT_EMAIL,
//   null,
//   GOOGLE_PRIVATE_KEY,
//   SCOPES
// );

// // Création de l'objet Calendar
// const calendar = google.calendar({ version: "v3", auth });

// // Définir les détails de l'événement que vous souhaitez ajouter
// const event = {
//   summary: "angeline j'ai pas encore reussi ",
//   description: "Description de l'événement",
//   start: {
//     dateTime: "2023-11-27T10:00:00", // Format ISO 8601
//     timeZone: "Europe/Paris",
//   },
//   end: {
//     dateTime: "2023-11-27T12:00:00", // Format ISO 8601
//     timeZone: "Europe/Paris",
//   },
// };

// // Ajouter l'événement au calendrier
// calendar.events.insert(
//   {
//     calendarId: "karimryahi01@gmail.com", // "primary" fait référence au calendrier par défaut de l'utilisateur
//     resource: event,
//   },
//   (err, res) => {
//     if (err) {
//       console.error("Erreur lors de l'ajout de l'événement :", err);
//       return;
//     }
//     console.log(
//       "Événement ajouté avec succès. ID de l'événement :",
//       res.data.id
//     );
//   }
// );
