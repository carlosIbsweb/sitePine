var j = jQuery.noConflict();
j(document).ready(function(){
    j(".button").click(function(){
        j.post("gimages.php",
        {
          name: "Donald Duck",
          city: "Duckburg"
        },
        function(data,status){
            alert("Data: " + data + "\nStatus: " + status);
        });
    });
});