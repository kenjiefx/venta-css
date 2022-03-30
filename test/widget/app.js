strawberry.create('app',function(){
  setTimeout(function(){
    $(".loading").fadeOut();
    $("#loader").html('');
    $("#main").fadeIn();
  },2000);
});

app.factory('presets',function(){
  return {}
});
