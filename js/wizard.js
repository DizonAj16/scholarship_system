function validate(button) {
    var wizardSection = $(button).closest("section");
    var valid = true;

    // Reset styles for all inputs, selects, and textareas
    $(wizardSection).find("input, select, textarea").css("border", "1px solid #9a9a9a");

    // Validate inputs, selects, and textareas
    $(wizardSection).find("input, select, textarea").each(function () {
        var value = $(this).val();

        // For selects, ensure a valid option is selected
        if ($(this).is("select") && (value === "" || value === null)) {
            valid = false;
            $(this).css("border", "red 1px solid");
        }

        // For other inputs and textareas
        if (!$(this).is("select") && (value === "" || value === null)) {
            valid = false;
            $(this).css("border", "red 1px solid");
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
    $(".wizard-flow-chart span.fill").next("span").addClass("fill"); // Update flow chart
}

function showPrevious(button) {
    var wizardSection = $(button).closest("section");
    $("section").addClass("display-none"); // Hide all sections
    $(wizardSection).prev("section").removeClass("display-none"); // Show previous section
    $(".wizard-flow-chart span.fill").last().removeClass("fill"); // Update flow chart
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
