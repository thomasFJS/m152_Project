
$(document).ready(function(){
  $('.modal').modal();
  $('.carousel-slider').carousel({fullWidth: true, padding:0},setTimeout(autoplay, 4500));
});

function autoplay() {
  $('.carousel').carousel('next');
  setTimeout(autoplay, 7500);
   }