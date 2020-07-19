jQuery(document).ready(function() {

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
