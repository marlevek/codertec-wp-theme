(function () {
    function getTrackingLink(target) {
        if (!target || typeof target.closest !== 'function') {
            return null;
        }

        return target.closest('[data-ct-track="1"]');
    }

    function getValue(element, attributeName, fallbackValue) {
        if (!element) {
            return fallbackValue || '';
        }

        return element.getAttribute(attributeName) || fallbackValue || '';
    }

    function getTrimmedText(element) {
        if (!element || !element.textContent) {
            return '';
        }

        return element.textContent.replace(/\s+/g, ' ').trim();
    }

    document.addEventListener('click', function (event) {
        var link = getTrackingLink(event.target);

        if (!link) {
            return;
        }

        var context = window.codertecBlogTracking || {};
        var payload = {
            event: getValue(link, 'data-ct-event', 'codertec_blog_click'),
            event_category: 'blog',
            event_action: 'click',
            link_type: getValue(link, 'data-ct-type', 'link'),
            click_area: getValue(link, 'data-ct-area'),
            click_label: getValue(link, 'data-ct-label', getTrimmedText(link)),
            click_destination: getValue(link, 'data-ct-destination'),
            click_url: link.href || '',
            page_surface: context.pageSurface || '',
            content_type: context.contentType || '',
            content_id: context.contentId || 0,
            content_name: context.contentName || '',
            content_slug: context.contentSlug || '',
            category_name: context.categoryName || ''
        };

        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push(payload);

        window.dispatchEvent(new CustomEvent('codertec:blog-click', {
            detail: payload
        }));
    });
}());
