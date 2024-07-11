<?php
include 'header.html';
include 'nav.html';

include 'credentials.php';
include 'curl.php';
$workplace_results = json_decode(curl($workplace_url, $headers), true);
?>


<h2> Del 2 - Skapa tidsrapport</h2>
<p>* Obligatoriska uppgifter</p>

<form id="uploadForm" enctype="multipart/form-data">
    <label for="date">Datum *:</label>
    <span id="dateWarning" style="color: red;"></span>
    <input type="date" id="date" name="date" required>

    <label for="hours">Antal timmar *:</label>
    <span id="hoursWarning" style="color: red;"></span>
    <input type="number" id="hours" step="0.1" name="hours" placeholder="7.5 (decimalpunkt)" min="0.1" required>

    <label for="workplace_id">Arbetsplats *:</label>
    <span id="workplace_idWarning" style="color: red;"></span>
    <select id="workplace_id" name="workplace_id" required>
        <option value="">Välj arbetsplats...</option>
        <?php foreach ($workplace_results as $workplace) { ?>
            <option value="<?= $workplace['id'] ?>"><?= htmlspecialchars($workplace["name"]) ?></option>
        <?php } ?>
    </select>

    <label for="info">Övrig information:</label>
    <textarea id="info" name="info" rows="4" cols="50" placeholder="Lägg till övrig information här..."></textarea>

    <label for="imageFile">Lägg till en bild:</label>
    <input type="file" name="imageFile" id="imageFile">
    <input type="hidden" id="imageInfo" name="imageInfo"><br>
    <button type="button" onclick="submitForm()">Skicka</button>
</form>

<div id="imageContainer" style="display: none;">
    <br>
    <img id="preview" src="#" alt="Preview Image" style="max-width: 200px; max-height: 200px;">
</div>

<script>
    // Event listener for file input change
    $('#imageFile').on('change', function (e) {
        let file = e.target.files[0];
        let reader = new FileReader();

        reader.onload = function (e) {
            $('#preview').attr('src', e.target.result);
            $('#imageContainer').show();
        }

        if (!file) {
            $('#imageContainer').hide();
        }

        reader.readAsDataURL(file);
    });

    function validateInput() {
        let inputOk = true;

        let dateInput = document.getElementById("date");
        let dateWarning = document.getElementById("dateWarning");
        if (dateInput.value === "") {
            dateWarning.innerHTML = "Datum måste anges.";
            inputOk = false;
        } else {
            dateWarning.innerHTML = "";
        }

        let hoursInput = document.getElementById("hours");
        let hoursWarning = document.getElementById("hoursWarning");

        if (hoursInput.value === "") {
            hoursWarning.innerHTML = "Arbetade timmar måste anges. Använd siffror och separera delar av timmar med . (punkt)";
            inputOk = false;
        } else if (hoursInput.value <= 0) {
            hoursWarning.innerHTML = "Angivna timmar måste vars mer än 0.";
            inputOk = false;
        } else {
            hoursWarning.innerHTML = "";
        }

        let workplace_idInput = document.getElementById("workplace_id");
        let workplace_idWarning = document.getElementById("workplace_idWarning");
        if (workplace_idInput.value === "") {
            workplace_idWarning.innerHTML = "Arbetsplats måste anges.";
            inputOk = false;
        } else {
            workplace_idWarning.innerHTML = "";
        }

        return inputOk;
    }

    function submitForm() {

        if (!validateInput()) {
            return; // Abort and return if validation fails
        }

        let formData = new FormData($('#uploadForm')[0]);
        $.ajax({
            type: 'POST',
            url: 'upload.php',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                let jsonResponse = JSON.parse(response);
                if (jsonResponse.success === true) {
                    $('#preview').attr('src', ''); // Clear preview
                    $('#uploadForm')[0].reset(); // Clear form
                    $('#imageContainer').hide();
                    alert('Tidsrapport skapad!');
                } else {
                    alert('Problem: ' + response.message);
                }
            },
            error: function () {
                alert('Det gick inte att skicka informationen');
            }
        });
    }
</script>

<?php
include 'footer.html';
?>
