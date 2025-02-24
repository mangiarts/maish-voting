// Function to check admin login
function checkAdmin() {
    const adminPassword = document.getElementById("adminPassword").value;
    if (adminPassword === "admin123") {
        document.getElementById("adminLogin").style.display = "none";
        document.getElementById("adminForm").style.display = "block";
        document.getElementById("contestantList").style.display = "block";
        loadContestants();
    } else {
        alert("Incorrect password!");
    }
}

// Function to add a new contestant
function addContestant() {
    const fullName = document.getElementById("fullName").value;
    const artistName = document.getElementById("artistName").value;
    const email = document.getElementById("email").value;
    const phone = document.getElementById("phone").value;
    const idNumber = document.getElementById("idNumber").value;
    const talentUrl = document.getElementById("talentUrl").value;
    const gender = document.getElementById("gender").value;
    const talentCategory = document.getElementById("talentCategory").value;
    const county = document.getElementById("county").value;
    const profilePic = document.getElementById("profilePic").files[0];

    if (!fullName || !artistName || !email || !phone || !idNumber || !talentUrl || !gender || !talentCategory || !county || !profilePic) {
        alert("Please fill in all fields and upload a profile picture.");
        return;
    }

    const reader = new FileReader();
    reader.onload = async function (e) {
        const profilePicURL = e.target.result;
        const uniqueCode = `TTS-000${Date.now()}`; // Unique code using timestamp

        const newContestant = {
            fullName,
            artistName,
            email,
            phone,
            idNumber,
            talentUrl,
            gender,
            talentCategory,
            county,
            profilePicURL,
            uniqueCode
        };

        try {
            const response = await fetch("YOUR_WEB_APP_URL", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(newContestant)
            });

            const result = await response.json();
            if (result.status === "success") {
                alert("Contestant added successfully!");
            } else {
                alert("Error saving contestant. Try again.");
            }
        } catch (error) {
            console.error("Error:", error);
            alert("Network error. Try again.");
        }
    };
    reader.readAsDataURL(profilePic);
}


// Function to load contestants
function loadContestants() {
    const contestantListContainer = document.getElementById("contestantListContainer");
    contestantListContainer.innerHTML = "";

    const contestants = JSON.parse(localStorage.getItem("contestants")) || [];

    contestants.forEach(contestant => {
        const card = document.createElement("div");
        card.classList.add("contestant-card");

        card.innerHTML = `
            <img src="${contestant.profilePicURL}" alt="${contestant.artistName}">
            <div>
                <h3>${contestant.fullName}</h3>
                <p><strong>Artist Name:</strong> ${contestant.artistName}</p>
                <p><strong>Email:</strong> ${contestant.email}</p>
                <p><strong>Phone:</strong> ${contestant.phone}</p>
                <p><strong>ID Number:</strong> ${contestant.idNumber}</p>
                <p><strong>Talent URL:</strong> <a href="${contestant.talentUrl}" target="_blank">View Talent</a></p>
                <p><strong>Gender:</strong> ${contestant.gender}</p>
                <p><strong>Talent Category:</strong> ${contestant.talentCategory}</p>
                <p><strong>County:</strong> ${contestant.county}</p>
                <p><strong>Unique Code:</strong> ${contestant.uniqueCode}</p>
                <button class="vote-btn" onclick="voteForContestant('${contestant.uniqueCode}', '${contestant.fullName}')">Vote for Me</button>
                <button class="whatsapp-btn" onclick="shareToWhatsApp('${contestant.artistName}', '${contestant.uniqueCode}')">Share to WhatsApp</button>
            </div>
        `;

        contestantListContainer.appendChild(card);
    });
}

// Function to redirect to Payment Methods page with pre-filled contestant details
function voteForContestant(contestantNumber, contestantName) {
    const url = new URL('Payment Methods.html', window.location.origin);
    url.searchParams.set('contestantNumber', contestantNumber);
    url.searchParams.set('contestantName', contestantName);
    window.location.href = url.toString();
}

// Function to share contestant details to WhatsApp
function shareToWhatsApp(artistName, uniqueCode) {
    const message = `Vote for ${artistName}! Support me by clicking the link: ${window.location.origin}/Payment%20Methods.html?contestantNumber=${uniqueCode}&contestantName=${artistName}`;
    const whatsappURL = `https://wa.me/?text=${encodeURIComponent(message)}`;
    window.open(whatsappURL, '_blank');
}

// Load contestants on page load
document.addEventListener("DOMContentLoaded", loadContestants);