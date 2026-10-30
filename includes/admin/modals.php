<div class="modal fade admin-dialog" id="adminEditDialog" tabindex="-1" aria-labelledby="adminEditTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="adminEditTitle">Edit item</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" aria-live="polite"></div>
        </div>
    </div>
</div>
<div class="modal fade admin-dialog admin-delete-dialog" id="adminDeleteDialog" tabindex="-1" aria-labelledby="adminDeleteTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="adminDeleteTitle">Delete item?</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <i class="bi bi-trash admin-delete-symbol" aria-hidden="true"></i>
                <p id="adminDeleteMessage" class="mb-0"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="adminDeleteConfirm">Delete</button>
            </div>
        </div>
    </div>
</div>
