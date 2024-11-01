(function () {
    var el = wp.element.createElement;
    var registerBlockType = wp.blocks.registerBlockType;
    var __ = wp.i18n.__;

    registerBlockType('xml-sitemap-for-google/html-sitemap-block', {
        title: __('HTML Sitemap Block', 'xml-sitemap-for-google'),
        icon: 'editor-table',
        category: 'common',
        edit: function () {
            return el('p', {}, 'HTML Sitemap Block');
        },
        save: function () {
            return el('div', { dangerouslySetInnerHTML: { __html: '[show_html_sitemap]' } });
        }
    });
})();
