//Run this function on document ready
jQuery( document ).ready( function( $ ) {

   // Function to load masinary script
   $('#container').imagesLoaded( function(){
    $('#container').masonry({
     itemSelector: '.brick',
     isAnimated: true,
     isFitWidth: true,
     columnWidth: 200
    });
  });


   // Function to open popup and display comments in it.
   $('.brick').click(function() {

    $('#image_id').val($(this).attr('id'));
    $('#detail-link').attr('href', instadetail_url+'?id='+$(this).attr('id'));
   		
   		jQuery.ajax({
   			url: instadetail_url+'?func=topComments&ajax=true&img_id='+$(this).attr('id'), 
   			success: function(data){
          var html = '';

	         if (data.length > 0){

              for (var i = 0; i < data.length; i++) {
                
                width = Math.round((data[i].rating/5)*100);
                html += '<div id="comment"><span class="com-name"><b>'+data[i].name+'</b></span><div class="rating_bar"><div class="rating" style="width:'+width+'%;"></div></div><p class="com-comment">'+data[i].comment+'</p></div>';
                
              };

           } else {

              html = '<div id="comment"><span class="com-name">No comments yet, be the first one to comment.</span><div></div><p></p></div>';

           }
           $('#list-comments').html('<p style="margin-bottom: 0;">Comments</p>'+html); 
	    	}
	    });

        $('#pop_up').bPopup();
    });

    // Function to save comments on the popup
    $('#save_comment_ajax').submit(function(e){
        e.preventDefault();
        var $rateYo = $("#rateYo").rateYo();

        jQuery.ajax({
          url: instadetail_url+'?func=submitComments&ajax=true',
          method: 'POST',
          data: $(this).serialize(),
          success: function(data){
            //$(this).reset();
            width = Math.round(($('#rating').val()/5)*100);
            html = '<div id="comment"><span class="com-name">'+$('#name').val()+'</span><div class="rating_bar"><div class="rating" style="width:'+width+'%;"></div></div><p class="com-comment">'+$('#user-comment').val()+'</p></div>'
            $('#name').val(''); $('#user-comment').val(''); $rateYo.rateYo("rating", 0);
            $('#list-comments').append(html);
          }
        });

    });

    // Function to save comments on the details page
    $('#save_comment').submit(function(e){
        e.preventDefault();
        
        jQuery.ajax({
          url: instadetail_url+'?func=submitComments&ajax=true',
          method: 'POST',
          data: $(this).serialize(),
          success: function(data){
            location.reload();
          }
        });

    });

    // initiation of rating selector
    $("#rateYo").rateYo({
      starWidth: "20px",
      fullStar: true
    });

    // function to get rating selected
    $("#rateYo").click(function(){
      var $rateYo = $("#rateYo").rateYo();
      var rating = $rateYo.rateYo("rating");
      $('#rating').val(rating);
    });

});