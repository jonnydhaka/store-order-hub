;(function($) {

    $(document).on('click', 'a.submitforapi', function(e) {
        e.preventDefault();

        if (!confirm(WppoolStoreOrder.confirm)) {
            return;
        }
        var self = $(this),
        id = self.data('id');
        wp.ajax.post('wppool-delete-post-by-id', {
            id: id,
            _wpnonce: WppoolStoreOrder.nonce
        })
        .done(function(response) {
            location.reload();
           console.log(response)

        })
        .fail(function() {
            alert(WppoolStoreOrder.error);
        });
    });

})(jQuery);
