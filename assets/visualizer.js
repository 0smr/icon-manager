document.addEventListener('DOMContentLoaded', () => {
    const selector = document.getElementById("selector");
    const editor = document.getElementById("editor");
    const preview = document.getElementById("preview");

    const onEdit = (e) => {
        const content = e.target.value;
        // editor.rows = (content.match(/(\r|\n)/g)?.length + 1) || 1; // Set auto rows count
        const taged = content.replace(new RegExp(selector.value, 'g'),(match, captr) => {
            const path = iconList[captr] ?? "";
            return `<b class="${!path ? "ne": ""}">${captr}<img src='${path}'></b>`
        });
        preview.innerHTML = taged;
    }

    editor.addEventListener('change', onEdit);
});