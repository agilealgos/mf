(function($) {

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

    $('.md-size-chart-btn').click(function (t) {
        t.preventDefault();
        var c = $(this).attr('chart-data-id');
        $('.scfw-size-chart-modal[chart-data-id="' + c + '"]').show();
        $('.scfw-size-chart-modal[chart-data-id="' + c + '"]').removeClass('md-size-chart-hide');
        $('.scfw-size-chart-modal[chart-data-id="' + c + '"]').addClass('md-size-chart-show');
    });

    $('div#md-size-chart-modal .remodal-close').click(function (t) {
        t.preventDefault();
        $(this).parents('.scfw-size-chart-modal').removeClass('md-size-chart-show');
        $(this).parents('.scfw-size-chart-modal').addClass('md-size-chart-hide');
    });

    $('div.md-size-chart-overlay').click(function (t) {
        t.preventDefault();
        $(this).parents('.scfw-size-chart-modal').removeClass('md-size-chart-show');
        $(this).parents('.scfw-size-chart-modal').addClass('md-size-chart-hide');
    });

    $('.md-size-chart-modal').each(function () {
        var c = $(this).attr('chart-data-id');
        $('.md-size-chart-modal[chart-data-id="' + c + '"]').slice(1).remove();
    });

})(jQuery);
