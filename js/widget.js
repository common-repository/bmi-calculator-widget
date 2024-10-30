(function() {
   // Localize jQuery variable
   var jQuery;

   /******** Load jQuery if not present *********/
   if (window.jQuery === undefined || window.jQuery.fn.jquery !== '1.4.2') {
       var script_tag = document.createElement('script');
       script_tag.setAttribute("type","text/javascript");
       script_tag.setAttribute("src",
           "http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js");
       script_tag.onload = scriptLoadHandler;
       script_tag.onreadystatechange = function () { // Same thing but for IE
           if (this.readyState == 'complete' || this.readyState == 'loaded') {
               scriptLoadHandler();
           }
       };
       // Try to find the head, otherwise default to the documentElement
       (document.getElementsByTagName("head")[0] || document.documentElement).appendChild(script_tag);
   } else {
       // The jQuery version on the window is the one we want to use
       jQuery = window.jQuery;
       main();
   }

   /******** Called once jQuery has loaded ******/
   function scriptLoadHandler() {
       // Restore $ and window.jQuery to their previous values and store the
       // new jQuery in our local jQuery variable
       jQuery = window.jQuery.noConflict(true);
       // Call our main function
       main();
   }

   function main() {
       jQuery(document).ready(function($) {

           $('#widgetSubmit').click(function(e) {
              var form = $(this).closest('form');

              e.preventDefault();
              var widget_url = "http://www.calculatorpro.com/wp-content/plugins/calcs/ajax/calc.php?callback=?"
              $.getJSON(widget_url, $(this).closest('form').serialize(), function(data) {
                  form.find('.answer').html(data.answer);
                  form.find('#answerRow').fadeIn();
              });

              return false;
           });
      });
   }
})(); // We call our anonymous function immediately