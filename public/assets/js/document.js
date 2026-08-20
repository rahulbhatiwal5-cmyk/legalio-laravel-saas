const fieldTemplates = {
    "textbox": (que_id) => `
        <div class="form-group">
            <label class="form-label" for="text_placeholder-${que_id}">Text Box Placeholder</label>
            <input type="text" class="form-control text_box_placeholder" id="text_placeholder-${que_id}" name="text_placeholder-${que_id}" value="">
        </div>
    `,
    "textarea": (que_id) => `
        <div class="form-group">
            <label class="form-label" for="text_area_placeholder-${que_id}">Text Area Placeholder</label>
            <textarea class="form-control" id="text_area_placeholder-${que_id}" name="text_area_placeholder-${que_id}"></textarea>
        </div>
    `,
    "dropdown": (que_id) => `
        <div class="form-group">
            <label class="form-label">Add Dropdown Option</label>
        </div>
        <div class="append_options" id="append_options${que_id}"></div>
        <div class="text-end">
            <div class="form-group">
                <button type="button" class="btn btn-sm btn-primary" onclick="addOptions('dropdown', ${que_id})">Add Option</button>
            </div>
        </div>
    `,
    "radio-button": (que_id) => `
        <div class="form-group">
            <label class="form-label">Add Radio Option</label>
        </div>
        <div class="append_options" id="append_options${que_id}"></div>
        <div class="text-end">
            <div class="form-group">
                <button type="button" class="btn btn-sm btn-primary" onclick="addOptions('radio-button', ${que_id})">Add Option</button>
            </div>
        </div>
    `,
    "date-field": (que_id) => `
        <div class="form-group">
            <label class="form-label" for="date_placeholder-${que_id}">Select Date</label>
            <input type="date" class="form-control" id="date_placeholder-${que_id}" name="date_placeholder-${que_id}">
        </div>
    `,
    "pricebox": (que_id) => `
        <div class="form-group">
            <label class="form-label" for="price_input-${que_id}">Price</label>
            <input type="number" class="form-control" id="price_input-${que_id}" name="price_input-${que_id}" step="0.01">
        </div>
    `,
    "number-field": (que_id) => `
        <div class="form-group">
            <label class="form-label" for="number_input-${que_id}">Enter Number</label>
            <input type="number" class="form-control" id="number_input-${que_id}" name="number_input-${que_id}">
        </div>
    `,
    "percentage-box": (que_id) => `
        <div class="form-group">
            <label class="form-label" for="percentage_input-${que_id}">Percentage</label>
            <input type="number" class="form-control" id="percentage_input-${que_id}" name="percentage_input-${que_id}" step="0.01" min="0" max="100">
        </div>
    `,
    "dropdown-link": (que_id) => `
        <div class="form-group">
            <label class="form-label" for="dropdown_link-${que_id}">Dropdown Link</label>
            <input type="url" class="form-control" id="dropdown_link-${que_id}" name="dropdown_link-${que_id}">
        </div>
    `
};
