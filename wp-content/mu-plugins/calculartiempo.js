let m = time.getMonth()
let d = time.getDay()
let h = time.getHours()
let m = time.getMinutes()
let s = time.getSeconds()

const time = Date.now();

const Y = time.getFullYear();
const M = time.getMonth();
const d = time.getDay();
const h = time.getHours();
const m = time.getMinutes();
const s = time.getSeconds();
const random = Math.ceil(Math.random() * 9999);
const codigo = `${Y}${M}${d}${h}${m}-${random}`;

document.getElementById("regForm")

animation_intime
$( document ).ready(function() {
  var currentTab = 0; // Current tab is set to be the first tab (0)
  showTab(currentTab); // Display the current tab

  const time = Date.now();

  const Y = time.getFullYear();
  const M = time.getMonth();
  const d = time.getDay();
  const h = time.getHours();
  const m = time.getMinutes();
  const s = time.getSeconds();
  const random = Math.ceil(Math.random() * 9999);
  const codigo = `${Y}${M}${d}${h}${m}-${random}`;

  console.log("este es mi codigo generado: ", codigo);

})

dateWithTimeZone("America/Bogota", Y, M, d, h, m, s)

dateWithTimeZone = (timeZone, year, month, day, hour, minute, second) => {
  let date = new Date(Date.UTC(year, month, day, hour, minute, second));

  let utcDate = new Date(date.toLocaleString('en-US', { timeZone: "UTC" }));
  let tzDate = new Date(date.toLocaleString('en-US', { timeZone: timeZone }));
  let offset = utcDate.getTime() - tzDate.getTime();

  date.setTime( date.getTime() + offset );

  return date;
};


jQuery(document).ready(function() {
  var currentTab = 0; // Current tab is set to be the first tab (0)
  showTab(currentTab); // Display the current tab

  const time = new Date(Date.now());

  const Y = time.getFullYear();
  const M = time.getMonth();
  const d = time.getDay();
  const h = time.getHours();
  const m = time.getMinutes();
  const s = time.getSeconds();
  const random = Math.ceil(Math.random() * 9999);
  const codigo = `${Y}${M}${d}${h}${m}-${random}`;

  console.log("este es mi codigo generado: ", codigo);

  const submission_form = document.getElementById("submission_id");
  submission_form.setAttribute('value', codigo);


});

