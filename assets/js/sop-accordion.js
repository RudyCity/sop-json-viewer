/**
 * SOP JSON Viewer - Enhanced Frontend JavaScript (Vanilla JS)
 * Modern accordion with smooth animations and improved UX
 */

class SOPAccordion {
    constructor(element) {
        this.element = element;
        this.sopId = element.dataset.sopId;
        this.currentlyOpen = null;
        this.animationDuration = 300;
        this.isAnimating = false;
        this.resizeObserver = null;
        this.intersectionObserver = null;
        
        // Performance optimization
        this.rafId = null;
        this.debouncedResize = this.debounce(this.handleResize.bind(this), 100);
        
        this.init();
    }

    init() {
        this.bindEvents();
        this.setupAccessibility();
        this.setupObservers();
        this.loadNestedData();
        this.enhanceLinkAccessibility();
        this.loadDynamicContent();
        this.preloadAnimations();
        this.handleInitialVisibility();
    }

    bindEvents() {
        // Use event delegation for better performance
        this.element.addEventListener('click', this.handleClick.bind(this));
        this.element.addEventListener('keydown', this.handleKeydown.bind(this));
        
        // Handle window resize with debouncing
        window.addEventListener('resize', this.debouncedResize);
        
        // Handle visibility change for performance
        document.addEventListener('visibilitychange', this.handleVisibilityChange.bind(this));
    }

    handleClick(e) {
        const header = e.target.closest('.sop-section-header, .sop-subsections .sop-section-header');
        if (header) {
            e.preventDefault();
            e.stopPropagation();
            const section = header.closest('.sop-section');
            this.toggleSection(header, section);
        }
        
        // Handle links in content
        const link = e.target.closest('.sop-content a, .sop-link');
        if (link) {
            const href = link.getAttribute('href');
            
            // Track link clicks for analytics
            this.trackLinkClick(link);
            
            // Handle anchor links
            if (href && href.startsWith('#')) {
                e.preventDefault();
                this.scrollToSection(href.substring(1));
            }
            
            // Handle external links
            if (link.classList.contains('sop-link') && link.getAttribute('target') === '_blank') {
                // Let default behavior handle external links
                return;
            }
        }
    }

    handleKeydown(e) {
        const header = e.target.closest('.sop-section-header, .sop-subsections .sop-section-header');
        if (!header) return;
        
        const section = header.closest('.sop-section');
        const accordion = section.closest('.sop-accordion');
        const isNested = header.closest('.sop-subsections') !== null;
        
        let headers;
        if (isNested) {
            const subsection = header.closest('.sop-subsections');
            headers = Array.from(subsection.querySelectorAll('.sop-section-header'));
        } else {
            headers = Array.from(accordion.querySelectorAll(':scope > .sop-section > .sop-section-header'));
        }
        
        const currentIndex = headers.indexOf(header);
        let targetIndex = currentIndex;
        
        switch (e.key) {
            case 'Enter':
            case ' ':
                e.preventDefault();
                this.toggleSection(header, section);
                break;
                
            case 'ArrowUp':
                e.preventDefault();
                targetIndex = currentIndex > 0 ? currentIndex - 1 : headers.length - 1;
                headers[targetIndex].focus();
                break;
                
            case 'ArrowDown':
                e.preventDefault();
                targetIndex = currentIndex < headers.length - 1 ? currentIndex + 1 : 0;
                headers[targetIndex].focus();
                break;
                
            case 'Home':
                e.preventDefault();
                headers[0].focus();
                break;
                
            case 'End':
                e.preventDefault();
                headers[headers.length - 1].focus();
                break;
                
            case 'Escape':
                e.preventDefault();
                if (isNested) {
                    const parentSection = section.closest('.sop-section');
                    const parentHeader = parentSection.querySelector('.sop-section-header');
                    if (parentHeader) {
                        parentHeader.focus();
                    }
                }
                break;
        }
    }

    toggleSection(header, section) {
        if (this.isAnimating) return;
        
        const content = section.querySelector('.sop-section-content');
        const isExpanded = header.getAttribute('aria-expanded') === 'true';
        
        if (isExpanded) {
            this.closeSection(header, content);
        } else {
            this.openSection(header, content);
        }
    }

    openSection(header, content) {
        if (this.isAnimating) return;
        this.isAnimating = true;
        
        // Close currently open section if it's not a parent of this one
        if (this.currentlyOpen && !this.isParentOf(this.currentlyOpen.content, content)) {
            this.closeSection(this.currentlyOpen.header, this.currentlyOpen.content, false);
        }
        
        // Set states before animation
        header.setAttribute('aria-expanded', 'true');
        content.removeAttribute('hidden');
        content.setAttribute('aria-expanded', 'true');
        
        // Force reflow to ensure transition works
        content.offsetHeight;
        
        // Get accurate height
        const height = content.scrollHeight;
        
        // Start animation
        this.animateContent(content, {
            maxHeight: height + 'px',
            opacity: 1
        }, () => {
            // Set final state
            content.style.maxHeight = 'none';
            content.style.overflow = 'visible';
            this.isAnimating = false;
            
            // Update currently open
            this.currentlyOpen = { header, content };
            
            // Manage nested focus
            this.manageNestedFocus(content, true);
            
            // Trigger custom event
            this.dispatchCustomEvent('sop:sectionOpened', { header, content });
        });
    }

    closeSection(header, content, updateCurrent = true) {
        if (this.isAnimating) return;
        this.isAnimating = true;
        
        // Set initial state for animation
        content.style.maxHeight = content.scrollHeight + 'px';
        content.style.overflow = 'hidden';
        
        // Force reflow
        content.offsetHeight;
        
        // Manage nested focus before closing
        this.manageNestedFocus(content, false);
        
        // Start animation
        this.animateContent(content, {
            maxHeight: '0px',
            opacity: 0
        }, () => {
            // Set final state
            content.setAttribute('hidden', 'hidden');
            content.setAttribute('aria-expanded', 'false');
            content.style.overflow = '';
            this.isAnimating = false;
            
            // Update currently open
            if (updateCurrent && this.currentlyOpen &&
                this.currentlyOpen.header === header &&
                this.currentlyOpen.content === content) {
                this.currentlyOpen = null;
            }
            
            // Trigger custom event
            this.dispatchCustomEvent('sop:sectionClosed', { header, content });
        });
        
        // Update header state
        header.setAttribute('aria-expanded', 'false');
    }

    animateContent(element, properties, callback) {
        // Cancel any existing animation
        if (this.rafId) {
            cancelAnimationFrame(this.rafId);
        }
        
        const duration = this.animationDuration;
        const start = performance.now();
        const initialProperties = {};
        
        // Store initial values
        for (const property of Object.keys(properties)) {
            initialProperties[property] = element.style[property] || '';
        }
        
        const animate = (currentTime) => {
            const elapsed = currentTime - start;
            const progress = Math.min(elapsed / duration, 1);
            
            // Apply easing function
            const easeProgress = this.easeInOutCubic(progress);
            
            // Apply properties with easing
            for (const [property, value] of Object.entries(properties)) {
                if (property === 'maxHeight') {
                    const initial = parseFloat(initialProperties[property]) || 0;
                    const target = parseFloat(value);
                    const current = initial + (target - initial) * easeProgress;
                    element.style[property] = current + 'px';
                } else if (property === 'opacity') {
                    element.style[property] = easeProgress;
                } else {
                    element.style[property] = value;
                }
            }
            
            if (progress < 1) {
                this.rafId = requestAnimationFrame(animate);
            } else if (callback) {
                callback();
            }
        };
        
        this.rafId = requestAnimationFrame(animate);
    }

    easeInOutCubic(t) {
        return t < 0.5 ? 4 * t * t * t : (t - 1) * (2 * t - 2) * (2 * t - 2) + 1;
    }

    setupAccessibility() {
        // Setup main headers
        const mainHeaders = this.element.querySelectorAll(':scope > .sop-accordion > .sop-section > .sop-section-header');
        mainHeaders.forEach((header, index) => {
            const sectionId = header.id || `sop-header-${this.sopId}-${index}`;
            const contentId = header.getAttribute('aria-controls') || `sop-content-${this.sopId}-${index}`;
            
            header.setAttribute('id', sectionId);
            header.setAttribute('role', 'tab');
            header.setAttribute('tabindex', index === 0 ? '0' : '-1');
            header.setAttribute('aria-expanded', 'false');
            
            const content = header.nextElementSibling;
            if (content && content.matches('.sop-section-content')) {
                content.setAttribute('id', contentId);
                content.setAttribute('role', 'tabpanel');
                content.setAttribute('aria-labelledby', sectionId);
                content.setAttribute('hidden', 'hidden');
            }
        });
        
        // Setup nested headers
        const nestedHeaders = this.element.querySelectorAll('.sop-subsections .sop-section-header');
        nestedHeaders.forEach((header, index) => {
            const section = header.closest('.sop-section');
            const content = section.querySelector('.sop-section-content');
            const baseId = `nested-${this.sopId}-${index}`;
            
            header.setAttribute('id', `sop-header-${baseId}`);
            header.setAttribute('aria-controls', `sop-content-${baseId}`);
            header.setAttribute('aria-expanded', 'false');
            header.setAttribute('role', 'button');
            header.setAttribute('tabindex', '-1');
            
            if (content) {
                content.setAttribute('id', `sop-content-${baseId}`);
                content.setAttribute('aria-labelledby', `sop-header-${baseId}`);
                content.setAttribute('hidden', 'hidden');
            }
        });
    }

    setupObservers() {
        // Setup ResizeObserver for better height calculations
        if ('ResizeObserver' in window) {
            this.resizeObserver = new ResizeObserver((entries) => {
                for (const entry of entries) {
                    if (entry.target.classList.contains('sop-section-content') && 
                        entry.target.getAttribute('aria-expanded') === 'true') {
                        // Update max-height when content changes
                        entry.target.style.maxHeight = 'none';
                    }
                }
            });
            
            // Observe all content elements
            this.element.querySelectorAll('.sop-section-content').forEach(content => {
                this.resizeObserver.observe(content);
            });
        }
        
        // Setup IntersectionObserver for lazy loading
        if ('IntersectionObserver' in window) {
            this.intersectionObserver = new IntersectionObserver((entries) => {
                for (const entry of entries) {
                    if (entry.isIntersecting) {
                        // Preload animations when visible
                        this.preloadAnimations();
                        this.intersectionObserver.unobserve(entry.target);
                    }
                }
            });
            
            this.intersectionObserver.observe(this.element);
        }
    }

    loadNestedData() {
        // Handle nested sections within the same accordion instance
        const nestedHeaders = this.element.querySelectorAll('.sop-subsections .sop-section-header');

        if (nestedHeaders.length > 0) {
            nestedHeaders.forEach((header, index) => {
                const section = header.closest('.sop-section');
                const content = section.querySelector('.sop-section-content');
                const baseId = `nested-${this.sopId}-${index}`;

                header.setAttribute('id', `sop-header-${baseId}`);
                header.setAttribute('aria-controls', `sop-content-${baseId}`);
                // Only set aria-expanded to 'false' if not already set (preserve per-section settings)
                if (!header.hasAttribute('aria-expanded')) {
                    header.setAttribute('aria-expanded', 'false');
                }
                header.setAttribute('role', 'button');
                header.setAttribute('tabindex', '-1');

                if (content) {
                    content.setAttribute('id', `sop-content-${baseId}`);
                    content.setAttribute('aria-labelledby', `sop-header-${baseId}`);
                    // Only set hidden if not already set (preserve per-section settings)
                    if (!content.hasAttribute('aria-expanded')) {
                        content.setAttribute('hidden', 'hidden');
                    }
                }
            });
        }
    }

    manageNestedFocus(content, isOpening) {
        const nestedHeaders = content.querySelectorAll('.sop-subsections .sop-section-header');
        
        nestedHeaders.forEach((header) => {
            if (isOpening) {
                header.setAttribute('tabindex', '0');
            } else {
                header.setAttribute('tabindex', '-1');
                if (document.activeElement === header) {
                    header.blur();
                }
            }
        });
    }

    scrollToSection(sectionId) {
        const target = document.getElementById(sectionId);
        if (target) {
            const targetTop = target.offsetTop - 20;
            window.scrollTo({
                top: targetTop,
                behavior: 'smooth'
            });
        }
    }

    handleResize() {
        // Recalculate heights for open sections
        if (this.currentlyOpen) {
            const content = this.currentlyOpen.content;
            if (content && !content.hasAttribute('hidden')) {
                const height = content.scrollHeight;
                content.style.maxHeight = height + 'px';
            }
        }
    }

    handleVisibilityChange() {
        if (document.hidden) {
            // Pause animations when tab is not visible
            this.isAnimating = false;
            if (this.rafId) {
                cancelAnimationFrame(this.rafId);
                this.rafId = null;
            }
        }
    }

    preloadAnimations() {
        // Preload CSS animations for smoother experience
        const testElement = document.createElement('div');
        testElement.style.opacity = '0';
        testElement.style.transition = 'opacity 0.3s ease';
        document.body.appendChild(testElement);
        
        // Trigger reflow
        testElement.offsetHeight;
        
        // Clean up
        document.body.removeChild(testElement);
    }

    isParentOf(parent, child) {
        let current = child.parentElement;
        while (current) {
            if (current === parent) return true;
            current = current.parentElement;
        }
        return false;
    }

    dispatchCustomEvent(eventName, detail) {
        const event = new CustomEvent(eventName, {
            detail,
            bubbles: true,
            cancelable: true
        });
        this.element.dispatchEvent(event);
    }

    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    trackLinkClick(link) {
        const linkTitle = link.querySelector('.sop-link-title')?.textContent || link.textContent;
        const linkUrl = link.getAttribute('href');
        const isExternal = link.getAttribute('target') === '_blank';
        
        // Trigger custom event for link tracking
        this.dispatchCustomEvent('sop:linkClicked', {
            link,
            title: linkTitle,
            url: linkUrl,
            isExternal
        });
        
        // Log to console for debugging
        console.log('SOP Link clicked:', {
            title: linkTitle,
            url: linkUrl,
            isExternal,
            sopId: this.sopId
        });
    }

    enhanceLinkAccessibility() {
        // Add ARIA labels for better accessibility
        const links = this.element.querySelectorAll('.sop-link');
        links.forEach(link => {
            const title = link.querySelector('.sop-link-title')?.textContent || '';
            const isExternal = link.getAttribute('target') === '_blank';
            
            if (isExternal && !link.getAttribute('aria-label')) {
                link.setAttribute('aria-label', `${title} (opens in new tab)`);
            }
            
            // Add keyboard navigation for links
            link.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    link.click();
                }
            });
        });
    }

    // Enhanced method to handle dynamic content loading
    loadDynamicContent() {
        // This method can be used to load link content dynamically
        const linkContainers = this.element.querySelectorAll('.sop-content');
        linkContainers.forEach(container => {
            const links = container.querySelectorAll('.sop-link');
            if (links.length > 0) {
                // Add loading state for external links
                links.forEach(link => {
                    if (link.getAttribute('target') === '_blank') {
                        link.addEventListener('click', () => {
                            link.classList.add('sop-link-loading');
                            setTimeout(() => {
                                link.classList.remove('sop-link-loading');
                            }, 1000);
                        });
                    }
                });
            }
        });
    }

    // Public methods for external control
    openSectionByIndex(index) {
        const headers = this.element.querySelectorAll(':scope > .sop-accordion > .sop-section > .sop-section-header');
        if (headers[index]) {
            const header = headers[index];
            const section = header.closest('.sop-section');
            this.toggleSection(header, section);
        }
    }

    closeAllSections() {
        if (this.currentlyOpen) {
            this.closeSection(this.currentlyOpen.header, this.currentlyOpen.content);
        }
    }

    getOpenSectionIndex() {
        if (this.currentlyOpen) {
            const headers = this.element.querySelectorAll(':scope > .sop-accordion > .sop-section > .sop-section-header');
            return Array.from(headers).indexOf(this.currentlyOpen.header);
        }
        return -1;
    }

    handleInitialVisibility() {
        const defaultVisibility = this.element.dataset.defaultVisibility || 'hidden';

        // Handle sections that are already marked as expanded in HTML (from PHP rendering)
        const expandedHeaders = this.element.querySelectorAll('.sop-section-header[aria-expanded="true"]');

        expandedHeaders.forEach(header => {
            const section = header.closest('.sop-section');
            const content = section.querySelector('.sop-section-content');

            if (content) {
                // Set the currently open section (only for main sections, not nested)
                if (!header.closest('.sop-subsections')) {
                    this.currentlyOpen = { header, content };
                }

                // Apply expanded styles immediately
                content.style.maxHeight = 'none';
                content.style.overflow = 'visible';
                content.style.opacity = '1';
                content.style.padding = '0 24px 20px';
                content.removeAttribute('hidden');
                content.setAttribute('aria-expanded', 'true');

                // Update header state
                header.setAttribute('aria-expanded', 'true');
                const toggleIcon = header.querySelector('.sop-toggle-icon');
                if (toggleIcon) {
                    toggleIcon.textContent = '−';
                }

                // Manage nested focus
                this.manageNestedFocus(content, true);
            }
        });

        // Fallback for default visibility if no sections are pre-expanded
        if (defaultVisibility === 'shown' && expandedHeaders.length === 0) {
            // Find the first section that should be expanded
            const firstHeader = this.element.querySelector('.sop-accordion > .sop-section > .sop-section-header');
            const firstContent = this.element.querySelector('.sop-accordion > .sop-section > .sop-section-content');

            if (firstHeader && firstContent) {
                // Set the currently open section
                this.currentlyOpen = { header: firstHeader, content: firstContent };

                // Apply expanded styles immediately
                firstContent.style.maxHeight = 'none';
                firstContent.style.overflow = 'visible';
                firstContent.style.opacity = '1';
                firstContent.style.padding = '0 24px 20px';
                firstContent.removeAttribute('hidden');
                firstContent.setAttribute('aria-expanded', 'true');

                // Update header state
                firstHeader.setAttribute('aria-expanded', 'true');
                const toggleIcon = firstHeader.querySelector('.sop-toggle-icon');
                if (toggleIcon) {
                    toggleIcon.textContent = '−';
                }

                // Manage nested focus
                this.manageNestedFocus(firstContent, true);
            }
        }
    }

    destroy() {
        // Clean up event listeners
        this.element.removeEventListener('click', this.handleClick);
        this.element.removeEventListener('keydown', this.handleKeydown);
        window.removeEventListener('resize', this.debouncedResize);
        document.removeEventListener('visibilitychange', this.handleVisibilityChange);

        // Clean up observers
        if (this.resizeObserver) {
            this.resizeObserver.disconnect();
        }

        if (this.intersectionObserver) {
            this.intersectionObserver.disconnect();
        }

        // Cancel any ongoing animations
        if (this.rafId) {
            cancelAnimationFrame(this.rafId);
        }
    }
}

// Auto-initialize accordions when document is ready
const initializeAccordions = () => {
    document.querySelectorAll('.sop-json-viewer').forEach((element) => {
        // Check if already initialized
        if (!element.hasAttribute('data-sop-initialized')) {
            new SOPAccordion(element);
            element.setAttribute('data-sop-initialized', 'true');
        }
    });
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeAccordions);
} else {
    initializeAccordions();
}

// Re-initialize when new content is loaded (for dynamic content)
const observer = new MutationObserver((mutations) => {
    for (const mutation of mutations) {
        for (const node of mutation.addedNodes) {
            if (node.nodeType === Node.ELEMENT_NODE) {
                if (node.classList && node.classList.contains('sop-json-viewer')) {
                    if (!node.hasAttribute('data-sop-initialized')) {
                        new SOPAccordion(node);
                        node.setAttribute('data-sop-initialized', 'true');
                    }
                } else if (node.querySelectorAll) {
                    node.querySelectorAll('.sop-json-viewer').forEach((element) => {
                        if (!element.hasAttribute('data-sop-initialized')) {
                            new SOPAccordion(element);
                            element.setAttribute('data-sop-initialized', 'true');
                        }
                    });
                }
            }
        }
    }
});

observer.observe(document.body, {
    childList: true,
    subtree: true
});

// Export for external use
window.SOPAccordion = SOPAccordion;