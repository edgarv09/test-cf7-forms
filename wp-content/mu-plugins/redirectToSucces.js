var wpcf7Elm = document.querySelector( '.wpcf7' );

wpcf7Elm.addEventListener( 'wpcf7submit', succesSubmitEvent, false );



function succesSubmitEvent (event) {
  // body...
  //
  let detail = event.detail;
  if ( '25' == detail.contactFormId ) {
    alert( "The contact form ID is 123." );
  // do something productive
  }
  console.log(event);
  console.table(event);

  window.location.href = `../success/?form_id=${detail.contactFormId}&data_id=${detail.containerPostId}`;
}

