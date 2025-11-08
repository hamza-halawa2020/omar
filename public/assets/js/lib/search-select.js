document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll('select[data-searchable="true"]').forEach(function (select) {

        select.style.display = "none";

        let wrapper = document.createElement("div");
        wrapper.className = "searchable-select-wrapper";

        let input = document.createElement("input");
        input.type = "text";
        input.className = "searchable-select-input";
        input.placeholder = select.getAttribute("data-placeholder") || "Search...";

        let dropdown = document.createElement("div");
        dropdown.className = "searchable-select-dropdown";

        function buildList() {
            dropdown.innerHTML = "";
            let searchValue = input.value.toLowerCase();

            select.querySelectorAll("option").forEach(option => {
                if (option.text.toLowerCase().includes(searchValue)) {
                    let item = document.createElement("div");
                    item.textContent = option.text;
                    item.onclick = function () {
                        select.value = option.value;
                        input.value = option.text;
                        dropdown.style.display = "none";
                    };
                    dropdown.appendChild(item);
                }
            });
        }

        input.onclick = function () {
            buildList();
            dropdown.style.display = "block";
        };

        input.oninput = function () {
            buildList();
        };

        document.addEventListener("click", function (e) {
            if (!wrapper.contains(e.target)) {
                dropdown.style.display = "none";
            }
        });

        wrapper.appendChild(input);
        wrapper.appendChild(dropdown);
        select.insertAdjacentElement("afterend", wrapper);
    });
});
