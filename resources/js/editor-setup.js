import ClassicEditor from '@ckeditor/ckeditor5-build-classic';

/** @type {File[]} */
const newAttachments = [];

/** @type {number[]} */
const attachmentTrash = [];

/**
 * @param file {File}
 * @param onDelete {Function}
 */
function attachmentUIFactory(file, onDelete) {
    const ext = file.name.split('.').pop();

    const attachmentUI = document.createElement('li');
    const attachmentIconUI = document.createElement('img');
    const attachmentNameUI = document.createElement('p');
    const attachmentSizeUI = document.createElement('p');
    const attachmentTrashUI = document.createElement('button');
    const attachmentTrashIconUI = document.createElement('img')

    attachmentUI.classList.add('presubmit-bar__attachment');
    attachmentUI.classList.add('attachment');

    attachmentIconUI.classList.add('attachment__icon');
    attachmentIconUI.src = '/images/icon/' + (ext ? ext : 'undefined') + '.png';

    attachmentNameUI.classList.add('attachment__filename');
    attachmentNameUI.innerText = file.name;

    attachmentTrashUI.classList.add('attachment__btn-remove');
    attachmentTrashIconUI.src = '/images/trash.png';

    attachmentSizeUI.classList.add('attachment__size');
    attachmentSizeUI.innerText = humanFileSize(file.size);

    attachmentTrashUI.appendChild(attachmentTrashIconUI);

    attachmentTrashUI.addEventListener('click', () => attachmentUI.remove() || onDelete());

    attachmentUI.appendChild(attachmentIconUI);
    attachmentUI.appendChild(attachmentNameUI);
    attachmentUI.appendChild(attachmentTrashUI);
    attachmentUI.appendChild(attachmentSizeUI);

    return attachmentUI;
}

!function setupSubmitButton() {
    const submitButton = document.getElementById('submit-button');
    const titleInput = document.getElementById('title-input');

    const submitURL = submitButton.getAttribute('data-action');
    const submitMethod = submitButton.getAttribute('data-method');
    const redirectURL = submitButton.getAttribute('data-redirect');

    if (!(submitButton instanceof HTMLButtonElement)) {
        console.error('#submit-button: ', submitButton);
        throw Error('#submit-button must be <button> element');
    }

    if (titleInput && !(titleInput instanceof HTMLInputElement)) {
        console.error('#title-input: ', titleInput);
        throw Error('#title-input must be <input> element');
    }

    if (typeof submitURL !== 'string' || submitURL === '')
        throw Error('#submit-button must have `data-action` attribute');

    if (typeof submitMethod !== 'string' || submitMethod === '')
        throw Error('#submit-button must have `data-method` attribute');

    if (typeof redirectURL !== 'string' || redirectURL === '')
        throw Error('#submit-button must have `data-redirect` attribute');

    submitButton.addEventListener('click', () => {
        /** @type {ClassicEditor} */
        const {editor} = window;

        if (!(editor instanceof ClassicEditor)) {
            console.error('window.editor = ', editor);
            throw Error('#submit-button must be deactivated until editor is not initialized');
        }

        const formData = new FormData;

        formData.append('title', titleInput ? titleInput.value : null);
        formData.append('body', editor.getData());
        formData.append('_method', submitMethod);

        newAttachments.forEach((file) => formData.append('attached[]', file));
        attachmentTrash.forEach((id) => formData.append('detached[]', id.toString()));

        fetch(submitURL, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute('content'),
            },
        }).then((res) => {
            if (res.status === 200) {
                location.href = redirectURL;
            }
        });
    });
}();

!function setupAttachmentTrashButtons() {
    /** @type {HTMLButtonElement[]}*/
    const trashButtons = Array.from(document.getElementsByClassName('attachment__btn-remove'));
    for (const button of trashButtons) {
        button.addEventListener('click', () => {
            attachmentTrash.push(parseInt(button.getAttribute('data-attachment-id')));
            button.closest('.attachment').remove();
            console.log('trash', attachmentTrash);
        });
    }
}();

!function setupFileAttachment() {
    const button = document.getElementById('attach-button');
    const fileInput = document.getElementById('file-input');
    const attachmentListUI = document.getElementById('attachments');

    if (!(fileInput instanceof HTMLInputElement && fileInput.getAttribute('type'))) {
        console.error('#file-input is not HTMLInputElement: ', fileInput);
        throw Error('#file-input must be <input type="file">');
    }

    button.addEventListener('click', () => fileInput.click());
    fileInput.addEventListener('change', () => {
        for (const file of fileInput.files) {
            newAttachments.push(file);
            attachmentListUI.appendChild(attachmentUIFactory(file, () => {
                newAttachments.splice(newAttachments.indexOf(file), 1);
            }));
        }
        console.log(newAttachments);
    });
}();

!async function setupEditor() {
    const submitButton = document.getElementById('submit-button');

    if (submitButton instanceof HTMLButtonElement) {
        submitButton.disabled = true;
    }

    const editor = await ClassicEditor
        .create(document.querySelector('#editor'), {
            toolbar: ['bold', 'italic', 'bulletedList', 'insertTable'],
            placeholder: document.getElementById('editor').getAttribute('data-placeholder'),
            table: {
                contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells'],
            },
        })
        .catch(error => {
            console.error('CKeditor have not been initialized due:', error);
        });

    window.editor = editor;

    if (submitButton instanceof HTMLButtonElement) {
        submitButton.disabled = false;
    }

    const buttons = Array.from(document.querySelectorAll('.editor__tool-btn'));

    for (const button of buttons) {
        const command = button.getAttribute('data-command');
        editor.commands.get(command).on('set:value', (info, name, value) => {
            if (value) button.classList.add('active');
            else button.classList.remove('active');
        });

        button.addEventListener('click', () => {
            editor.execute(command);
        });
    }
}();

///////////////////////////////
/// BASEMENT WITH HELPERS


function humanFileSize(bytes, dp = 1) {
    const thresh = 1024;

    if (Math.abs(bytes) < thresh) {
        return bytes + ' B';
    }

    const units = ['kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB']
    let u = -1;
    const r = 10 ** dp;

    do {
        bytes /= thresh;
        ++u;
    } while (Math.round(Math.abs(bytes) * r) / r >= thresh && u < units.length - 1);


    return bytes.toFixed(dp) + ' ' + units[u];
}

