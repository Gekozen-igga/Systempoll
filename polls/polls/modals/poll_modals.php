<!-- modals/poll_modal.php -->
<div id="pollModal" class="modal">
    <div class="modal-content">
        <span class="close-button">&times;</span>
        <h2>Poll Title</h2>
        <p>Poll Question Here</p>
        <form method="POST" action="vote.php">
            <input type="radio" name="option_id" value="1"> Option 1<br>
            <input type="radio" name="option_id" value="2"> Option 2<br>
            <button type="submit">Vote</button>
        </form>
    </div>
</div>

<!-- Add this script in your main layout -->
<script>
    const modal = document.getElementById("pollModal");
    const closeButton = document.querySelector(".close-button");

    // Function to open modal
    function openModal(pollTitle, pollQuestion, options) {
        modal.querySelector('h2').innerText = pollTitle;
        modal.querySelector('p').innerText = pollQuestion;

        const form = modal.querySelector('form');
        form.innerHTML = ''; // Clear previous options
        options.forEach(option => {
            form.innerHTML += `<input type="radio" name="option_id" value="${option.id}"> ${option.text}<br>`;
        });
        modal.style.display = "block";
    }

    closeButton.addEventListener("click", () => {
        modal.style.display = "none";
    });

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>
