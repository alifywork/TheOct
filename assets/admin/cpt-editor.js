/**
 * Turn TheOct Fields display-only "tab" markers into accessible editor tabs.
 * No field values are altered; controls remain in their original form.
 */
(function () {
    'use strict';

    function buildTabs(group, groupIndex) {
        var fields = Array.prototype.slice.call(group.children).filter(function (node) {
            return node.classList && node.classList.contains('theoct-content-field');
        });
        var markers = fields.filter(function (field) {
            return field.classList.contains('theoct-field-type-tab');
        });

        // Respect other field groups and the plugin's unenhanced UI.
        if (markers.length < 2 || group.dataset.theoctTabsReady === 'yes') {
            return;
        }

        var sections = [];
        var active = 0;
        var showingAll = false;
        var idBase = 'theoct-admin-sections-' + groupIndex;
        var current = null;

        function newSection(label) {
            var panel = document.createElement('section');
            var heading = document.createElement('h4');
            panel.className = 'theoct-admin-section';
            panel.id = idBase + '-panel-' + sections.length;
            panel.setAttribute('role', 'tabpanel');
            panel.tabIndex = 0;
            heading.className = 'theoct-admin-section-heading';
            heading.textContent = label;
            panel.appendChild(heading);
            group.appendChild(panel);
            sections.push({ label: label, panel: panel });
            return panel;
        }

        // A schema may start with fields before its first tab marker.
        fields.forEach(function (field) {
            if (field.classList.contains('theoct-field-type-tab')) {
                var label = field.querySelector('.theoct-display-tab strong');
                current = newSection(label ? label.textContent.trim() : 'Section ' + (sections.length + 1));
                field.remove(); // Display-only marker, not an input.
            } else {
                if (!current) {
                    current = newSection('General');
                }
                current.appendChild(field);
            }
        });

        if (!sections.length) {
            return;
        }

        var bar = document.createElement('div');
        var nav = document.createElement('div');
        var buttons = [];
        var showAll = document.createElement('button');

        bar.className = 'theoct-admin-toolbar';
        nav.className = 'theoct-admin-tabs';
        nav.setAttribute('role', 'tablist');
        nav.setAttribute('aria-label', 'Custom field sections');
        showAll.type = 'button';
        showAll.className = 'button theoct-admin-show-all';
        showAll.setAttribute('aria-pressed', 'false');
        showAll.textContent = 'Show all sections';

        sections.forEach(function (section, index) {
            var button = document.createElement('button');
            var count = section.panel.querySelectorAll(':scope > .theoct-content-field').length;
            var countText = document.createElement('small');

            button.type = 'button';
            button.className = 'theoct-admin-tab';
            button.id = idBase + '-tab-' + index;
            button.setAttribute('role', 'tab');
            button.setAttribute('aria-controls', section.panel.id);
            button.setAttribute('aria-selected', 'false');
            button.tabIndex = -1;
            button.appendChild(document.createTextNode(section.label));
            countText.className = 'theoct-admin-tab-count';
            countText.textContent = String(count);
            countText.setAttribute('aria-label', count + ' fields');
            button.appendChild(countText);
            section.panel.setAttribute('aria-labelledby', button.id);

            button.addEventListener('click', function () {
                setActive(index, false);
            });
            button.addEventListener('keydown', function (event) {
                var next = index;
                if (event.key === 'ArrowRight') {
                    next = (index + 1) % buttons.length;
                } else if (event.key === 'ArrowLeft') {
                    next = (index - 1 + buttons.length) % buttons.length;
                } else if (event.key === 'Home') {
                    next = 0;
                } else if (event.key === 'End') {
                    next = buttons.length - 1;
                } else {
                    return;
                }
                event.preventDefault();
                setActive(next, true);
            });

            buttons.push(button);
            nav.appendChild(button);
        });

        function setActive(index, focusButton) {
            active = index;
            showingAll = false;
            showAll.setAttribute('aria-pressed', 'false');
            showAll.textContent = 'Show all sections';
            nav.hidden = false;
            sections.forEach(function (section, i) {
                section.panel.hidden = i !== active;
                section.panel.setAttribute('role', 'tabpanel');
                buttons[i].setAttribute('aria-selected', i === active ? 'true' : 'false');
                buttons[i].tabIndex = i === active ? 0 : -1;
            });
            if (focusButton) {
                buttons[index].focus();
            }
        }

        showAll.addEventListener('click', function () {
            showingAll = !showingAll;
            showAll.setAttribute('aria-pressed', showingAll ? 'true' : 'false');
            showAll.textContent = showingAll ? 'Show one section' : 'Show all sections';
            nav.hidden = showingAll;

            sections.forEach(function (section, i) {
                section.panel.hidden = !showingAll && i !== active;
                section.panel.setAttribute('role', showingAll ? 'region' : 'tabpanel');
            });

            // Keep keyboard focus visible on the show-all control.
            if (!showingAll) {
                setActive(active, false);
            }
        });

        bar.appendChild(nav);
        bar.appendChild(showAll);
        group.insertBefore(bar, sections[0].panel);
        group.classList.add('theoct-admin-enhanced');
        group.dataset.theoctTabsReady = 'yes';
        setActive(0, false);
    }

    function init() {
        var metabox = document.getElementById('theoct-fields-content');
        if (!metabox) {
            return;
        }
        var groups = metabox.querySelectorAll('.theoct-content-group');
        Array.prototype.forEach.call(groups, buildTabs);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
