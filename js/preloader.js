 // Show preloader and hide the form initially
        window.addEventListener("load", function () {
            var preloader = document.querySelector(".loader");
            var loginForm = document.querySelector(".box");

            setTimeout(function () {
                preloader.style.display = "none"; // Hide the preloader
                loginForm.style.display = "block"; // Show the login form
                document.querySelector("body").style.overflow = "auto"; // Allow scrolling
            }, 2010); // Wait 3.8 seconds

            // Display error message after 7.5 seconds, if there's an error
            var errorMessage = document.getElementById("errorMessage");
            if (errorMessage.innerHTML.trim() !== "") {
                setTimeout(function () {
                    errorMessage.style.display = "block"; // Show error message
                }, 5000); // Wait 7.5 seconds

                setTimeout(function () {
                    errorMessage.style.display = "none"; // Hide error message after 2 seconds
                }, 7000); // Wait 2 seconds after displaying
            }
        });


// Function to hide the preloader and show the content when everything is loaded
window.addEventListener("load", function() {
    var preloader2 = document.querySelector(".preloader");
  
    setTimeout(function() {
      preloader2.style.display = "none";
      document.querySelector("body").style.overflow = "auto";
    }, 1000); 
  });
  
  