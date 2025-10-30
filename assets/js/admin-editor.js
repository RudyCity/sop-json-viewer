/**
 * SOP JSON Viewer - Modern Admin Editor JavaScript (Vanilla JS)
 */

class SOPJSONEditor {
    constructor() {
        this.editor = null;
        this.validationTimer = null;
        this.currentSopId = 'default-sop';
        this.isLoading = false;
        this.templates = {
            basic: {
                title: "Basic SOP Template",
                description: "Simple SOP structure with basic sections",
                data: {
                    "title": "Standard Operating Procedure",
                    "description": "This is a basic SOP template for standard procedures",
                    "sections": [
                        {
                            "title": "Purpose",
                            "content": "Describe the purpose of this SOP and what it aims to achieve."
                        },
                        {
                            "title": "Scope",
                            "content": "Define the scope and applicability of this procedure."
                        },
                        {
                            "title": "Responsibilities",
                            "content": "Outline the roles and responsibilities of personnel involved."
                        }
                    ]
                }
            },
            advanced: {
                title: "Advanced SOP Template",
                description: "Complex SOP with nested subsections",
                data: {
                    "title": "Advanced Standard Operating Procedure",
                    "description": "Comprehensive SOP with detailed sections and subsections",
                    "sections": [
                        {
                            "title": "Preparation Phase",
                            "content": "Initial preparation and setup requirements",
                            "subsections": [
                                {
                                    "title": "Equipment Setup",
                                    "content": "Detailed equipment preparation instructions"
                                },
                                {
                                    "title": "Safety Checks",
                                    "content": "Pre-operation safety verification procedures"
                                }
                            ]
                        },
                        {
                            "title": "Execution Phase",
                            "content": "Main procedure execution steps",
                            "subsections": [
                                {
                                    "title": "Step-by-Step Process",
                                    "content": "Detailed execution instructions with quality checkpoints"
                                },
                                {
                                    "title": "Quality Assurance",
                                    "content": "Quality control and verification procedures"
                                }
                            ]
                        },
                        {
                            "title": "Completion Phase",
                            "content": "Finalization and documentation requirements"
                        }
                    ]
                }
            },
            safety: {
                title: "Safety Protocol Template",
                description: "Safety-focused SOP with emergency procedures",
                data: {
                    "title": "Safety Protocol SOP",
                    "description": "Comprehensive safety procedures and emergency protocols",
                    "sections": [
                        {
                            "title": "Safety Requirements",
                            "content": "Personal protective equipment (PPE) and safety gear requirements",
                            "subsections": [
                                {
                                    "title": "Required PPE",
                                    "content": "List of mandatory personal protective equipment"
                                },
                                {
                                    "title": "Safety Training",
                                    "content": "Required training and certifications"
                                }
                            ]
                        },
                        {
                            "title": "Risk Assessment",
                            "content": "Identified risks and mitigation strategies",
                            "subsections": [
                                {
                                    "title": "Hazard Identification",
                                    "content": "Potential hazards and risk levels"
                                },
                                {
                                    "title": "Control Measures",
                                    "content": "Implementing risk control measures"
                                }
                            ]
                        },
                        {
                            "title": "Emergency Procedures",
                            "content": "Emergency response and first aid procedures",
                            "subsections": [
                                {
                                    "title": "Emergency Contacts",
                                    "content": "List of emergency contacts and procedures"
                                },
                                {
                                    "title": "First Aid Response",
                                    "content": "Immediate first aid measures for common incidents"
                                }
                            ]
                        }
                    ]
                }
            },
            links: {
                title: "Link Collection Template",
                description: "SOP with interactive link lists and resource collections",
                data: {
                    "title": "Resource Collection SOP",
                    "description": "Comprehensive collection of links and resources organized by category",
                    "sections": [
                        {
                            "title": "📚 Documentation & References",
                            "sort": true,
                            "sort_by": "title",
                            "sort_order": "asc",
                            "content": [
                                {
                                    "type": "link",
                                    "title": "API Documentation",
                                    "url": "https://docs.example.com/api",
                                    "target": "_blank"
                                },
                                {
                                    "type": "link",
                                    "title": "Training Materials",
                                    "url": "/training-materials"
                                },
                                {
                                    "type": "link",
                                    "title": "User Manual PDF",
                                    "url": "/wp-content/uploads/user-manual.pdf",
                                    "target": "_blank"
                                }
                            ]
                        },
                        {
                            "title": "🔗 Important Links",
                            "content": [
                                {
                                    "type": "link",
                                    "title": "Company Website",
                                    "url": "https://company-website.com",
                                    "target": "_blank"
                                },
                                {
                                    "type": "link",
                                    "title": "Employee Portal",
                                    "url": "https://portal.company.com",
                                    "target": "_blank"
                                },
                                {
                                    "type": "link",
                                    "title": "Internal Wiki",
                                    "url": "/wiki"
                                }
                            ]
                        },
                        {
                            "title": "🛠️ Tools & Software",
                            "content": [
                                {
                                    "type": "link",
                                    "title": "Project Management Tool",
                                    "url": "https://pm-tool.company.com",
                                    "target": "_blank"
                                },
                                {
                                    "type": "link",
                                    "title": "Time Tracking System",
                                    "url": "/time-tracking"
                                }
                            ],
                            "subsections": [
                                {
                                    "title": "Access Information",
                                    "content": "<p>For tool access, contact IT support or use your company credentials.</p>"
                                },
                                {
                                    "title": "Additional Resources",
                                    "content": [
                                        {
                                            "type": "link",
                                            "title": "Video Tutorials",
                                            "url": "/tutorials"
                                        },
                                        {
                                            "type": "link",
                                            "title": "FAQ Section",
                                            "url": "/faq"
                                        }
                                    ]
                                }
                            ]
                        },
                        {
                            "title": "📞 Support & Contacts",
                            "content": [
                                {
                                    "type": "link",
                                    "title": "IT Helpdesk",
                                    "url": "/helpdesk/it"
                                },
                                {
                                    "type": "link",
                                    "title": "Emergency Contacts",
                                    "url": "/emergency-contacts"
                                },
                                {
                                    "type": "link",
                                    "title": "Submit Support Ticket",
                                    "url": "/support/ticket"
                                }
                            ]
                        }
                    ]
                }
            }
        };

        this.init();
    }

    init() {
        this.initCodeMirror();
        this.bindEvents();
        this.loadDefaultData();
    }

    initCodeMirror() {
        const textarea = document.getElementById('sjp-json-textarea');
        if (textarea && typeof wp !== 'undefined' && wp.codeEditor) {
            this.editor = wp.codeEditor.initialize(textarea, {
                codemirror: {
                    lineNumbers: true,
                    mode: 'application/json',
                    theme: 'default',
                    indentUnit: 2,
                    matchBrackets: true,
                    autoCloseBrackets: true,
                    foldGutter: true,
                    gutters: ['CodeMirror-linenumbers', 'CodeMirror-foldgutter']
                }
            });

            this.editor.codemirror.on('change', (cm) => {
                this.scheduleValidation();
            });
        }
    }

    bindEvents() {
        // Form submission
        const form = document.getElementById('sjp-sop-form');
        if (form) {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                this.saveData();
            });
        }

        // SOP ID change
        const sopIdInput = document.getElementById('sop_id');
        if (sopIdInput) {
            sopIdInput.addEventListener('change', () => {
                this.currentSopId = sopIdInput.value;
                this.loadExistingData();
            });
        }

        // Button events
        this.bindButtonEvents();

        // Tab switching
        this.bindTabEvents();

        // Template selection
        this.bindTemplateEvents();

        // Help modal
        this.bindHelpEvents();

        // Saved SOPs table events
        this.bindSavedSopsEvents();
    }

    bindButtonEvents() {
        const loadButton = document.getElementById('sjp-load-data');
        if (loadButton) {
            loadButton.addEventListener('click', () => {
                this.loadExistingData();
            });
        }

        const exportButton = document.getElementById('sjp-export-data');
        if (exportButton) {
            exportButton.addEventListener('click', () => {
                this.exportData();
            });
        }

        const formatButton = document.getElementById('sjp-format-json');
        if (formatButton) {
            formatButton.addEventListener('click', () => {
                this.formatJSON();
            });
        }

        const clearButton = document.getElementById('sjp-clear-editor');
        if (clearButton) {
            clearButton.addEventListener('click', () => {
                this.clearEditor();
            });
        }

        const refreshPreview = document.getElementById('sjp-preview-refresh');
        if (refreshPreview) {
            refreshPreview.addEventListener('click', () => {
                this.refreshPreview();
            });
        }

        const fullscreenPreview = document.getElementById('sjp-preview-fullscreen');
        if (fullscreenPreview) {
            fullscreenPreview.addEventListener('click', () => {
                this.toggleFullscreenPreview();
            });
        }
    }

    bindTabEvents() {
        const tabs = document.querySelectorAll('.sjp-tab');
        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const targetTab = tab.dataset.tab;
                this.switchTab(targetTab);
            });
        });
    }

    bindTemplateEvents() {
        const templateCards = document.querySelectorAll('.sjp-template-card');
        templateCards.forEach(card => {
            card.addEventListener('click', () => {
                const templateType = card.dataset.template;
                this.loadTemplate(templateType);
            });
        });
    }

    bindHelpEvents() {
        const helpToggle = document.getElementById('sjp-help-toggle');
        const helpModal = document.getElementById('sjp-help-modal');
        const helpClose = document.getElementById('sjp-help-close');

        if (helpToggle) {
            helpToggle.addEventListener('click', () => {
                helpModal.classList.add('active');
            });
        }

        if (helpClose) {
            helpClose.addEventListener('click', () => {
                helpModal.classList.remove('active');
            });
        }

        if (helpModal) {
            helpModal.addEventListener('click', (e) => {
                if (e.target === helpModal) {
                    helpModal.classList.remove('active');
                }
            });
        }
    }

    bindSavedSopsEvents() {
        // Edit SOP button
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('sjp-load-sop') || e.target.closest('.sjp-load-sop')) {
                e.preventDefault();
                const button = e.target.classList.contains('sjp-load-sop') ? e.target : e.target.closest('.sjp-load-sop');
                const sopId = button.getAttribute('data-sop-id');
                if (sopId) {
                    this.loadSopById(sopId);
                }
            }
        });

        // Delete SOP button
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('sjp-delete-sop') || e.target.closest('.sjp-delete-sop')) {
                e.preventDefault();
                const button = e.target.classList.contains('sjp-delete-sop') ? e.target : e.target.closest('.sjp-delete-sop');
                const sopId = button.getAttribute('data-sop-id');
                if (sopId) {
                    this.deleteSopById(sopId);
                }
            }
        });
    }

    loadSopById(sopId) {
        if (!this.editor || this.isLoading) return;

        // Prevent multiple simultaneous loads
        this.isLoading = true;

        // Clear any existing validation timer
        if (this.validationTimer) {
            clearTimeout(this.validationTimer);
            this.validationTimer = null;
        }

        // Update SOP ID input
        const sopIdInput = document.getElementById('sop_id');
        if (sopIdInput) {
            sopIdInput.value = sopId;
            this.currentSopId = sopId;
        }

        // Clear editor first
        this.editor.codemirror.setValue('');
        this.clearPreview();

        // Show loading state
        this.showValidationStatus('warning', `Loading SOP: ${sopId}...`);

        // Disable form inputs during loading
        this.setFormDisabled(true);

        this.makeAjaxRequest(sjp_ajax.ajax_url, {
            action: 'sjp_load_sop_data',
            nonce: sjp_ajax.nonce,
            sop_id: sopId
        }).then((response) => {
            if (response.success && response.data) {
                this.editor.codemirror.setValue(JSON.stringify(response.data, null, 2));
                this.validateJSON();
                this.showValidationStatus('success', `✅ SOP "${sopId}" loaded successfully`);
            } else {
                this.showValidationStatus('error', `❌ SOP "${sopId}" not found`);
                // Load default data if SOP not found
                this.loadDefaultData();
            }
        }).catch(() => {
            this.showValidationStatus('error', `❌ Error loading SOP "${sopId}"`);
            // Load default data on error
            this.loadDefaultData();
        }).finally(() => {
            // Re-enable form inputs
            this.setFormDisabled(false);
            this.isLoading = false;
        });
    }

    deleteSopById(sopId) {
        if (!confirm(`Are you sure you want to delete SOP "${sopId}"? This action cannot be undone.`)) {
            return;
        }

        // Show loading state
        this.showValidationStatus('warning', `Deleting SOP: ${sopId}...`);

        this.makeAjaxRequest(sjp_ajax.ajax_url, {
            action: 'sjp_delete_sop_data',
            nonce: sjp_ajax.nonce,
            sop_id: sopId
        }).then((response) => {
            if (response.success) {
                this.showValidationStatus('success', `✅ SOP "${sopId}" deleted successfully`);
                // Refresh page to update the table
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                this.showValidationStatus('error', `❌ ${response.data}`);
            }
        }).catch(() => {
            this.showValidationStatus('error', `❌ Error deleting SOP "${sopId}"`);
        });
    }

    switchTab(tabName) {
        // Update tab buttons
        document.querySelectorAll('.sjp-tab').forEach(tab => {
            tab.classList.remove('active');
        });
        document.querySelector(`[data-tab="${tabName}"]`).classList.add('active');

        // Update tab content
        document.querySelectorAll('.sjp-tab-content').forEach(content => {
            content.classList.remove('active');
        });
        document.getElementById(`${tabName}-tab`).classList.add('active');
    }

    loadTemplate(templateType) {
        const template = this.templates[templateType];
        if (template && this.editor) {
            this.editor.codemirror.setValue(JSON.stringify(template.data, null, 2));
            this.validateJSON();
            this.switchTab('editor');
        }
    }

    formatJSON() {
        if (!this.editor) return;
        
        try {
            const jsonData = this.editor.codemirror.getValue();
            const parsed = JSON.parse(jsonData);
            this.editor.codemirror.setValue(JSON.stringify(parsed, null, 2));
            this.validateJSON();
        } catch (error) {
            this.showValidationStatus('error', `Cannot format invalid JSON: ${error.message}`);
        }
    }

    clearEditor() {
        if (!this.editor) return;
        
        if (confirm('Are you sure you want to clear the editor?')) {
            this.editor.codemirror.setValue('');
            this.clearPreview();
            this.showValidationStatus('warning', 'Editor cleared');
        }
    }

    refreshPreview() {
        this.validateJSON();
    }

    toggleFullscreenPreview() {
        const previewPanel = document.querySelector('.sjp-preview-panel');
        if (previewPanel) {
            previewPanel.classList.toggle('fullscreen');
        }
    }

    scheduleValidation() {
        clearTimeout(this.validationTimer);
        this.validationTimer = setTimeout(() => {
            this.validateJSON();
        }, 1000);
    }

    validateJSON() {
        if (!this.editor) return;

        const jsonData = this.editor.codemirror.getValue();
        const statusElement = document.getElementById('sjp-validation-status');

        if (!statusElement) return;

        if (!jsonData.trim()) {
            this.showValidationStatus('warning', 'JSON editor is empty');
            this.clearPreview();
            return;
        }

        // Basic JSON validation
        try {
            const parsed = JSON.parse(jsonData);

            // Custom validation using fetch API
            this.makeAjaxRequest(sjp_ajax.ajax_url, {
                action: 'sjp_validate_json',
                nonce: sjp_ajax.nonce,
                json_data: jsonData
            }).then((response) => {
                if (response.success) {
                    this.showValidationStatus('success', '✅ JSON valid and structure correct');
                    this.updatePreview(parsed);
                } else {
                    this.showValidationStatus('error', `❌ ${response.data}`);
                    this.clearPreview();
                }
            }).catch(() => {
                this.showValidationStatus('error', '❌ Error validating JSON');
                this.clearPreview();
            });

        } catch (error) {
            this.showValidationStatus('error', `❌ JSON Syntax Error: ${error.message}`);
            this.clearPreview();
        }
    }

    showValidationStatus(type, message) {
        const statusElement = document.getElementById('sjp-validation-status');
        if (statusElement) {
            statusElement.className = `sjp-validation-status notice-${type}`;
            statusElement.innerHTML = `<p>${message}</p>`;
        }
    }

    clearPreview() {
        const previewContainer = document.getElementById('sjp-preview-container');
        if (previewContainer) {
            previewContainer.innerHTML = `
                <div class="sjp-preview-placeholder">
                    <div class="sjp-preview-icon">👁️</div>
                    <h3>Preview</h3>
                    <p>Preview will appear here after you enter valid JSON data.</p>
                </div>
            `;
        }
    }

    makeAjaxRequest(url, data) {
        return fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams(data)
        }).then(response => response.json());
    }

    saveData() {
        if (!this.editor) return;

        const jsonData = this.editor.codemirror.getValue();
        const sopIdInput = document.getElementById('sop_id');
        const statusElement = document.getElementById('sjp-validation-status');

        if (!sopIdInput || !statusElement) return;

        const sopId = sopIdInput.value;

        if (!jsonData.trim()) {
            alert('JSON data cannot be empty');
            return;
        }

        // Show loading state
        this.showValidationStatus('warning', 'Saving data...');

        this.makeAjaxRequest(sjp_ajax.ajax_url, {
            action: 'sjp_save_sop_data',
            nonce: sjp_ajax.nonce,
            sop_id: sopId,
            sop_data: jsonData
        }).then((response) => {
            if (response.success) {
                this.showValidationStatus('success', '✅ Data saved successfully');
            } else {
                this.showValidationStatus('error', `❌ ${response.data}`);
            }
        }).catch(() => {
            this.showValidationStatus('error', '❌ Error saving data');
        });
    }

    loadExistingData() {
        if (!this.editor || this.isLoading) return;

        const sopIdInput = document.getElementById('sop_id');
        if (!sopIdInput) return;

        const sopId = sopIdInput.value;

        // Prevent loading if SOP ID is empty or same as current
        if (!sopId.trim() || sopId === this.currentSopId) return;

        // Prevent multiple simultaneous loads
        this.isLoading = true;

        // Clear any existing validation timer
        if (this.validationTimer) {
            clearTimeout(this.validationTimer);
            this.validationTimer = null;
        }

        // Clear editor first
        this.editor.codemirror.setValue('');
        this.clearPreview();

        // Show loading state
        this.showValidationStatus('warning', 'Loading existing data...');

        // Disable form inputs during loading
        this.setFormDisabled(true);

        this.makeAjaxRequest(sjp_ajax.ajax_url, {
            action: 'sjp_load_sop_data',
            nonce: sjp_ajax.nonce,
            sop_id: sopId
        }).then((response) => {
            if (response.success && response.data) {
                this.editor.codemirror.setValue(JSON.stringify(response.data, null, 2));
                this.currentSopId = sopId;
                this.validateJSON();
            } else {
                // Load default template
                this.loadDefaultData();
            }
        }).catch(() => {
            this.loadDefaultData();
        }).finally(() => {
            // Re-enable form inputs
            this.setFormDisabled(false);
            this.isLoading = false;
        });
    }

    loadDefaultData() {
        if (!this.editor) return;

        const defaultData = {
            "title": "Contoh SOP",
            "description": "Deskripsi singkat tentang SOP ini",
            "sections": [
                {
                    "title": "Bagian 1: Pengenalan",
                    "content": "Konten pengenalan dengan **formatting** dan [link](https://example.com).",
                    "subsections": [
                        {
                            "title": "Sub-bagian 1.1",
                            "content": "Konten sub-bagian pertama"
                        }
                    ]
                },
                {
                    "title": "Bagian 2: Proses",
                    "content": "Konten proses utama"
                }
            ]
        };

        this.editor.codemirror.setValue(JSON.stringify(defaultData, null, 2));
        this.validateJSON();
    }

    exportData() {
        if (!this.editor) return;

        const jsonData = this.editor.codemirror.getValue();
        const sopIdInput = document.getElementById('sop_id');
        if (!sopIdInput) return;

        const sopId = sopIdInput.value;
        const blob = new Blob([jsonData], { type: 'application/json' });
        const url = URL.createObjectURL(blob);

        const a = document.createElement('a');
        a.href = url;
        a.download = `sop-${sopId}-${new Date().toISOString().split('T')[0]}.json`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    }

    updatePreview(data) {
        const previewContainer = document.getElementById('sjp-preview-container');
        if (!previewContainer) return;

        // Clear previous content and reset
        previewContainer.innerHTML = '';

        // Generate proper accordion HTML
        let preview = '<div class="sop-json-viewer" data-sop-id="preview">';

        if (data.title) {
            preview += `<h2 class="sop-title">${this.escapeHtml(data.title)}</h2>`;
        }

        if (data.description) {
            preview += `<p class="sop-description">${this.escapeHtml(data.description)}</p>`;
        }

        preview += '<div class="sop-accordion" role="tablist" aria-multiselectable="true">';
        
        if (data.sections && data.sections.length > 0) {
            preview += this.renderSectionsForPreview(data.sections);
        }
        
        preview += '</div></div>';

        previewContainer.innerHTML = preview;

        // Initialize accordion in preview with a small delay to ensure DOM is ready
        setTimeout(() => {
            this.initializePreviewAccordion();
        }, 50);
    }

    renderSectionsForPreview(sections) {
        let html = '';
        sections.forEach((section, index) => {
            const sectionId = `preview-section-${index}`;
            
            html += `<div class="sop-section">`;
            html += `<button class="sop-section-header"
                              id="header-${sectionId}"
                              aria-controls="content-${sectionId}"
                              aria-expanded="false"
                              role="tab"
                              type="button">
                <span>${this.escapeHtml(section.title)}</span>
                <span class="sop-toggle-icon" aria-hidden="true">+</span>
            </button>`;
            
            html += `<div class="sop-section-content"
                             id="content-${sectionId}"
                             aria-labelledby="header-${sectionId}"
                             role="tabpanel"
                             aria-expanded="false"
                             hidden>`;
            
            if (section.content) {
                html += `<div class="sop-content">${this.processContent(section.content)}</div>`;
            }
            
            if (section.subsections && section.subsections.length > 0) {
                html += '<div class="sop-subsections">';
                html += this.renderSectionsForPreview(section.subsections);
                html += '</div>';
            }
            
            html += '</div></div>';
        });
        return html;
    }

    processContent(content) {
        if (Array.isArray(content)) {
            // Sort link array by title
            const sortedContent = [...content].sort((a, b) => {
                if (!a.title || !b.title) return 0;
                return a.title.localeCompare(b.title, undefined, { sensitivity: 'base' });
            });
            
            // Process sorted link array
            let html = '';
            sortedContent.forEach(item => {
                if (item.type === 'link') {
                    const title = this.escapeHtml(item.title || '');
                    const url = this.escapeHtml(item.url || '#');
                    const target = item.target || '_self';
                    html += `<div class="sop-link-item">
                        <a href="${url}" target="${target}" class="sop-link">
                            <span class="sop-link-title">${title}</span>
                            <span class="sop-link-icon" aria-hidden="true">→</span>
                        </a>
                    </div>`;
                }
            });
            return html;
        } else {
            // Process markdown-style content to HTML
            return content
                .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                .replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2" target="_blank">$1</a>')
                .replace(/\n/g, '<br>');
        }
    }

    initializePreviewAccordion() {
        // Initialize accordion functionality for preview
        const previewContainer = document.getElementById('sjp-preview-container');
        if (previewContainer) {
            const accordionElement = previewContainer.querySelector('.sop-json-viewer');
            if (accordionElement && typeof SOPAccordion !== 'undefined') {
                // Add unique ID for preview to avoid conflicts
                accordionElement.setAttribute('data-sop-id', 'preview-' + Date.now());
                
                // Check if already initialized to prevent duplicates
                if (!accordionElement.hasAttribute('data-sop-initialized')) {
                    new SOPAccordion(accordionElement);
                    accordionElement.setAttribute('data-sop-initialized', 'true');
                }
            } else {
                // Fallback simple accordion functionality
                this.initSimpleAccordion(previewContainer);
            }
        }
    }

    initSimpleAccordion(container) {
        const headers = container.querySelectorAll('.sop-section-header');
        headers.forEach(header => {
            header.addEventListener('click', () => {
                const section = header.closest('.sop-section');
                const content = section.querySelector('.sop-section-content');
                const isExpanded = header.getAttribute('aria-expanded') === 'true';
                
                if (isExpanded) {
                    header.setAttribute('aria-expanded', 'false');
                    content.setAttribute('hidden', 'hidden');
                } else {
                    header.setAttribute('aria-expanded', 'true');
                    content.removeAttribute('hidden');
                }
            });
        });
    }

    setFormDisabled(disabled) {
        const form = document.getElementById('sjp-sop-form');
        if (form) {
            const inputs = form.querySelectorAll('input, button, textarea, select');
            inputs.forEach(input => {
                if (disabled) {
                    input.setAttribute('disabled', 'disabled');
                } else {
                    input.removeAttribute('disabled');
                }
            });
        }
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Initialize when document is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        new SOPJSONEditor();
    });
} else {
    new SOPJSONEditor();
}