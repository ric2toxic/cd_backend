$(document).ready(function(){

        $(".country_code").hide();
        $("#country_code").parent().hide();
        $("#contact").keypress(function(e){
            $(".country_code").show();
            $("#contact").parent().removeClass("col-xs-12").addClass("col-xs-8");           
        });

        $("#phone_number").keypress(function(e){
            $("#country_code").parent().show();
            $("#phone_number").parent().removeClass("col-xs-12").addClass("col-xs-8");

        });

        //youtube 
        var youtube = document.querySelectorAll( ".youtube" );
        for (var i = 0; i < youtube.length; i++) {
              // add the code here
              // thumbnail image source.
              var source = "https://img.youtube.com/vi/"+ youtube[i].dataset.embed +"/sddefault.jpg";

              // Load the image asynchronously
                var image = new Image();
                image.src = source;
                image.addEventListener( "load", function() {
                    youtube[ i ].appendChild( image );
                }( i ) );

                youtube[i].addEventListener( "click", function() {
         
                var iframe = document.createElement( "iframe" );
         
                    iframe.setAttribute( "frameborder", "0" );
                    iframe.setAttribute( "allowfullscreen", "" );
                    iframe.setAttribute( "src", "https://www.youtube.com/embed/"+ this.dataset.embed +"?rel=0&showinfo=0&autoplay=1" );
         
                    this.innerHTML = "";
                    this.appendChild( iframe );
            });
        }

        $(".scroll_effect").on('click', function(event) {
            target = $(this).data('target');
          // Make sure this.hash has a value before overriding default behavior
          if (target !== "") {
            // Prevent default anchor click behavior
            event.preventDefault();

            // Store hash
           

            // Using jQuery's animate() method to add smooth page scroll
            // The optional number (800) specifies the number of milliseconds it takes to scroll to the specified area
            $('html, body').animate({
              scrollTop: $(target).offset().top
            }, 800, function(){
         
              // Add hash (#) to URL when done scrolling (default click behavior)
              window.location.hash = target;
            });
          } // End if
        });

        


});

if((navigator.userAgent.indexOf("Opera") || navigator.userAgent.indexOf('OPR')) != -1 ) 
      {
          $(".browser").val('Opera');
      }
      else if(navigator.userAgent.indexOf("Chrome") != -1 )
      {
          $(".browser").val('Chrome');
      }
      else if(navigator.userAgent.indexOf("Safari") != -1)
      {
          $(".browser").val('Safari');
      }
      else if(navigator.userAgent.indexOf("Firefox") != -1 ) 
      {
           $(".browser").val('Firefox');
      }
      else if((navigator.userAgent.indexOf("MSIE") != -1 ) || (!!document.documentMode == true )) //IF IE > 10
      {
          $(".browser").val('IE'); 
      }  
      else 
      {
         $(".browser").val('unknown');
      }

function validateAccount(flag) {

    if (flag == "subscribe_email") {
         
         email =  $(".subscribe_email").val();

         if(email == '') {
            alert('Please enter email.');
            return false;
         }

         $('#business_email').val(email);
         $('.open_popup').click();
         return false;
    }

      if(flag == "subscribe") {
        var phone_number = $(".phone_number_subscribe").val().trim(); 
      } else {
        var phone_number = $("#phone_number").val().trim();  
      } 
      

      if (phone_number == '') {
          alert("Phone number is required field");
          return false;
      }

      else if (isNaN(phone_number)) {
          alert("Please input valid phone number");
          return false;
      }

      else if(phone_number.length != 10) {
          alert("Please input valid phone number");
          return false;
    } 
    
}

