"use strict";

let beheerders = [];

function renderSidebar() {
    const sidebar = document.getElementById("sideLinks");
    sidebar.innerHTML = "";

    beheerders.forEach((b, index) => {
        const a = document.createElement("a");
        a.href = "#";
        a.textContent = b.naam;
        a.addEventListener("click", (e) => {
            e.preventDefault();
            renderProfile(index);
        });
        sidebar.appendChild(a);
    });
}

function renderProfile(index) {
    const b = beheerders[index];
    const nl = str => str ? str.replace(/\\n|\n/g, '<br>') : '';

    document.querySelectorAll("#sideLinks a").forEach((a, i) => {
        a.classList.toggle("active", i === index);

        if (i === index) a.setAttribute("aria-current", "true");
        else a.removeAttribute("aria-current");
    });

    document.getElementById("profileContainer").innerHTML = `
        <table>
            <tr>
                <th><strong>Meet the Team</strong></th>
                <td><img src="${b.foto}" alt="${b.naam}" style="height: 250px; width: 190px;"></td>
            </tr>
            <tr><th><strong>Naam:</strong></th><td>${b.naam}</td></tr>
            <tr><th><strong>Woonplaats:</strong></th><td>${b.woonplaats}</td></tr>
            <tr><th><strong>Wanneer ben je begonnen met darten:</strong></th><td>${nl(b.begonnen)}</td></tr>
            <tr><th><strong>1ste dartpijlen:</strong></th><td>${b.eerstePijlen}</td></tr>
            <tr><th><strong>Darts die ik gebruik:</strong></th><td>${b.huidigePijlen}</td></tr>
            <tr><th><strong>Favoriete pro dartspeler:</strong></th><td>${nl(b.favorietePro)}</td></tr>
            <tr><th><strong>Favoriete dartsmerk:</strong></th><td>${b.favorieteMerk}</td></tr>
            <tr><th><strong>Tip voor iedereen:</strong></th><td>${nl(b.tip)}</td></tr>
        </table>`;
}

document.addEventListener("DOMContentLoaded", async () => {
    try {
        const response = await fetch("api/beheerders.php");
        if (!response.ok) throw new Error(`HTTP ${response.status}`);

        beheerders = await response.json();
        // Already sorted server-side by volgorde, but keep client-side sort defensively
        beheerders.sort((a, b) => (a.volgorde ?? 0) - (b.volgorde ?? 0));

        renderSidebar();

        if (beheerders.length > 0) renderProfile(0);
    } catch (error) {
        console.error("Fout bij ophalen beheerders:", error);
        const container = document.getElementById("profileContainer");
        if (container) container.innerHTML = "<p>Beheerders konden niet geladen worden.</p>";
    }
});
