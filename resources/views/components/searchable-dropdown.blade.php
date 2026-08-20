<div class="dropdown">
    <!-- Dropdown Button -->
    <button class="btn btn-primary dropdown-toggle w-100 text-start" type="button" id="dropdownMenuButton{{ $id }}" data-bs-toggle="dropdown" aria-expanded="false">
        {{ $selectedText ?? 'Select an Option' }}
    </button>

    <!-- Dropdown Menu with Search -->
    <ul class="dropdown-menu w-100" aria-labelledby="dropdownMenuButton{{ $id }}">
        <!-- Search Input -->
        <li class="px-2">
            <input type="text" class="form-control" placeholder="Search..." id="dropdownSearch{{ $id }}" onkeyup="filterDropdown('{{ $id }}')">
        </li>

        <!-- Dropdown Options -->
        <div id="dropdownOptions{{ $id }}">
            @foreach ($options as $key => $value)
                <li>
                    <a class="dropdown-item dropdown-option" href="#" data-value="{{ $key }}">{{ $value }}</a>
                </li>
            @endforeach
        </div>
    </ul>

    <!-- Hidden Input for Storing Selection -->
    <input type="hidden" name="{{ $name }}" id="{{ $id }}" value="{{ $selected }}">
</div>

<!-- JavaScript for Search & Selection -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    let dropdownItems = document.querySelectorAll("#dropdownOptions{{ $id }} .dropdown-option");
    let dropdownButton = document.getElementById("dropdownMenuButton{{ $id }}");
    let hiddenInput = document.getElementById("{{ $id }}");

    // Update button text & store selected value
    dropdownItems.forEach(item => {
        item.addEventListener("click", function (e) {
            e.preventDefault();
            dropdownButton.innerText = this.innerText;
            hiddenInput.value = this.dataset.value;
        });
    });
});

// Search Function
function filterDropdown(id) {
    let input = document.getElementById("dropdownSearch" + id).value.toLowerCase();
    let items = document.querySelectorAll("#dropdownOptions" + id + " .dropdown-option");

    items.forEach(item => {
        if (item.innerText.toLowerCase().includes(input)) {
            item.style.display = "block";
        } else {
            item.style.display = "none";
        }
    });
}
</script>
