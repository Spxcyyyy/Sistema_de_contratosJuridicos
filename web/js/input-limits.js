(function () {
    'use strict';
    const selector = 'input:not([type]), input[type="text"], input[type="search"], input[type="email"], input[type="password"], input[type="tel"], input[type="url"], textarea';
    function limitFields(root) {
        function limit(field) {
            if (!field.hasAttribute('maxlength') || field.maxLength > 50) {
                field.maxLength = 50;
            }
        }
        if (root.matches && root.matches(selector)) limit(root);
        root.querySelectorAll(selector).forEach(limit);
    }
    // Restrict only fields explicitly marked by the form.
    document.addEventListener('beforeinput', function (event) {
        if (event.target.matches('[data-letters-only="true"]') &&
            !event.isComposing && event.data && /[^\p{L}\p{M} ]/u.test(event.data)) {
            event.preventDefault();
        }
    });
    function removeInvalidCharacters(field) {
        const value = field.value;
        const cleaned = value.replace(/[^\p{L}\p{M} ]/gu, '');
        if (value === cleaned) return;
        const start = field.selectionStart;
        const end = field.selectionEnd;
        field.value = cleaned;
        if (start !== null && end !== null) {
            field.setSelectionRange(
                value.slice(0, start).replace(/[^\p{L}\p{M} ]/gu, '').length,
                value.slice(0, end).replace(/[^\p{L}\p{M} ]/gu, '').length
            );
        }
    }
    document.addEventListener('input', function (event) {
        if (!event.isComposing && event.target.matches('[data-letters-only="true"]')) {
            removeInvalidCharacters(event.target);
        }
    }, true);
    document.addEventListener('compositionend', function (event) {
        if (event.target.matches('[data-letters-only="true"]')) removeInvalidCharacters(event.target);
    }, true);
    document.addEventListener('DOMContentLoaded', function () {
        limitFields(document);
        // Also cover grid filters replaced by PJAX and dynamically added fields.
        new MutationObserver(function (mutations) {
            mutations.forEach(function (mutation) {
                mutation.addedNodes.forEach(function (node) {
                    if (node.nodeType === 1) limitFields(node);
                });
            });
        }).observe(document.body, {childList: true, subtree: true});
    });
})();
