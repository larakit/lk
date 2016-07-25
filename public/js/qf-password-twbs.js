LarakitJs.initSelector('.js-laraform-password', function () {
    var $this = $(this);
    $this.on('click', function () {
        var el = $this.closest('.input-group').find('input');
        if (el.attr('type') == 'password') {
            $this.removeClass('fa-eye').addClass('fa-eye-slash');
            el.attr('type', 'text');
        } else {
            $this.removeClass('fa-eye-slash').addClass('fa-eye');
            el.attr('type', 'password');
        }
    });
});