 function togglePasswordVisibility() {
            var password = document.getElementById("password");
            var confirmPassword = document.getElementById("confirmPassword");

            // Toggle visibility for the "Password" field
            toggleInputType(password);

            // Toggle visibility for the "Confirm Password" field
            toggleInputType(confirmPassword);
        }

        function toggleInputType(inputElement) {
            inputElement.type = (inputElement.type === "password") ? "text" : "password";
        }

        function sanitizeInput(inputElement) {
            // Remove special characters
            inputElement.value = inputElement.value.replace(/[^A-Za-z\s]/g, '');
        }

        function capitalizeEachWord(str) {
            return str.replace(/\b\w/g, function(char) {
                return char.toUpperCase();
            });
        }

        function applySentenceCase(inputElement) {
            var inputValue = inputElement.value;

            // Special case for "House No. & Street"
            if (inputElement.id === "house_no_street") {
                inputValue = capitalizeEachWord(inputValue);
            } else {
                // Regular sentence case for other fields
                var words = inputValue.split(/\s+/); // Split by whitespace
                words = words.map(function(word) {
                    return word.charAt(0).toUpperCase() + word.slice(1).toLowerCase();
                });
                inputValue = words.join(' ');
            }
            inputElement.value = inputValue;
        }

        function convertToUppercase(inputElement) {
            inputElement.value = inputElement.value.toUpperCase();
        }

        function sanitizeNumericInput(event) {
            var inputValue = event.target.value;
            // Replace any non-numeric characters with an empty string
            var numericValue = inputValue.replace(/[^0-9]/g, '');

            // Truncate to a maximum length of 11 characters
            event.target.value = numericValue.substring(0, 11);
        }
        function showProcessingAlert() {
            alert("Please wait while we are processing your registration and sending your verification code to your email.");
        }
        
