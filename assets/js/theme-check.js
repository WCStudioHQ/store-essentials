
jQuery(document).ready(function ($) {
    if (typeof wcsesSortToggle !== 'undefined') {
        var current_theme = wcsesSortToggle.current_theme;
        var position = wcsesSortToggle.position;
        if ((current_theme === 'twentytwentyfour') || (current_theme === 'twentytwentyfive')  || (current_theme === 'twentytwentythree') ) {
            if (position === 'before' || position === 'both') {
                $('.woocommerce-ordering').remove();
            }
        }
    }
});