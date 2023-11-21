// const apiKey = 'ta_cle_api';
// const calendarId = 'ton_calendar_id';

// const startDate = new Date('2023-11-17T00:00:00Z');
// const endDate = new Date('2023-11-17T23:59:59Z');

// fetch('https://www.googleapis.com/calendar/v3/calendars/${calendarId}/events?key=${apiKey}&timeMin=${startDate.toISOString()}&timeMax=${endDate.toISOString()}')
//   .then(response => {
//     if (!response.ok) {
//       throw new Error('La requête a échoué');
//     }
//     return response.json();
//   })
//   .then(data => {
//     const events = data.items;

//     const busyTimes = events.map(event => ({
//       start: new Date(event.start.dateTime  event.start.date),
//       end: new Date(event.end.dateTime  event.end.date)
//     }));

//     const availableTimes = [];
//     let currentTime = startDate;
//     const interval = 60;

//     while (currentTime < endDate) {
//       let busy = false;
//       for (const busyTime of busyTimes) {
//         if (currentTime >= busyTime.start && currentTime < busyTime.end) {
//           busy = true;
//           break;
//         }
//       }
//       if (!busy) {
//         availableTimes.push(new Date(currentTime));
//       }
//       currentTime = new Date(currentTime.getTime() + interval * 60000);
//     }

//     const availableTimesList = document.getElementById('availableTimesList');
//     availableTimes.forEach(time => {
//       const listItem = document.createElement('li');
//       listItem.textContent = time.toLocaleString();
//       availableTimesList.appendChild(listItem);
//     });
//   })
//   .catch(error => {
//     console.error('Erreur:', error);
//   });
// <ul id="availableTimesList"></ul>
