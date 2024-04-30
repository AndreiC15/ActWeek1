document.addEventListener("DOMContentLoaded", function () {
  var inputFields = document.querySelectorAll("input");

  inputFields.forEach(function (inputField, index) {
    inputField.addEventListener("keypress", function (event) {
      if (event.key === "Enter") {
        event.preventDefault();
        var nextIndex = (index + 1) % inputFields.length;
        inputFields[nextIndex].focus();
      }
    });
  });
});

function myFunction() {
  var x = document.getElementById("password");
  if (x.type === "password") {
    x.type = "text";
  } else {
    x.type = "password";
  }
}

function capitalizeEachWord(str) {
  return str.replace(/\b\w/g, function (char) {
    return char.toUpperCase();
  });
}

function sanitizeInput(inputElement) {
  // Remove special characters
  inputElement.value = inputElement.value.replace(/[^A-Za-z\s]/g, "");
}

function applySentenceCase(inputElement) {
  var inputValue = inputElement.value;

  // Special case for "House No. & Street"
  if (inputElement.id === "house_no_street") {
    inputValue = capitalizeEachWord(inputValue);
  } else {
    // Regular sentence case for other fields
    var words = inputValue.split(/\s+/); // Split by whitespace
    words = words.map(function (word) {
      return word.charAt(0).toUpperCase() + word.slice(1).toLowerCase();
    });
    inputValue = words.join(" ");
  }

  inputElement.value = inputValue;
}

function convertToUppercase(inputElement) {
  inputElement.value = inputElement.value.toUpperCase();
}

function sanitizeNumericInput(event) {
  var inputValue = event.target.value;
  // Replace any non-numeric characters with an empty string
  var numericValue = inputValue.replace(/[^0-9]/g, "");

  // Truncate to a maximum length of 11 characters
  event.target.value = numericValue.substring(0, 11);
}
function redirectToWebcam() {
  window.location.href = "webcam.php";
}
