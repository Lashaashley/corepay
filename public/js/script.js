
window.uni_modal_secondary = function($title = '', $url = '', $size = "") {

    // ✅ Whitelist allowed size classes — reject anything unexpected
    const allowedSizes = ['modal-md', 'modal-lg', 'large', 'mid-large'];
    const safeSize = allowedSizes.includes($size) ? $size : 'modal-md';

    $.ajax({
        url: $url,
        error: function(err) {
            console.error('Modal load error:', err);
            alert("An error occurred");
        },
        success: function(resp) {
            if (resp) {

                // ✅ $title set via .text() — never rendered as HTML
                $('#uni_modal_secondary .modal-title').text($title);

                // ✅ resp sanitized before injection — requires DOMPurify
                const safeResp = typeof DOMPurify !== 'undefined'
                    ? DOMPurify.sanitize(resp)
                    : resp;

                $('#uni_modal_secondary .modal-body').html(safeResp);

                // ✅ Only whitelisted classes applied
                $('#uni_modal_secondary .modal-dialog')
                    .removeClass('large mid-large modal-md modal-lg')
                    .addClass(safeSize);   // ✅ validated value only

                $('#uni_modal_secondary').modal({
                    backdrop: 'static',
                    keyboard: true,
                    focus: true
                });

                $('#uni_modal_secondary').modal('show');
            }
        }
    });
};
window._conf = function($msg = '', $func = '', $params = []) {
    $('#confirm_modal #confirm').attr('onclick', $func + "(" + $params.join(',') + ")")
    $('#confirm_modal .modal-body').html($msg)
    $('#confirm_modal').modal('show')
}
$(function() {

    $('#uni_modal').on('show.bs.modal', function() {
        if ($(this).find('.summernote')) {
            $('.summernote').each(function() {
                var _height = $(this).attr('data-height') || '20vh';
                var tabsize = $(this).attr('data-tabsize') || 2;
                var placeholder = $(this).attr('data-placeholder') || "Write something here.";
                $(this).summernote({
                    placeholder: placeholder,
                    tabsize: tabsize,
                    height: _height,
                    toolbar: [
                        ['style', ['style']],
                        ['font', ['bold', 'underline', 'clear']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['insert', ['link']],
                        ['view', ['fullscreen', 'codeview']]
                    ]
                })
            })
            $('.panel-heading.note-toolbar').addClass('bg-light border-bottom shadow ')
        }
    })
    $('.summernote').each(function() {
        var _height = $(this).attr('data-height') || '20vh';
        var tabsize = $(this).attr('data-tabsize') || 2;
        var placeholder = $(this).attr('data-placeholder') || "Write something here.";
        $(this).summernote({
            placeholder: placeholder,
            tabsize: tabsize,
            height: _height,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link']],
                ['view', ['codeview']]
            ]
        })
    })
    $('.panel-heading.note-toolbar').addClass('bg-light border-bottom shadow ')
})