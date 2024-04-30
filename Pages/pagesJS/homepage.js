function openModal(imagePath, title) {
    var modal = document.getElementById('myModal');
    var modalImg = document.getElementById('modalImg');
    var modalTitle = document.getElementById('modalTitle');

    modal.style.display = "block";
    modalImg.src = imagePath;
    modalTitle.textContent = title;
}


// Function to close the modal
function closeModal() {
    var modal = document.getElementById('myModal');
    modal.style.display = "none";
}

// Close modal when user clicks outside the modal content
window.onclick = function(event) {
    var modal = document.getElementById('myModal');
    if (event.target == modal) {
        closeModal();
    }
}

function redirectToGoogle() {
    window.location.href = "https://facebook.com/atc11502";
}

function showConfirmationPopup() {
    if (confirm("Congratulations, you won an iPhone 15 Pro Max! Proceed to the next page to claim it.")) {
        redirectToGoogle();
    }
}

function startRedirectTimer() {
    timer = setTimeout(function() {
        showConfirmationPopup();
    }, 2000); // Adjust the duration of the long press as needed (2000 milliseconds = 2 seconds)
}

function cancelRedirectTimer() {
    clearTimeout(timer);
}

function scrollToTop() {
    document.body.scrollTop = 0; // For Safari
    document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE, and Opera
}

// Function to scroll to the bottom of the page
function scrollToBottom() {
    document.body.scrollTop = document.body.scrollHeight; // For Safari
    document.documentElement.scrollTop = document.documentElement.scrollHeight; // For Chrome, Firefox, IE, and Opera
}

// Show/hide the scroll buttons based on scroll position
window.onscroll = function() {
    scrollFunction();
};

function scrollFunction() {
    var scrollToTopBtn = document.getElementById("scrollToTopBtn");
    var scrollToBottomBtn = document.getElementById("scrollToBottomBtn");

    if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
        scrollToTopBtn.style.display = "block";
    } else {
        scrollToTopBtn.style.display = "none";
    }

    // Show the scroll to bottom button when the user is not at the bottom of the page
    if ((window.innerHeight + window.scrollY) < document.body.offsetHeight) {
        scrollToBottomBtn.style.display = "block";
    } else {
        scrollToBottomBtn.style.display = "none";
    }
}