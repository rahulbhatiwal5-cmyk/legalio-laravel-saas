<div class="modal fade" id="editDocumentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

```
        <div class="modal-header">
            <h5 class="modal-title">Edit Document with AI</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
            <div class="mb-3">
                <label>What do you want to edit?</label>
                <select class="form-control" id="modalEditType">
                    <option value="questionnaire">Questionnaire</option>
                    <option value="contract">Contract Text</option>
                    <option value="both">Questionnaire & Contract</option>
                </select>
            </div>

            <div class="mb-3">
                <label>Instruction for AI</label>
                <textarea class="form-control" id="modalEditInstruction" rows="5"
                    placeholder="Describe what the AI should change, improve or correct..."></textarea>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary" onclick="submitEditDocument()">Apply Changes</button>
        </div>

    </div>
</div>
```

</div>

<script>
function openEditModal(){
    const modal = new bootstrap.Modal(document.getElementById('editDocumentModal'));
    modal.show();
}
</script>
