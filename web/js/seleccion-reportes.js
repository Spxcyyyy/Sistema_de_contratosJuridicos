(() => {
    const setup = () => {
        const panel = document.getElementById('seleccion-reportes');
        const grid = document.getElementById('contratos-grid');
        if (!panel || !grid) return;
        const key = panel.dataset.storageKey;
        const contador = document.getElementById('seleccion-contador');
        const boton = document.getElementById('reporte-seleccion-boton');
        const limpiar = document.getElementById('seleccion-limpiar');
        const todos = document.getElementById('seleccionar-pagina');
        const checks = [...grid.querySelectorAll('.contrato-seleccion')];
        let seleccion = new Set();
        const sinPersistencia = () => {
            document.getElementById('seleccion-ayuda').textContent = 'El navegador no permite guardar la selección. Genera el reporte antes de cambiar de página.';
        };
        const restaurar = () => {
            try {
                const saved = JSON.parse(sessionStorage.getItem(key) || '[]');
                if (Array.isArray(saved)) seleccion = new Set(saved.filter(id => /^[1-9]\d{0,9}$/.test(String(id))).map(String).slice(0, 500));
            } catch (_) { sinPersistencia(); }
        };
        restaurar();
        const sync = () => {
            checks.forEach(check => { check.checked = seleccion.has(check.value); });
            const marcados = checks.filter(check => check.checked).length;
            todos.checked = checks.length > 0 && marcados === checks.length;
            todos.indeterminate = marcados > 0 && marcados < checks.length;
            todos.disabled = checks.length === 0;
            contador.textContent = `${seleccion.size} contrato${seleccion.size === 1 ? '' : 's'} seleccionado${seleccion.size === 1 ? '' : 's'}`;
            boton.disabled = limpiar.disabled = seleccion.size === 0;
            document.getElementById('reporte-seleccion-ids').value = JSON.stringify([...seleccion]);
            try { sessionStorage.setItem(key, JSON.stringify([...seleccion])); } catch (_) { sinPersistencia(); }
        };
        grid.addEventListener('change', event => {
            if (event.target === todos) {
                const activar = todos.checked;
                const nuevos = checks.filter(check => !seleccion.has(check.value));
                if (activar && seleccion.size + nuevos.length > 500) {
                    alert('Puedes seleccionar hasta 500 contratos por reporte.');
                } else {
                    checks.forEach(check => activar ? seleccion.add(check.value) : seleccion.delete(check.value));
                }
            } else if (event.target.matches('.contrato-seleccion')) {
                if (event.target.checked && seleccion.size >= 500 && !seleccion.has(event.target.value)) {
                    alert('Puedes seleccionar hasta 500 contratos por reporte.');
                } else if (event.target.checked) seleccion.add(event.target.value);
                else seleccion.delete(event.target.value);
            } else return;
            sync();
        });
        limpiar.addEventListener('click', () => { seleccion.clear(); sync(); });
        document.getElementById('reporte-seleccion-form').addEventListener('submit', event => {
            if (!seleccion.size) { event.preventDefault(); return; }
            document.getElementById('reporte-seleccion-ids').value = JSON.stringify([...seleccion]);
        });
        window.addEventListener('pageshow', () => { restaurar(); sync(); });
        sync();
    };
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', setup);
    else setup();
})();
