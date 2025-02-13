window.addEventListener("load", function () {
    var preloader = document.querySelector(".loader");
    var loginForm = document.querySelector(".box");
    var errorMessage = document.getElementById("errorMessage");

    // Show the preloader and hide form initially
    setTimeout(function () {
        preloader.style.display = "none"; // Hide the preloader after 3.8 seconds (3800 ms)
        loginForm.style.display = "block"; // Show the login form
        document.querySelector("body").style.overflow = "auto"; // Allow scrolling

        // Display error message shortly after the preloader is hidden
        if (errorMessage.innerHTML.trim() !== "") {
            setTimeout(function () {
                errorMessage.style.display = "block"; // Show error message after a short delay (200ms)
            }, 200); // Delay to show error message after the form is shown

            // Hide error message after 3 seconds of visibility
            setTimeout(function () {
                errorMessage.style.display = "none";
            }, 3200); // Error will be visible for 3 seconds after it's displayed
        }
    }, 4900 ); // Preloader hides after 3.8 seconds
});


// Function to hide the preloader and show the content when everything is loaded
window.addEventListener("load", function() {
    var preloader2 = document.querySelector(".preloader");
  
    setTimeout(function() {
      preloader2.style.display = "none";
      document.querySelector("body").style.overflow = "auto";
    }, 2000); 
  });
  
  