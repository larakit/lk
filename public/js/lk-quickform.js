LarakitJs.initSelector('.js-laraform-clean', function () {
    var $this = $(this);
    $this.on('click', function () {
        $this.closest('.input-group').find('input,textarea').val('');
    });
});


LarakitJs.initSelector('.js-laraform-example', function () {
    var $this = $(this);

    $this.on('click', function () {
        var $block = $this.parent(),
            id = $block.attr("rel"),
            append = parseInt($block.attr("data-append")),
            val = $this.attr("data-value"),
            $form = $this.closest('form'),
            element = $form.find("#" + id);

        console.log(element);
        if (append > 0) {
            var current = element.val();
            element.val(current ? current + ", " + val : val);
        } else {
            element.val(val);
        }
        element.trigger("change");
        element.trigger("blur");
    });
});

qf.Validator.prototype.classes.error = 'has-error';
qf.Validator.prototype.classes.valid = 'has-success';
qf.Validator.prototype.classes.message = 'error-block';
qf.Validator.prototype.classes.ancestor = 'form-group';
qf.Validator.prototype.onFieldError = function (elementId, errorMessage) {
    $('#' + elementId).closest('.form-group,.element-group').addClass(this.classes.error);
    $('.error-block[rel=' + elementId + ']').html(errorMessage);
};

qf.Validator.prototype.onFieldValid = function (elementId) {
    $('#' + elementId).closest('.form-group,.element-group').addClass(this.classes.valid);
    $('.error-block[rel=' + elementId + ']').html('');
};

qf.Validator.prototype.removeErrorMessage = function (elementId) {
    var parent = $('#' + elementId).closest('.form-group,.element-group');

    this.errors.remove(elementId);
    if (parent.length) {
        parent.removeClass(this.classes.error + " " + this.classes.valid);
        parent.find('span.' + this.classes.message).remove();
    }
};

qf.Validator.prototype.removeRelatedErrors = function (rule) {
    this.removeErrorMessage(rule.owner);
    for (var i = 0, item; item = rule.chained[i]; i++) {
        for (var j = 0, multiplier; multiplier = item[j]; j++) {
            this.removeRelatedErrors(multiplier);
        }
    }
};