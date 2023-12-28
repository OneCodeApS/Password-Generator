<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Password Decoder</title>

    <style>
/* style.css */
body {
    font-family: 'Arial', sans-serif;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background-color: #f0f0f0;
    margin: 0;
    padding: 0;
}

.container {
    text-align: center;
    background-color: #ffffff;
    padding: 20px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    border-radius: 10px;
}

#logoContainer {
    margin-bottom: 20px;
}

#logo {
    max-width: 150px; /* Adjust as needed */
    height: auto;
}

button {
    background-color: #007bff;
    color: white;
    border: none;
    padding: 10px 20px;
    margin: 10px;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s;
}

button:hover {
    background-color: #0056b3;
}

.hidden {
    display: none;
}

label, #copySuccessMessage {
    display: block;
    margin-top: 20px;
}

#copySuccessMessage {
    color: #28a745;
}

.flexCenter {
    display: flex;
    justify-content: center;
    align-items: center;
}

#spinnerContainer {
    border: 5px solid #f3f3f3; /* Light grey background */
    border-top: 5px solid #3498db; /* Blue color */
    border-radius: 50%;
    width: 50px;
    height: 50px;
    animation: spin 2s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}




    </style>
</head>
<body>



        <div class="container">
            <div id="logoContainer">
                <img src="https://c-solution.dk/wp-content/uploads/2023/04/MicrosoftTeams-image-3.png" alt="Logo" id="logo">
            </div>
            <button id="decodeButton">Decode Password</button>
            <div class="flexCenter">
            <div id="spinnerContainer" class="hidden">
            </div>
            </div>
            
            
            

            <label id="decodedTextLabel" class="hidden"><span id="decodedText"><?php echo htmlspecialchars($decodedValue); ?></span></label>
            <button id="copyButton" class="hidden">Copy Password</button>
            <p id="copySuccessMessage" class="hidden"></p>
        </div>


    <script>
        // Your URL


// script.js
var url = window.location.href;
var urlGUID = url.split('?')[1];
let spinnerInterval;
var decodedText = document.getElementById("decodedText")

document.getElementById("decodeButton").addEventListener("click", function() {
   
    //document.getElementById("decodedText").textContent = decodedString;

    // Hide the decode button
    this.classList.add("hidden");

    // Show the decoded text and copy button
    
    document.getElementById('spinnerContainer').classList.remove("hidden");
    postDecodeRequest()
});

document.getElementById("copyButton").addEventListener("click", function() {
    var text = document.getElementById("decodedText").textContent;
    navigator.clipboard.writeText(text).then(function() {
        var successMessage = document.getElementById("copySuccessMessage");
        successMessage.textContent = "Password copied to clipboard";
        successMessage.classList.remove("hidden");

        // Hide the message after a few seconds
        setTimeout(function() {
            successMessage.classList.add("hidden");
        }, 3000);
    });
});

function postDecodeRequest() {
    var xhr = new XMLHttpRequest();
    var url = "https://prod-69.westeurope.logic.azure.com:443/workflows/b92c0aa1b4f5446ea9d439363a6d4ffa/triggers/manual/paths/invoke?api-version=2016-06-01&sp=%2Ftriggers%2Fmanual%2Frun&sv=1.0&sig=T4gFUptZK0fD7yqvP8p0Jb8ZqqL70rlA8ib3Rw5xAR0";
    xhr.open("POST", url, true);
    xhr.setRequestHeader("Content-Type", "application/json");
    
    xhr.onreadystatechange = function () {
        console.log(xhr);
        console.log("decodedPassword.php?GUID=" + urlGUID)
        console.log(urlGUID);
        if (xhr.readyState === 4 && xhr.status === 202) {
            console.log("Request successful");
            // Wait for 5 seconds before Firebase fetching
        var attemptCount = 0;
        var maxAttempts = 5;

        var intervalId = setInterval(function() {
            var xhrFirebase = new XMLHttpRequest();
            xhrFirebase.open("GET", "decodedPassword.php?GUID=" + urlGUID, true); // Adjust the path as needed
            xhrFirebase.onreadystatechange = function() {
                console.log(xhrFirebase);
                if (xhrFirebase.readyState === 4) {
                    if (xhrFirebase.status === 200 && xhrFirebase.responseText != "") {
                        clearInterval(intervalId);
                        var firebaseData = xhrFirebase.responseText;
                        setDecodedText(firebaseData);
                        console.log(firebaseData);
                        document.getElementById("copyButton").classList.remove("hidden");
                    } else {
                        console.error("Failed to fetch data.");
                    }
                }
            };

            xhrFirebase.send();

            attemptCount++;
            if (attemptCount >= maxAttempts) {
                clearInterval(intervalId); // Clear the interval if max attempts are reached
                setDecodedText("The password could not be retrieved. It may have already been retrieved earlier or it might have expired.");
                decodedText.style.color = "red";
                console.error("Maximum attempts reached. Stopping retries.");
            }
        }, 1000);

        }
    };
    var data = JSON.stringify({"GUID": urlGUID});
    xhr.send(data);
}

function setDecodedText(newText) {
    decodedText.innerText = newText;
    document.getElementById('spinnerContainer').classList.add("hidden");
    clearInterval(spinnerInterval);
    document.getElementById("decodedTextLabel").classList.remove("hidden");
}

function randomCharacter() {
  const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()';
  return characters.charAt(Math.floor(Math.random() * characters.length));
}

function updateSpinner() {
  document.getElementById('spinnerContent').innerText = randomCharacter();
}



    </script>
</body>
</html>