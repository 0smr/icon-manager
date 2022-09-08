/**
 * Activate preview image veiwer.
 * @param {EventListenerOrEventListenerObject} event
 * @returns {void}
 */
async function activeModal(event) {
    if(!event.target.src) return; // Return if src is empty.

    const container = document.getElementById("container");
    const cover = document.getElementById("cover");
    const modal = document.getElementById("modal");
    const unicodeTable = document.getElementById("unicode-table");
    const img = event.target;

    cover.style.display = "block";
    modal.style.display = "block";
    unicodeTable.classList.add("blur");

    // Fetch SVG file data.
    const svg = await fetch(img.src).then(response => response.text());

    container.innerHTML = svg;
}

/**
 * @param {String} svgPath
 * @return {String} SVG element as string.
 */
function svgCommands(svgPath) {
    // ([a-zA-Z])(-?\d*\.?\d*),?(-?\d*\.?\d*)(-|\d|\.|,)*

}

/**
 * @param {EventListenerOrEventListenerObject} event
 */
function copyToClipboard(event) {
    const tooltip = event.target;
    const oldText = tooltip.innerText;
    navigator.clipboard.writeText(tooltip.innerText.substr(0, 4)); // copy to clipboard

    tooltip.innerText = "copied";
    setTimeout(() => (tooltip.innerText = oldText), 1000);
}

/**
 * POST svg file text to add into list.
 * @param {Number} code
 * @returns {void}
 */
async function addNewIcon(code) {
    const fileDialog = document.getElementById("file-dialog");
    const file = fileDialog.files[0];

    if(!file || !code) { alert("Select a file first."); return; }

    const defaultName = code + "-" + file.name;
    const newName = prompt("Enter icon name.", defaultName);

    if(newName != null) {
        const fileContent = await file.text();

        fetch('crud.php', {
            method: 'POST',
            body: JSON.stringify({
                op: "add",
                name: newName || defaultName,
                content: fileContent
            })
        }).then(res => res.json())
          .then(json => {
            if(json.success == true)
                location.reload();
        });
    }
}

/**
 * Request font generation.
 * @param {EventListenerOrEventListenerObject} event
 */
 function generateFont(event) {
    const parentElement = event.target.parentElement;
    parentElement.classList.add("progress");

    fetch('crud.php', {
        method: 'POST',
        body: JSON.stringify({ op: "generateFont" })
    }).then(res => res.json())
      .then(json => {
        alert(json.success ? "Font generated" : "Error occured");
        alert(json.message);
        parentElement.classList.remove("progress");
    });
}

document.addEventListener("DOMContentLoaded", () => {
    const cover = document.getElementById("cover");
    const modal = document.getElementById("modal");
    const fileDialog = document.getElementById("file-dialog");
    const unicodeTable = document.getElementById("unicode-table");

    const glyphs = document.querySelectorAll("#unicode-table img");
    const tooltips = document.querySelectorAll("#unicode-table i");

    const vguide = document.querySelector("#modal .vguide");
    const hguide = document.querySelector("#modal .hguide");
    const emptyCells = document.getElementsByClassName("add");

    const generateButton = document.getElementById("generator");

    for (const glyph of glyphs) {
        glyph.addEventListener("click", activeModal);
    }

    for(const emptyCell of emptyCells) {
        emptyCell.addEventListener("click", (e) => {
            if(fileDialog.files[0]) {
                addNewIcon(e.target.innerHTML);
            } else {
                fileDialog.click();
                fileDialog.onchange = () => { addNewIcon(e.target.innerHTML); }
            }
        });
    }

    for (const tooltip of tooltips) {
        tooltip.addEventListener("click", copyToClipboard);
    }

    generateButton.addEventListener("click", generateFont);

    cover.onclick = () => {
        cover.style.display = "none";
        modal.style.display = "none";
        unicodeTable.classList.remove("blur");
    };

    modal.onmousemove = event => {
        let padding = 5;
        let x = event.clientX - modal.offsetLeft;
        let y = event.clientY - modal.offsetTop;

        vguide.style.left = x + "px";
        vguide.innerHTML = x - padding - 2 + "px";
        hguide.style.top = y + "px";
        hguide.innerHTML = y - padding + "px";
    };
});