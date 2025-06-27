document.addEventListener('DOMContentLoaded', function() {
    // Tabs
    document.querySelectorAll('.wp-lsp-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            document.querySelectorAll('.wp-lsp-tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.wp-lsp-tab-content').forEach(tc => tc.classList.remove('active'));
            tab.classList.add('active');
            document.querySelector('.wp-lsp-tab-content[data-tab="'+tab.dataset.tab+'"]').classList.add('active');
        });
    });

    // Global switch: deshabilita el form entero
    const globalSwitch = document.getElementById('wp-lsp-global-enable');
    const form = document.getElementById('wp-lsp-form');
    function updateGlobalSwitch() {
        if (globalSwitch.checked) {
            form.querySelectorAll('input,select,textarea,button').forEach(e=>e.disabled=false);
        } else {
            form.querySelectorAll('input,select,textarea,button').forEach(e=>e.disabled=true);
            globalSwitch.disabled = false; // Mantén el switch operativo
        }
    }
    if (globalSwitch) globalSwitch.addEventListener('change', updateGlobalSwitch);
    updateGlobalSwitch();

    // Switch secciones: oculta los campos si la sección está desactivada
    document.querySelectorAll('.wp-lsp-section-switch input[type="checkbox"]').forEach(chk => {
        chk.addEventListener('change', function() {
            const tab = chk.closest('.wp-lsp-tab').dataset.tab;
            const content = document.querySelector('.wp-lsp-tab-content[data-tab="'+tab+'"]');
            if (content) content.style.display = chk.checked ? '' : 'none';
        });
        // Estado inicial
        const tab = chk.closest('.wp-lsp-tab').dataset.tab;
        const content = document.querySelector('.wp-lsp-tab-content[data-tab="'+tab+'"]');
        if (content && !chk.checked) content.style.display = 'none';
    });

    // Imagen: previsualización instantánea
    function updateImagePreview(input) {
        let url = input.value.trim();
        let preview = input.closest('.wp-lsp-image-wrap').querySelector('.wp-lsp-img-preview');
        if (!preview) return;
        if (url && /^(https?:)?\/\/.+\.(jpg|jpeg|png|webp|gif|svg)$/i.test(url)) {
            preview.src = url;
            preview.style.display = "block";
        } else {
            preview.src = "";
            preview.style.display = "none";
        }
    }
    document.querySelectorAll('.wp-lsp-image-url').forEach(input => {
        input.addEventListener('input', ()=>updateImagePreview(input));
        updateImagePreview(input);
    });
    document.body.addEventListener('input', function(e) {
        if (e.target.classList.contains('wp-lsp-image-url')) updateImagePreview(e.target);
    });

    // ==== REPEATER MEJORADO ====

    document.querySelectorAll('.wp-lsp-repeater').forEach(wrapper => {
        const table = wrapper.querySelector('.wp-lsp-repeater-table tbody');
        const addBtn = wrapper.querySelector('.wp-lsp-add-item');
        const saveBtn = wrapper.querySelector('.wp-lsp-save-item');
        const cancelBtn = wrapper.querySelector('.wp-lsp-cancel-edit');
        const editIdxInput = wrapper.querySelector('.wp-lsp-repeater-edit-idx');
        const form = wrapper.querySelector('.wp-lsp-repeater-form');
        const msg = wrapper.querySelector('.wp-lsp-repeater-msg');
        const fields = Array.from(form.querySelectorAll('input,textarea,select')).filter(f => !f.classList.contains('wp-lsp-repeater-edit-idx'));
        let editingIdx = null;

        // Añadir nuevo item
        if (addBtn) addBtn.addEventListener('click', function() {
            let newRow = [];
            let valid = true;
            fields.forEach(field => {
                let v = field.value;
                if (field.type === 'number') {
                    v = parseFloat(v.replace(',','.'));
                    if (field.id.toLowerCase().includes('ratingvalue')) {
                        if (v > 5) {
                            valid = false;
                            msg.textContent = "⚠️ El valor máximo de rating es 5.";
                            msg.style.color = "red";
                        }
                        if (v < 0) {
                            valid = false;
                            msg.textContent = "⚠️ El valor mínimo es 0.";
                            msg.style.color = "red";
                        }
                    }
                }
                newRow.push(v);
            });
            if (!valid) return;
            msg.textContent = "Añadido ✅";
            msg.style.color = "green";
            // Añade fila visual
            if (table) {
                let tr = document.createElement('tr');
                newRow.forEach(val => {
                    let td = document.createElement('td');
                    td.textContent = val;
                    tr.appendChild(td);
                });
                let tdAcc = document.createElement('td');
                tdAcc.innerHTML = "<button type='button' class='button wp-lsp-edit-item'>Editar</button> <button type='button' class='button wp-lsp-remove-item'>Eliminar</button>";
                tr.appendChild(tdAcc);
                table.appendChild(tr);
            }
            // Limpia formulario repeater
            fields.forEach(field => field.value = '');
            setTimeout(()=>{msg.textContent='';},1500);
        });

        // Eliminar fila visual
        if (table) table.addEventListener('click', function(e) {
            if (e.target.classList.contains('wp-lsp-remove-item')) {
                e.preventDefault();
                e.target.closest('tr').remove();
            }
        });

        // Editar fila (rellena form)
        if (table) table.addEventListener('click', function(e) {
            if (e.target.classList.contains('wp-lsp-edit-item')) {
                e.preventDefault();
                let tr = e.target.closest('tr');
                let tds = Array.from(tr.querySelectorAll('td')).slice(0, fields.length);
                tds.forEach((td, i) => fields[i].value = td.textContent);
                editingIdx = Array.prototype.indexOf.call(table.children, tr);
                editIdxInput.value = editingIdx;
                saveBtn.style.display = '';
                cancelBtn.style.display = '';
                addBtn.style.display = 'none';
            }
        });

        // Guardar edición
        if (saveBtn) saveBtn.addEventListener('click', function() {
            let valid = true;
            let vals = [];
            fields.forEach((field, i) => {
                let v = field.value;
                if (field.type === 'number') {
                    v = parseFloat(v.replace(',','.'));
                    if (field.id.toLowerCase().includes('ratingvalue')) {
                        if (v > 5) {
                            valid = false;
                            msg.textContent = "⚠️ El valor máximo de rating es 5.";
                            msg.style.color = "red";
                        }
                        if (v < 0) {
                            valid = false;
                            msg.textContent = "⚠️ El valor mínimo es 0.";
                            msg.style.color = "red";
                        }
                    }
                }
                vals.push(v);
            });
            if (!valid) return;
            // Actualiza fila
            let trs = Array.from(table.querySelectorAll('tr'));
            let tr = trs[parseInt(editIdxInput.value)];
            if (tr) {
                Array.from(tr.querySelectorAll('td')).slice(0, vals.length).forEach((td,i)=>{td.textContent=vals[i];});
            }
            fields.forEach(field => field.value = '');
            addBtn.style.display = '';
            saveBtn.style.display = 'none';
            cancelBtn.style.display = 'none';
            msg.textContent = "Editado ✅";
            msg.style.color = "green";
            setTimeout(()=>{msg.textContent='';},1500);
        });

        // Cancelar edición
        if (cancelBtn) cancelBtn.addEventListener('click', function() {
            fields.forEach(field => field.value = '');
            addBtn.style.display = '';
            saveBtn.style.display = 'none';
            cancelBtn.style.display = 'none';
            msg.textContent = "";
        });
    });
});