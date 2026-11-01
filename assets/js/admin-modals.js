 
(() => {
    const editElement = document.getElementById('adminEditDialog');
    const deleteElement = document.getElementById('adminDeleteDialog');
    const editDialog = new bootstrap.Modal(editElement);
    const deleteDialog = new bootstrap.Modal(deleteElement);
    const editBody = editElement.querySelector('.modal-body');
    const editTitle = document.getElementById('adminEditTitle');
    let editUrl = '';
    let opener = null;
    let pendingDelete = null;
    let loadNumber = 0;
     
    const formPages = {
        'edit_product.php': { title: 'Edit Product', list: 'products.php', size: 'product' },
        'edit_discount.php': { title: 'Edit Discount', list: 'discounts.php' },
        'add_product.php': { title: 'Add Product', list: 'products.php', size: 'product' },
        'add_category.php': { title: 'Add Category', list: 'categories.php', size: 'small' },
        'add_age_group.php': { title: 'Add Age Group', list: 'age_groups.php' },
        'add_discount.php': { title: 'Add Discount', list: 'discounts.php' }
    };

     
    document.querySelectorAll('.users-page .modal-overlay').forEach(element => {
        element.className = 'modal fade admin-dialog';
        element.tabIndex = -1;
        const title = element.id === 'editModal' ? 'Edit User' : 'Add User';
        element.setAttribute('aria-label', title);
        element.querySelector('.modal-header h3').textContent = title;
        const content = element.querySelector('.modal-card');
        content.className = 'modal-content';
        const dialog = document.createElement('div');
        dialog.className = 'modal-dialog modal-dialog-centered modal-dialog-scrollable admin-dialog-small';
        element.append(dialog);
        dialog.append(content);
        const form = content.querySelector('form');
        const footer = form.lastElementChild;
        footer.className = 'modal-footer';
        const body = document.createElement('div');
        body.className = 'modal-body';
        [...form.children].filter(child => child !== footer).forEach(child => body.append(child));
        form.prepend(body);
        const cancel = document.createElement('button');
        cancel.type = 'button';
        cancel.className = 'btn btn-light';
        cancel.textContent = 'Cancel';
        cancel.dataset.bsDismiss = 'modal';
        footer.prepend(cancel);
        const close = content.querySelector('.modal-header button');
        close.className = 'btn-close';
        close.type = 'button';
        close.textContent = '';
        close.setAttribute('aria-label', 'Close');
        document.body.append(element);
    });

     
    document.querySelectorAll('.modal:not(#adminEditDialog):not(#adminDeleteDialog)').forEach(element => {
        element.classList.add('admin-dialog');
        element.querySelector('.modal-dialog')?.classList.add('modal-dialog-centered', 'modal-dialog-scrollable');
        if (element.id.toLowerCase().startsWith('delete')) {
            element.classList.add('admin-delete-dialog');
            element.querySelectorAll('.modal-body > .text-danger:has(i), .modal-body > i').forEach(icon => icon.remove());
            const content = element.querySelector('.modal-content');
            if (!content.querySelector('.modal-header')) {
                const title = content.querySelector('h5');
                const header = document.createElement('div');
                header.className = 'modal-header';
                title.classList.add('modal-title');
                const close = document.createElement('button');
                close.type = 'button';
                close.className = 'btn-close';
                close.dataset.bsDismiss = 'modal';
                close.setAttribute('aria-label', 'Close');
                header.append(title, close);
                content.querySelector('form').prepend(header);
            }
            const footer = content.querySelector('[type="submit"]')?.closest('.d-flex');
            if (footer) {
                footer.className = 'modal-footer';
                content.querySelector('form').append(footer);
            }
            content.querySelector('.modal-body')?.classList.remove('text-center');
        }
    });

    window.confirmAdminDelete = function (message, onConfirm, title = 'Delete item?') {
        pendingDelete = onConfirm;
        document.getElementById('adminDeleteTitle').textContent = title;
        document.getElementById('adminDeleteMessage').textContent = message;
        deleteDialog.show();
    };
    document.getElementById('adminDeleteConfirm').addEventListener('click', () => {
        const action = pendingDelete;
        pendingDelete = null;
        deleteDialog.hide();
        if (action) action();
    });
    deleteElement.addEventListener('hidden.bs.modal', () => { pendingDelete = null; });

     
    document.addEventListener('submit', event => {
        const button = event.submitter;
        const form = event.target;
        if (!button?.classList.contains('admin-action-delete') || form.dataset.deleteConfirmed === 'yes') return;
        event.preventDefault();
        const entity = (button.getAttribute('aria-label') || 'Delete item').replace(/^Delete\s+/i, '');
        const cells = button.closest('tr')?.querySelectorAll('td');
        const name = cells?.[1]?.textContent.trim();
        const detail = name ? ' “' + name + '”' : '';
        const message = form.action.includes('delete_product.php')
            ? 'Remove' + detail + ' from the catalog? Its data and previous orders will be kept.'
            : 'Are you sure you want to delete' + detail + '? This action cannot be undone.';
        window.confirmAdminDelete(message, () => {
            form.dataset.deleteConfirmed = 'yes';
            form.requestSubmit(button);
            delete form.dataset.deleteConfirmed;
        }, 'Delete ' + entity + '?');
    });

    function showEditContent(html, url) {
        const doc = new DOMParser().parseFromString(html, 'text/html');
        const content = doc.querySelector('.admin-content');
        const form = content?.querySelector('form');
        if (!form) throw new Error('The form could not be loaded. Please reopen it or sign in again.');
        editBody.replaceChildren();
        content.querySelectorAll('.alert').forEach(alert => editBody.append(alert));
        form.classList.add('admin-remote-form');
        form.querySelectorAll('a.btn').forEach(link => {
            const cancel = document.createElement('button');
            cancel.type = 'button';
            cancel.className = 'btn btn-light';
            cancel.dataset.bsDismiss = 'modal';
            cancel.textContent = 'Cancel';
            link.replaceWith(cancel);
        });
        if (!form.querySelector('[data-bs-dismiss="modal"]')) {
            const cancel = document.createElement('button');
            cancel.type = 'button';
            cancel.className = 'btn btn-light me-2';
            cancel.dataset.bsDismiss = 'modal';
            cancel.textContent = 'Cancel';
            form.querySelector('[type="submit"]').before(cancel);
        }
        const submit = form.querySelector('[type="submit"]');
        if (new URL(url).pathname.endsWith('/add_discount.php')) submit.textContent = 'Add Discount';
        let footer = submit.closest('.d-flex');
        const keepFooterInPlace = !!footer?.classList.contains('product-upload-actions');
        if (!footer || footer === form) {
            footer = document.createElement('div');
            const cancel = form.querySelector('[data-bs-dismiss="modal"]');
            if (cancel) footer.append(cancel);
            footer.append(submit);
        }
        if (keepFooterInPlace) footer.classList.add('admin-remote-footer');
        else footer.className = 'admin-remote-footer';
        const cancelButton = footer.querySelector('[data-bs-dismiss="modal"]');
        if (cancelButton) footer.prepend(cancelButton);
        const fields = document.createElement('div');
        fields.className = 'admin-remote-fields';
        if (keepFooterInPlace) {
            [...form.children].forEach(child => fields.append(child));
            form.append(fields);
        } else {
            [...form.children].filter(child => child !== footer).forEach(child => fields.append(child));
            form.append(fields, footer);
        }
        if (/\/(add|edit)_discount\.php/.test(url)) fields.classList.add('admin-discount-fields');
        editBody.append(form);
        editUrl = url;
        editBody.scrollTop = 0;
    }

    document.addEventListener('click', async event => {
        const link = event.target.closest('a[href]');
        if (!link || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
        const url = new URL(link.href);
        const page = url.pathname.split('/').pop();
        const settings = formPages[page];
        if (url.origin !== location.origin || !settings) return;
        event.preventDefault();
        opener = link;
        editUrl = link.href;
        const currentLoad = ++loadNumber;
        editTitle.textContent = settings.title;
        editElement.querySelector('.modal-dialog').classList.toggle('admin-dialog-product', settings.size === 'product');
        editElement.querySelector('.modal-dialog').classList.toggle('admin-dialog-small', settings.size === 'small');
        editBody.textContent = 'Loading form…';
        editDialog.show();
        try {
            const response = await fetch(editUrl, { credentials: 'same-origin' });
            if (!response.ok) throw new Error('Unable to load the form. Please try again.');
            const html = await response.text();
            if (currentLoad === loadNumber) showEditContent(html, response.url);
        } catch (error) {
            if (currentLoad !== loadNumber) return;
            editBody.textContent = error.message;
            const fallback = document.createElement('a');
            fallback.href = link.href;
            fallback.className = 'btn btn-light d-block mt-3';
            fallback.textContent = 'Open the existing form page';
            editBody.append(fallback);
        }
    });

    editBody.addEventListener('submit', async event => {
        const form = event.target;
        if (!form.classList.contains('admin-remote-form')) return;
        event.preventDefault();
        if (editElement.dataset.saving === 'yes') return;
        const data = new FormData(form);
        if (event.submitter?.name) data.append(event.submitter.name, event.submitter.value);
        const submitUrl = new URL(form.getAttribute('action') || editUrl, editUrl);
        const method = (form.getAttribute('method') || 'GET').toUpperCase();
        const submitButtons = [...form.querySelectorAll('[type="submit"]')];
        editElement.dataset.saving = 'yes';
        submitButtons.forEach(button => { button.disabled = true; });
        try {
            const response = await fetch(submitUrl, { method, body: data, credentials: 'same-origin' });
            const expectedPage = formPages[new URL(editUrl).pathname.split('/').pop()].list;
            if (response.ok && new URL(response.url).pathname.endsWith('/' + expectedPage)) {
                location.reload();
                return;
            }
            if (!response.ok) throw new Error('Unable to save. Check your connection and reopen the form before trying again.');
            showEditContent(await response.text(), response.url);
        } catch (error) {
            const note = document.createElement('div');
            note.className = 'alert alert-danger';
            note.setAttribute('role', 'alert');
            note.textContent = error.message;
            editBody.prepend(note);
        } finally {
            delete editElement.dataset.saving;
            submitButtons.forEach(button => { button.disabled = false; });
        }
    });
    editElement.addEventListener('hide.bs.modal', event => {
        if (editElement.dataset.saving === 'yes') event.preventDefault();
    });
    editElement.addEventListener('hidden.bs.modal', () => {
        ++loadNumber;
        editBody.replaceChildren();
        opener?.focus();
    });
})();
