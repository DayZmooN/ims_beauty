// const { google } = require("googleapis");
// require("dotenv").config({ path: "C:/wamp/www/ims_beauty/.env.local" });

// const GOOGLE_PRIVATE_KEY = process.env.private_key.replace(/\\n/g, "\n");
// const GOOGLE_CLIENT_EMAIL = process.env.client_email;
// const SCOPES = ["https://www.googleapis.com/auth/calendar"];

// const auth = new google.auth.JWT(
//   GOOGLE_CLIENT_EMAIL,
//   null,
//   GOOGLE_PRIVATE_KEY,
//   SCOPES
// );

// const calendar = google.calendar({ version: "v3", auth });

// async function createEvent(event) {
//   try {
//     const response = await calendar.events.insert({
//       calendarId: "karimryahi01@gmail.com",
//       requestBody: event,
//     });
//     console.log("Événement créé :", response.data);
//   } catch (error) {
//     console.error("Erreur lors de la création de l’événement :", error);
//   }
// }

// const sampleEvent = {
//   summary: "Réunion avec le client",
//   location: "Bureau central",
//   description: "Discussion sur le projet X",
//   start: {
//     dateTime: "2023-11-13T10:00:00-04:00",
//     timeZone: "Europe/Paris",
//   },
//   end: {
//     dateTime: "2023-11-13T11:00:00-04:00",
//     timeZone: "Europe/Paris",
//   },
// };

// async function listEvents() {
//   try {
//     const response = await calendar.events.list({
//       calendarId: "karimryahi01@gmail.com",
//       timeMin: new Date().toISOString(),
//       timeMax: new Date(
//         new Date().setDate(new Date().getDate() + 7)
//       ).toISOString(),
//       singleEvents: true,
//       orderBy: "startTime",
//     });

//     const events = response.data.items;
//     const availableSlots = findAvailableSlots(events);
//     displayCalendarSlots(availableSlots);
//   } catch (error) {
//     console.error("Erreur lors de la récupération des événements :", error);
//   }
// }

// function findAvailableSlots(events) {
//   let availableSlots = [];
//   let lastEndTime = new Date();

//   events.forEach((event) => {
//     const eventStart = new Date(event.start.dateTime);
//     if (eventStart > lastEndTime) {
//       availableSlots.push({ start: lastEndTime, end: eventStart });
//     }
//     lastEndTime = new Date(event.end.dateTime);
//   });

//   const endOfDay = new Date();
//   endOfDay.setHours(23, 59, 59, 999);
//   if (lastEndTime < endOfDay) {
//     availableSlots.push({ start: lastEndTime, end: endOfDay });
//   }

//   return availableSlots;
// }

// function displayCalendarSlots(slots) {
//   const slotsContainer = document.getElementById("calendar-slots-container");
//   slots.forEach((slot) => {
//     const slotElement = document.createElement("div");
//     slotElement.textContent = `Disponible de ${slot.start.toLocaleTimeString()} à ${slot.end.toLocaleTimeString()}`;
//     slotsContainer.appendChild(slotElement);
//   });
// }

// createEvent(sampleEvent);
// listEvents();
///////////////////////////////////////////////////////////////////
// JavaScript
// function handleClientLoad() {
//   gapi.load("client", initClient);
// }

// function initClient() {
//   gapi.client
//     .init({
//       apiKey: "AIzaSyAJOE5ji9Sz-bj7ksBG8kWV9BXc1y_wk7E",
//       discoveryDocs: [
//         "https://www.googleapis.com/discovery/v1/apis/calendar/v3/rest",
//       ],
//     })
//     .then(function () {
//       listEvents();
//     });
// }

// function listEventsRdv() {
//   gapi.client.calendar.events
//     .list({
//       calendarId: "karimryahi01@gmail.com",
//       timeMin: new Date().toISOString(),
//       showDeleted: false,
//       singleEvents: true,
//       maxResults: 10,
//       orderBy: "startTime",
//     })
//     .then(function (response) {
//       const events = response.result.items;
//       displayEvents(events);
//     });
// }
// Fonction pour créer un événement dans Google Calendar
// document.addEventListener("DOMContentLoaded", function () {
//   // Liste des créneaux horaires disponibles (à personnaliser)
//   const availableSlots = [
//     {
//       start: new Date("2023-11-20T09:00:00"),
//       end: new Date("2023-11-20T11:30:00"),
//     },
//     {
//       start: new Date("2023-11-20T14:00:00"),
//       end: new Date("2023-11-20T18:00:00"),
//     },
//     // Ajoutez d'autres créneaux horaires disponibles ici
//   ];

//   // Affichez les créneaux horaires disponibles à l'utilisateur
//   const slotsContainer = document.getElementById("slots-container");
//   availableSlots.forEach((slot, index) => {
//     const slotElement = document.createElement("div");
//     slotElement.textContent =
//       slot.start.toLocaleString() + " - " + slot.end.toLocaleString();

//     // Ajoutez un bouton pour sélectionner ce créneau
//     const selectButton = document.createElement("button");
//     selectButton.textContent = "Sélectionner";
//     selectButton.addEventListener("click", () => {
//       // Lorsque l'utilisateur sélectionne un créneau, enregistrez-le dans votre agenda ici
//       const selectedSlot = availableSlots[index];
//       createCalendarEvent(selectedSlot);
//     });

//     slotElement.appendChild(selectButton);
//     slotsContainer.appendChild(slotElement);
//   });

//   // Fonction pour enregistrer le rendez-vous dans votre agenda Google
//   function createCalendarEvent(selectedSlot) {
//     // Utilisez la Google Calendar API pour créer un événement avec le résumé "Rendez-vous au temps qui a été choisi"
//     // Assurez-vous d'ajouter la logique pour envoyer la demande au serveur Symfony depuis ici
//     const apiKey = "AIzaSyAJOE5ji9Sz-bj7ksBG8kWV9BXc1y_wk7E";
//     gapi.load("client:auth2", () => {
//       gapi.client.init({
//         apiKey: apiKey,
//         clientId:
//           "207164739117-trp0er85ho9ggk8dqnpd1e3jjl1pk5hb.apps.googleusercontent.com",
//         discoveryDocs: [
//           "https://www.googleapis.com/discovery/v1/apis/calendar/v3/rest",
//         ],
//         scope: "https://www.googleapis.com/auth/calendar",
//       });

//       gapi.client.load("calendar", "v3", () => {
//         const startDate = selectedSlot.start.toISOString();
//         const endDate = selectedSlot.end.toISOString();

//         const event = {
//           summary: "Rendez-vous au temps qui a été choisi",
//           start: {
//             dateTime: startDate,
//             timeZone: "Europe/Paris", // Remplacez par votre fuseau horaire
//           },
//           end: {
//             dateTime: endDate,
//             timeZone: "Europe/Paris", // Remplacez par votre fuseau horaire
//           },
//         };

//         gapi.client.calendar.events
//           .insert({
//             calendarId: "karimryahi01@gmail.com", // ID de votre calendrier Google
//             resource: event,
//           })
//           .then((response) => {
//             // Événement créé avec succès
//             console.log("Événement créé :", response);
//             alert("Événement créé avec succès !");
//           })
//           .catch((error) => {
//             // Erreur lors de la création de l'événement
//             console.error("Erreur lors de la création de l'événement :", error);
//             alert(
//               "Erreur lors de la création de l'événement. Veuillez réessayer."
//             );
//           });
//       });
//     });
//   }
// });

// document.addEventListener("DOMContentLoaded", function () {
//   // Clé d'API Google
//   const apiKey = "AIzaSyAJOE5ji9Sz-bj7ksBG8kWV9BXc1y_wk7E"; // Remplacez par votre clé d'API Google

//   // ID du calendrier Google que vous souhaitez afficher
//   const calendarId = "karimryahi01@gmail.com"; // Remplacez par l'ID de votre calendrier Google

//   // Créez un objet de configuration de l'API Google Calendar
//   gapi.load("client:auth2", () => {
//     gapi.client.init({
//       apiKey: apiKey,
//       clientId:
//         "207164739117-trp0er85ho9ggk8dqnpd1e3jjl1pk5hb.apps.googleusercontent.com",
//       discoveryDocs: [
//         "https://www.googleapis.com/discovery/v1/apis/calendar/v3/rest",
//       ],
//       scope: "https://www.googleapis.com/auth/calendar.readonly",
//     });

//     // Chargez le client de l'API Calendar
//     gapi.client.load("calendar", "v3", () => {
//       // Définissez la période de temps pour laquelle vous souhaitez afficher les disponibilités
//       const now = new Date();
//       const oneWeekLater = new Date();
//       oneWeekLater.setDate(oneWeekLater.getDate() + 7); // Afficher les disponibilités pour la semaine à venir

//       // Requête pour récupérer les événements du calendrier
//       gapi.client.calendar.events
//         .list({
//           calendarId: calendarId,
//           timeMin: now.toISOString(),
//           timeMax: oneWeekLater.toISOString(),
//           maxResults: 10, // Limite le nombre d'événements à récupérer
//           orderBy: "startTime",
//           singleEvents: true,
//         })
//         .then((response) => {
//           const events = response.result.items;
//           const availabilitySlots = [];

//           // Parcourez les événements pour déterminer les disponibilités
//           let currentSlotStart = now;
//           events.forEach((event) => {
//             const eventStart = new Date(event.start.dateTime);
//             const eventEnd = new Date(event.end.dateTime);

//             // Si l'événement chevauche le créneau actuel, mettez à jour le début du créneau
//             if (
//               eventStart <= currentSlotStart &&
//               eventEnd >= currentSlotStart
//             ) {
//               currentSlotStart = eventEnd;
//             } else {
//               // Sinon, ajoutez le créneau actuel aux disponibilités
//               availabilitySlots.push({
//                 start: new Date(currentSlotStart),
//                 end: new Date(eventStart),
//               });

//               // Mettez à jour le début du créneau actuel
//               currentSlotStart = eventEnd;
//             }
//           });

//           // Si le dernier créneau se termine après "oneWeekLater", ajustez-le
//           if (currentSlotStart < oneWeekLater) {
//             availabilitySlots.push({
//               start: new Date(currentSlotStart),
//               end: new Date(oneWeekLater),
//             });
//           }

//           // Affichez les créneaux de disponibilité sur votre page
//           const availabilityContainer = document.getElementById(
//             "availability-container"
//           );
//           availabilitySlots.forEach((slot) => {
//             const slotElement = document.createElement("div");
//             slotElement.textContent = `${slot.start.toLocaleString()} - ${slot.end.toLocaleString()}`;
//             availabilityContainer.appendChild(slotElement);
//           });
//         })
//         .catch((error) => {
//           console.error(
//             "Erreur lors de la récupération des événements :",
//             error
//           );
//         });
//     });
//   });
// });

///////////////////////////////////////////////////////////////////////////////////////////
/* version ameliorer */
///////////////////////////////////////////////////////////////////////////////////////////

// document.addEventListener("DOMContentLoaded", function () {
// Initialisation de l'API Google
//   gapi.load("client:auth2", () => {
//     gapi.client.init({
//       apiKey: "AIzaSyAJOE5ji9Sz-bj7ksBG8kWV9BXc1y_wk7E",
//       clientId:
//         "207164739117-trp0er85ho9ggk8dqnpd1e3jjl1pk5hb.apps.googleusercontent.com",
//       discoveryDocs: [
//         "https://www.googleapis.com/discovery/v1/apis/calendar/v3/rest",
//       ],
//       scope: "https://www.googleapis.com/auth/calendar",
//     });

//     gapi.client.load("calendar", "v3", () => {
//       const now = new Date();
//       const oneWeekLater = new Date(now);
//       oneWeekLater.setDate(now.getDate() + 7);

//       gapi.client.calendar.events
//         .list({
//           calendarId: "karimryahi01@gmail.com",
//           timeMin: now.toISOString(),
//           timeMax: oneWeekLater.toISOString(),
//           maxResults: 100,
//           orderBy: "startTime",
//           singleEvents: true,
//         })
//         .then((response) => {
//           const events = response.result.items;

//           // Récupérer toutes les durées des services et calculer les créneaux disponibles pour chacun
//           const serviceDurations = document.querySelectorAll(
//             "[data-service-duration]"
//           );
//           serviceDurations.forEach((element) => {
//             let serviceDuration = parseInt(element.dataset.serviceDuration, 10);
//             displayAvailability(now, oneWeekLater, events, serviceDuration);
//           });
//         })
//         .catch((error) => {
//           console.error(
//             "Erreur lors de la récupération des événements :",
//             error
//           );
//         });
//     });
//   });
//   document.querySelector("form").addEventListener("submit", function (event) {
//     event.preventDefault();

//     const selectedDateInput = document.getElementById("selectedDate");
//     const selectedDate = new Date(selectedDateInput.value);

//     if (isNaN(selectedDate.getTime())) {
//       alert("Veuillez sélectionner une date valide.");
//       return;
//     }

//     const selectedSlot = document.querySelector(
//       'input[name="selectedTimeSlot"]:checked'
//     );
//     if (!selectedSlot) {
//       alert("Veuillez sélectionner un créneau.");
//       return;
//     }

//     const serviceId = selectedSlot.getAttribute("data-service-id"); // Ajout de l'attribut data-service-id
//     addEventToCalendar(selectedDate, serviceId);
//   });
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

// function appendSlotToContainer(slot, container) {
//   const slotLabel = document.createElement("label");
//   slotLabel.className = "slot-label";

//   const formattedStartDate = slot.start.toLocaleDateString("fr-FR", {
//     year: "numeric",
//     month: "long",
//     day: "numeric",
//     hour: "numeric",
//     minute: "numeric",
//     hour12: false,
//   });
//   const formattedEndDate = slot.end.toLocaleTimeString("fr-FR", {
//     hour: "numeric",
//     minute: "numeric",
//     hour12: false,
//   });

//   slotLabel.textContent = `${formattedStartDate} - ${formattedEndDate}`;

//   const slotRadio = document.createElement("input");
//   slotRadio.type = "radio";
//   slotRadio.name = "selectedTimeSlot";
//   slotRadio.value = slot.start.toISOString(); // Utilisez un format de date qui convient à votre backend

//   slotLabel.prepend(slotRadio);

//   container.appendChild(slotLabel);
// }

// document.querySelector("form").addEventListener("submit", function (event) {
//   event.preventDefault();

//   const selectedSlot = document.querySelector(
//     'input[name="selectedTimeSlot"]:checked'
//   );
//   if (!selectedSlot) {
//     alert("Veuillez sélectionner un créneau.");
//     return;
//   }

//   const slotValue = new Date(selectedSlot.value);
//   addEventToCalendar(slotValue);
// });

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

// function addEventToCalendar(startTime, serviceId) {
//   // Définir les détails de l'événement que vous souhaitez ajouter
//   const event = {
//     summary: serviceId, // Utilisez le serviceId comme summary
//     description: "Description de l'événement",
//     start: {
//       dateTime: startTime.toISOString(), // Utilisez la date de début passée en paramètre
//       timeZone: "Europe/Paris",
//     },
//     end: {
//       dateTime: new Date(startTime.getTime() + 60 * 60 * 1000).toISOString(), // Fin 1 heure après le début
//       timeZone: "Europe/Paris",
//     },
//   };

//   // Ajouter l'événement au calendrier
//   calendar.events.insert(
//     {
//       calendarId: "karimryahi01@gmail.com", // "primary" fait référence au calendrier par défaut de l'utilisateur
//       resource: event,
//     },
//     (err, res) => {
//       if (err) {
//         console.error("Erreur lors de l'ajout de l'événement :", err);
//         return;
//       }
//       console.log(
//         "Événement ajouté avec succès. ID de l'événement :",
//         res.data.id
//       );
//     }
//   );
// }

// // Exemple d'utilisation de la fonction
// const startTime = new Date("2023-11-27T10:00:00");
// const serviceId = "angeline j'ai pas encore reussi";

// addEventToCalendar(startTime, serviceId);

////////////////////////////////////////////////////////////////////////////////////////////////rip
// // Importez la bibliothèque Google API Client
// import { google } from "googleapis";

// // Configurez l'authentification avec vos identifiants
// require("dotenv").config({ path: "C:/wamp/www/ims_beauty/.env.local" });

// const GOOGLE_PRIVATE_KEY = process.env.private_key.replace(/\\n/g, "\n");
// const GOOGLE_CLIENT_EMAIL = process.env.client_email;
// const SCOPES = ["https://www.googleapis.com/auth/calendar"];

// const auth = new google.auth.JWT(
//   GOOGLE_CLIENT_EMAIL,
//   null,
//   GOOGLE_PRIVATE_KEY,
//   SCOPES
// );

// // Créez une instance de l'API Google Calendar
// const calendar = google.calendar({ version: "v3", auth });

// // Définissez la plage de temps pour laquelle vous souhaitez vérifier la disponibilité
// const timeMin = new Date(); // Date et heure minimales (actuelles)
// const timeMax = new Date(); // Date et heure maximales (1 semaine à partir de maintenant)
// timeMax.setDate(timeMax.getDate() + 7); // Ajoutez 7 jours à la date maximale

// // Définissez les paramètres de la requête pour obtenir les événements
// const requestParams = {
//   calendarId: "YOUR_CALENDAR_ID", // ID de votre calendrier Google
//   timeMin: timeMin.toISOString(),
//   timeMax: timeMax.toISOString(),
//   singleEvents: true,
//   orderBy: "startTime",
// };

// // Effectuez la requête pour obtenir les événements
// calendar.events.list(requestParams, (err, res) => {
//   if (err) {
//     console.error("Erreur lors de la récupération des événements :", err);
//     return;
//   }

//   const events = res.data.items;

//   if (events.length === 0) {
//     // Aucun événement trouvé, vous êtes disponible toute la semaine
//     console.log("Vous êtes disponible toute la semaine !");
//   } else {
//     // Il y a des événements dans la plage de temps spécifiée
//     // Vous pouvez maintenant analyser les événements pour déterminer les heures et les jours où vous êtes disponibles
//     console.log("Événements trouvés :", events);
//   }
// });
