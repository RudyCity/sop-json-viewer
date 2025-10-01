# 04 - Build Accordion Frontend

## Tujuan Step
Implementasi JavaScript untuk interactivity accordion dan enhancement frontend experience.

## Langkah Detail

### 4.1 Create Frontend JavaScript
Update file `assets/js/sop-accordion.js`:

```javascript
(function($) {
    'use strict';

    class SOPAccordion {
        constructor(element) {
            this.element = $(element);
            this.sopId = this.element.data('sop-id');
            this.currentlyOpen = null;
            this.animationDuration = 300;

            this.init();
        }

        init() {
            this.bindEvents();
            this.setupAccessibility();
            this.loadNestedData();
        }

        bindEvents() {
            // Toggle section
            this.element.on('click', '.sop-section-header', (e) => {
                e.preventDefault();
                const $header = $(e.currentTarget);
                const $section = $header.closest('.sop-section');

                this.toggleSection($header, $section);
            });

            // Keyboard navigation
            this.element.on('keydown', '.sop-section-header', (e) => {
                this.handleKeyboardNavigation(e);
            });

            // Click pada links dalam content
            this.element.on('click', '.sop-content a', (e) => {
                const $link = $(e.currentTarget);
                const href = $link.attr('href');

                // Handle internal links jika diperlukan
                if (href && href.startsWith('#')) {
                    e.preventDefault();
                    this.scrollToSection(href.substring(1));
                }
            });
        }

        toggleSection($header, $section) {
            const $content = $section.find('.sop-section-content');
            const isExpanded = $header.attr('aria-expanded') === 'true';

            if (isExpanded) {
                this.closeSection($header, $content);
            } else {
                this.openSection($header, $content);
            }
        }

        openSection($header, $content) {
            // Close currently open section jika ada
            if (this.currentlyOpen) {
                const $currentHeader = this.currentlyOpen.header;
                const $currentContent = this.currentlyOpen.content;
                this.closeSection($currentHeader, $currentContent);
            }

            // Open new section
            $header.attr('aria-expanded', 'true');
            $content.attr('hidden', false);

            // Animate content
            $content.css('max-height', '0px');
            $content.animate({
                'max-height': $content.prop('scrollHeight') + 'px',
                'padding-top': '20px',
                'padding-bottom': '20px'
            }, this.animationDuration, 'easeInOutCubic');

            // Update currently open
            this.currentlyOpen = {
                header: $header,
                content: $content
            };

            // Trigger custom event
            this.element.trigger('sop:sectionOpened', [$header, $content]);
        }

        closeSection($header, $content) {
            $header.attr('aria-expanded', 'false');

            // Animate content
            $content.animate({
                'max-height': '0px',
                'padding-top': '0px',
                'padding-bottom': '0px'
            }, this.animationDuration, 'easeInOutCubic', () => {
                $content.attr('hidden', true);
            });

            // Clear currently open
            if (this.currentlyOpen &&
                this.currentlyOpen.header.is($header) &&
                this.currentlyOpen.content.is($content)) {
                this.currentlyOpen = null;
            }

            // Trigger custom event
            this.element.trigger('sop:sectionClosed', [$header, $content]);
        }

        handleKeyboardNavigation(e) {
            const $header = $(e.currentTarget);
            const $section = $header.closest('.sop-section');
            const $accordion = $section.closest('.sop-accordion');
            const $headers = $accordion.find('.sop-section-header');

            let currentIndex = $headers.index($header);
            let targetIndex = currentIndex;

            switch (e.keyCode) {
                case 13: // Enter
                case 32: // Space
                    e.preventDefault();
                    this.toggleSection($header, $section);
                    break;

                case 38: // Arrow Up
                    e.preventDefault();
                    targetIndex = currentIndex > 0 ? currentIndex - 1 : $headers.length - 1;
                    $headers.eq(targetIndex).focus();
                    break;

                case 40: // Arrow Down
                    e.preventDefault();
                    targetIndex = currentIndex < $headers.length - 1 ? currentIndex + 1 : 0;
                    $headers.eq(targetIndex).focus();
                    break;

                case 36: // Home
                    e.preventDefault();
                    $headers.first().focus();
                    break;

                case 35: // End
                    e.preventDefault();
                    $headers.last().focus();
                    break;
            }
        }

        setupAccessibility() {
            // Ensure proper ARIA attributes
            this.element.find('.sop-section-header').each((index, header) => {
                const $header = $(header);
                const sectionId = $header.attr('id') || `sop-header-${this.sopId}-${index}`;
                const contentId = $header.attr('aria-controls') || `sop-content-${this.sopId}-${index}`;

                $header.attr({
                    'id': sectionId,
                    'role': 'tab',
                    'tabindex': index === 0 ? '0' : '-1',
                    'aria-expanded': 'false'
                });

                const $content = $header.next('.sop-section-content');
                $content.attr({
                    'id': contentId,
                    'role': 'tabpanel',
                    'aria-labelledby': sectionId,
                    'hidden': 'hidden'
                });
            });

            // Set first header as tabindex 0
            this.element.find('.sop-section-header').first().attr('tabindex', '0');
        }

        loadNestedData() {
            // Load additional data untuk nested accordions jika diperlukan
            const nestedSections = this.element.find('.sop-subsections .sop-section');

            if (nestedSections.length > 0) {
                // Initialize nested accordions
                nestedSections.each((index, section) => {
                    new SOPAccordion(section);
                });
            }
        }

        scrollToSection(sectionId) {
            const $target = $(`#${sectionId}`);
            if ($target.length) {
                $('html, body').animate({
                    scrollTop: $target.offset().top - 20
                }, 500);
            }
        }

        // Public methods untuk external control
        openSectionByIndex(index) {
            const $header = this.element.find('.sop-section-header').eq(index);
            if ($header.length) {
                const $section = $header.closest('.sop-section');
                this.openSection($header, $section.find('.sop-section-content'));
            }
        }

        closeAllSections() {
            if (this.currentlyOpen) {
                this.closeSection(this.currentlyOpen.header, this.currentlyOpen.content);
            }
        }

        getOpenSectionIndex() {
            if (this.currentlyOpen) {
                return this.element.find('.sop-section-header').index(this.currentlyOpen.header);
            }
            return -1;
        }
    }

    // jQuery easing function untuk smooth animation
    $.extend($.easing, {
        easeInOutCubic: function (x, t, b, c, d) {
            if ((t/=d/2) < 1) return c/2*t*t*t + b;
            return c/2*((t-=2)*t*t + 2) + b;
        }
    });

    // Auto-initialize accordions when document is ready
    $(document).ready(() => {
        $('.sop-json-viewer').each((index, element) => {
            new SOPAccordion(element);
        });
    });

    // Export untuk external use jika diperlukan
    window.SOPAccordion = SOPAccordion;

})(jQuery);
```

### 4.2 Enhance CSS dengan Animations
Update file `assets/css/sop-accordion.css` dengan animations dan enhancements:

```css
/* ... existing CSS ... */

/* Enhanced animations */
.sop-section-content {
    padding: 0;
    overflow: hidden;
    max-height: 0;
    transition: max-height 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94),
                padding 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    background-color: #fff;
}

.sop-section-content:not([hidden]) {
    max-height: 1000px;
    padding: 20px;
    background-color: #fff;
}

/* Loading state */
.sop-json-viewer.loading {
    opacity: 0.6;
    pointer-events: none;
}

.sop-json-viewer.loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 20px;
    height: 20px;
    margin: -10px 0 0 -10px;
    border: 2px solid #007cba;
    border-radius: 50%;
    border-top-color: transparent;
    animation: sop-spin 1s linear infinite;
}

@keyframes sop-spin {
    to { transform: rotate(360deg); }
}

/* Focus styles untuk better accessibility */
.sop-section-header:focus {
    outline: 2px solid #007cba;
    outline-offset: -2px;
    box-shadow: 0 0 0 4px rgba(0, 124, 186, 0.2);
}

.sop-section-header:focus:not(:focus-visible) {
    outline: none;
    box-shadow: none;
}

/* High contrast mode support */
@media (prefers-contrast: high) {
    .sop-section-header {
        border: 2px solid currentColor;
    }

    .sop-accordion {
        border: 2px solid currentColor;
    }
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
    .sop-section-content {
        transition: none;
    }

    .sop-json-viewer.loading::after {
        animation: none;
    }

    .sop-toggle-icon {
        transition: none;
    }
}

/* Print styles */
@media print {
    .sop-section-header {
        background: #f0f0f0 !important;
        color: #000 !important;
        break-inside: avoid;
    }

    .sop-section-content {
        max-height: none !important;
        padding: 15px !important;
        display: block !important;
        visibility: visible !important;
    }

    .sop-toggle-icon {
        display: none;
    }
}

/* Dark mode support (jika tema mendukung) */
@media (prefers-color-scheme: dark) {
    .sop-accordion {
        border-color: #444;
        background-color: #1a1a1a;
    }

    .sop-section-header {
        background-color: #2d2d2d;
        color: #fff;
    }

    .sop-section-header:hover {
        background-color: #3d3d3d;
    }

    .sop-section-content {
        background-color: #1a1a1a;
        color: #e0e0e0;
    }

    .sop-content a {
        color: #66b3ff;
    }
}
```

### 4.3 Add Error Handling dan Fallbacks
Update main plugin class dengan error handling:

```php
// Dalam class SOP_JSON_Viewer
public function render_sop_accordion($atts) {
    try {
        $atts = shortcode_atts(array(
            'id' => '',
            'class' => '',
            'fallback' => 'default'
        ), $atts);

        if (empty($atts['id'])) {
            return $this->render_error('SOP ID is required');
        }

        $sop_data = $this->get_sop_data($atts['id']);

        if (!$sop_data) {
            $fallback_message = $this->get_fallback_content($atts['fallback'], $atts['id']);
            return $fallback_message;
        }

        return $this->render_accordion_html($sop_data, $atts);

    } catch (Exception $e) {
        error_log('SOP JSON Viewer Error: ' . $e->getMessage());
        return $this->render_error('An error occurred while rendering the SOP accordion');
    }
}

private function render_error($message) {
    return '<div class="sop-error" style="padding: 15px; background: #ffe6e6; border: 1px solid #ff9999; border-radius: 4px; color: #cc0000; margin: 10px 0;">' .
           '<strong>SOP JSON Viewer Error:</strong> ' . esc_html($message) .
           '</div>';
}

private function get_fallback_content($fallback_type, $sop_id) {
    switch ($fallback_type) {
        case 'message':
            return '<div class="sop-fallback" style="padding: 20px; text-align: center; color: #666;">' .
                   '<p>SOP content for "' . esc_html($sop_id) . '" is not available.</p>' .
                   '<p>Please contact the administrator to add this content.</p>' .
                   '</div>';

        case 'default':
        default:
            return $this->render_accordion_html($this->get_default_sop_data(), array('id' => $sop_id));
    }
}

private function get_default_sop_data() {
    return array(
        'title' => 'SOP Not Found',
        'description' => 'The requested SOP content could not be loaded.',
        'sections' => array(
            array(
                'title' => 'Content Unavailable',
                'content' => 'The SOP content you are looking for is currently unavailable. Please try again later or contact your administrator.'
            )
        )
    );
}
```

### 4.4 Add Performance Optimizations
Tambahkan lazy loading dan performance enhancements:

```javascript
// Dalam SOPAccordion class
loadNestedData() {
    // Lazy load nested accordions
    const nestedSections = this.element.find('.sop-subsections .sop-section');

    if (nestedSections.length > 0) {
        // Use Intersection Observer untuk lazy initialization
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const $section = $(entry.target);
                        new SOPAccordion($section[0]);
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.1 });

            nestedSections.each((index, section) => {
                observer.observe(section);
            });
        } else {
            // Fallback untuk browser lama
            nestedSections.each((index, section) => {
                new SOPAccordion(section);
            });
        }
    }
}
```

### 4.5 Add Analytics dan Tracking
Tambahkan basic analytics support:

```php
// Dalam class SOP_JSON_Viewer
public function render_sop_accordion($atts) {
    // ... existing code ...

    $output = $this->render_accordion_html($sop_data, $atts);

    // Add analytics jika diperlukan
    if (apply_filters('sjp_enable_analytics', false)) {
        $output .= $this->render_analytics_code($atts['id']);
    }

    return $output;
}

private function render_analytics_code($sop_id) {
    return "<script>
        document.addEventListener('DOMContentLoaded', function() {
            $('.sop-json-viewer[data-sop-id=\"{$sop_id}\"]').on('sop:sectionOpened', function(e, $header, $content) {
                if (typeof gtag !== 'undefined') {
                    gtag('event', 'sop_section_opened', {
                        'sop_id': '{$sop_id}',
                        'section_title': $header.find('.sop-section-title').text()
                    });
                }
            });
        });
    </script>";
}
```

## File yang Dibuat/Dimodifikasi di Step Ini
- Modified: `assets/js/sop-accordion.js` (tambah full interactivity)
- Modified: `assets/css/sop-accordion.css` (tambah animations dan enhancements)
- Modified: `includes/class-sop-json-viewer.php` (tambah error handling dan fallbacks)

## Testing Checkpoint
1. Accordion dapat di-toggle dengan mouse dan keyboard
2. Smooth animations berjalan dengan baik
3. Keyboard navigation (Arrow keys, Enter, Space) berfungsi
4. Accessibility features (ARIA, screen reader) berjalan dengan baik
5. Error handling menampilkan pesan yang helpful
6. Fallback content muncul ketika data tidak tersedia
7. Performance optimizations tidak mengganggu functionality

## Next Step
Lanjut ke Step 05 untuk final integration, testing, dan deployment preparation.