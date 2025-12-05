$(function () {
    // set CSRF for AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Add to cart
    $(document).on('click', '.add-to-cart', function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        var btn = $(this);
        btn.prop('disabled', true).text('Adding...');

        $.post('/cart/add', { product_id: id })
            .done(function (res) {
                if (res.status === 'success') {
                    $('#cart-count').text(res.count);
                    alert('Added to cart');
                } else {
                    alert('Could not add to cart');
                }
            })
            .fail(function () {
                alert('Request failed');
            })
            .always(function () {
                btn.prop('disabled', false).text('Add');
            });
    });
});

