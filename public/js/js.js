$(document).ready(function(){
  if($(window).width() < 610){
    $('.nieuw_film_slider').slick({
      infinite: true,
      slidesToShow: 1,
      slidesToScroll: 1,
      dots: true,
      speed: 600,
      responsive: true
    });
  }else if($(window).width() < 1000 && $(window).width() > 610){
    $('.nieuw_film_slider').slick({
      infinite: true,
      slidesToShow: 2,
      slidesToScroll: 1,
      dots: true,
      speed: 600,
      responsive: true
    });
  }else{
    $('.nieuw_film_slider').slick({
      infinite: true,
      slidesToShow: 3,
      slidesToScroll: 3,
      dots: true,
      speed: 600,
      responsive: true
    });
  }
  // $('.afleverDatum').hide();
  $('.nee').click(function(){
    $('.afleverDatum').show("fast");
    $('.vraag').hide("fast");
  });
});
