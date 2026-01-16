
<!-- Modal for Upload -->
<div id="uploadModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeModal">&times;</span>
            <h2>Upload Recipients</h2>
            <form action="upload_csv.php" method="post" enctype="multipart/form-data">
                <input type="file" name="csv_file" accept=".csv" required>
                <br>
                <button type="submit">Upload</button>
            </form>
        </div>
    </div>

    <script>
        // Modal handling
        const modal = document.getElementById("uploadModal");
        const uploadLink = document.getElementById("upload-link");
        const closeModal = document.getElementById("closeModal");

        // Open the modal
        uploadLink.addEventListener("click", () => {
            modal.style.display = "flex";
        });

        // Close the modal
        closeModal.addEventListener("click", () => {
            modal.style.display = "none";
        });

        // Close modal when clicking outside of it
        window.addEventListener("click", (event) => {
            if (event.target === modal) {
                modal.style.display = "none";
            }
        });
    </script>
    <!-- Modal for Send Email -->
    <div id="sendEmailModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeEmailModal">&times;</span>
            <h2>Send Emails</h2>
            <form id="emailForm" enctype="multipart/form-data">
                <input type="text" name="subject" placeholder="Subject" required>
                <textarea name="body" rows="5" placeholder="Body" required></textarea>
                <input type="file" name="attachment" accept=".pdf, .docx, .png, .jpg">
                <button type="submit">Send</button>
            </form>
        </div>
    </div>

    <!-- Modal for Sending Status -->
    <div id="statusModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeStatusModal">&times;</span>
            <h2>Sending Status</h2>
            <div id="statusContent"></div>
        </div>
    </div>

    <script>
        const emailModal = document.getElementById("sendEmailModal");
        const emailLink = document.getElementById("send-email-link");
        const closeEmailModal = document.getElementById("closeEmailModal");
        const emailForm = document.getElementById("emailForm");

        const statusModal = document.getElementById("statusModal");
        const closeStatusModal = document.getElementById("closeStatusModal");
        const statusContent = document.getElementById("statusContent");

        // Open the "Send Email" modal
        emailLink.addEventListener("click", () => {
            emailModal.style.display = "flex";
        });

        // Close the "Send Email" modal
        closeEmailModal.addEventListener("click", () => {
            emailModal.style.display = "none";
        });

        // Close the "Sending Status" modal
        closeStatusModal.addEventListener("click", () => {
            statusModal.style.display = "none";
        });

        // Close modals when clicking outside
        window.addEventListener("click", (event) => {
            if (event.target === emailModal) emailModal.style.display = "none";
            if (event.target === statusModal) statusModal.style.display = "none";
        });

        // Handle form submission
        emailForm.addEventListener("submit", (e) => {
            e.preventDefault();
            const formData = new FormData(emailForm);

            // Close the "Send Email" modal
            emailModal.style.display = "none";

            // Show the "Sending Status" modal
            statusContent.innerHTML = "Starting to send emails...<br>";
            statusModal.style.display = "flex";

            // Start sending emails
            fetch("send_bulk_email.php", {
                method: "POST",
                body: formData,
            })
                .then((response) => {
                    const reader = response.body.getReader();
                    const decoder = new TextDecoder();
                    function read() {
                        reader.read().then(({ done, value }) => {
                            if (done) return; // All data has been read
                            const chunk = decoder.decode(value);
                            statusContent.innerHTML += chunk + "<br>";
                            read(); // Continue reading
                        });
                    }
                    read();
                })
                .catch(() => {
                    statusContent.innerHTML += "An error occurred.";
                });
        });

    </script>
    <!-- Modal for Viewing Recipients -->
    <div id="viewRecipientsModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeRecipientsModal">&times;</span>
            <h2>Recipient List</h2>
            <div id="recipientsContent">
                <!-- Recipients table will be dynamically loaded here -->
            </div>
        </div>
    </div>

    <script>
        const viewRecipientsModal = document.getElementById("viewRecipientsModal");
        const viewRecipientsLink = document.getElementById("view_recipients");
        const closeRecipientsModal = document.getElementById("closeRecipientsModal");
        const recipientsContent = document.getElementById("recipientsContent");

        // Open the "View Recipients" modal
        viewRecipientsLink.addEventListener("click", () => {
            // Show the modal
            viewRecipientsModal.style.display = "flex";

            // Fetch recipient data dynamically
            recipientsContent.innerHTML = "Loading recipients...";
            fetch("view_recipients.php")
                .then((response) => response.text())
                .then((html) => {
                    recipientsContent.innerHTML = html;
                })
                .catch(() => {
                    recipientsContent.innerHTML = "Failed to load recipients.";
                });
        });

        // Close the "View Recipients" modal
        closeRecipientsModal.addEventListener("click", () => {
            viewRecipientsModal.style.display = "none";
        });

        // Close modals when clicking outside
        window.addEventListener("click", (event) => {
            if (event.target === viewRecipientsModal) {
                viewRecipientsModal.style.display = "none";
            }
        });

    </script>


<div id="smtp-modal" class="modal">
        <div class="modal-content">
            <span class="close" id="close-smtp-modal">&times;</span>
            <h3>SMTP Server Details</h3>
            <form method="post" action="index.php">
                <input type="text" name="smtp_host" placeholder="SMTP Host"
                    value="<?php echo $smtp_settings['smtp_host'] ?? ''; ?>" required>
                <input type="number" name="smtp_port" placeholder="SMTP Port"
                    value="<?php echo $smtp_settings['smtp_port'] ?? ''; ?>" required>
                <input type="text" name="smtp_user" placeholder="SMTP Username"
                    value="<?php echo $smtp_settings['smtp_user'] ?? ''; ?>" required>
                <input type="password" name="smtp_pass" placeholder="SMTP Password"
                    value="<?php echo $smtp_settings['smtp_pass'] ?? ''; ?>" required>
                <button type="submit">Save</button>
            </form>
        </div>
    </div>

    <script>
        // Get modal and buttons
        const smtpModal = document.getElementById('smtp-modal');
        const editSmtpBtn = document.getElementById('edit-smtp-btn');
        const closeSmtpModal = document.getElementById('close-smtp-modal');

        // Open modal
        editSmtpBtn.addEventListener('click', () => {
            smtpModal.style.display = 'flex';
        });

        // Close modal
        closeSmtpModal.addEventListener('click', () => {
            smtpModal.style.display = 'none';
        });

        // Close modal when clicking outside the content
        window.addEventListener('click', (event) => {
            if (event.target === smtpModal) {
                smtpModal.style.display = 'none';
            }
        });

        // Refresh SMTP status
        document.getElementById('refresh-status').addEventListener('click', function () {
            fetch('check_smtp_status.php')
                .then(response => response.text())
                .then(status => {
                    const statusElement = document.getElementById('smtp-status');
                    statusElement.textContent = status;
                    if (status.includes('Connected')) {
                        statusElement.className = 'status-success';
                    } else {
                        statusElement.className = 'status-failed';
                    }
                })
                .catch(error => {
                    alert('Failed to refresh status: ' + error.message);
                });
        });
    </script>