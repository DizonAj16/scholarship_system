function updateStepper() {
  const steps = $(".wizard-flow-chart .step span");
  const currentIndex = $("section").index($("section:not(.display-none)"));

  // Remove previous highlights
  steps.removeClass("fill current");

  steps.each((i, el) => {
    if (i < currentIndex) {
      $(el).addClass("fill"); // previous steps filled
    } else if (i === currentIndex) {
      $(el).addClass("current"); // current step highlighted
    }
  });
}

function validate(button) {
  var wizardSection = $(button).closest("section");
  var valid = true;

  // Remove any existing error messages first
  $(wizardSection).find(".error-message").remove();

  // Reset styles for all inputs, selects, and textareas
  $(wizardSection)
    .find("input, select, textarea")
    .css("border", "1px solid #9a9a9a");

  // Validate inputs, selects, and textareas
  $(wizardSection)
    .find("input, select, textarea")
    .each(function () {
      var value = $(this).val();

      // For selects, ensure a valid option is selected
      if ($(this).is("select") && (value === "" || value === null)) {
        valid = false;
        $(this).css("border", "red 1px solid");
        $(this).after(
          '<div class="error-message" style="color:red;font-size:12px;margin-top:2px;">This field is required</div>'
        );
      }

      // For other inputs and textareas
      if (!$(this).is("select") && (value === "" || value === null)) {
        valid = false;
        $(this).css("border", "red 1px solid");
        $(this).after(
          '<div class="error-message" style="color:red;font-size:12px;margin-top:2px;">This field is required</div>'
        );
      }
    });

  // Proceed if valid
  if (valid) {
    showNextWizardSection(wizardSection);
  }
}

function showNextWizardSection(wizardSection) {
  $("section").addClass("display-none"); // Hide all sections
  $(wizardSection).next("section").removeClass("display-none"); // Show next section
  updateStepper(); // Highlight step numbers correctly
  scrollToTop();
}

function showPrevious(button) {
  var wizardSection = $(button).closest("section");
  $("section").addClass("display-none"); // Hide all sections
  $(wizardSection).prev("section").removeClass("display-none"); // Show previous section
  updateStepper(); // Highlight step numbers correctly
  scrollToTop();
}
function validateCheckout() {
  var valid = true;

  // Reset styles for the notes field
  $("#notes").css("border", "1px solid #9a9a9a");

  // Validate the notes textarea
  if ($("#notes").val() == "") {
    $("#notes").css("border", "red 1px solid");
    valid = false;
  }

  return valid;
}

function scrollToTop() {
  window.scrollTo({ top: 0, behavior: "smooth" });
}
