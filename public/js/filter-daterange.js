LarakitJs.initSelector('.js-filter-daterange', function(){
    var self = $(this),
        block = self.closest('.filter-daterange-block'),
        from = $('#'+block.attr('data-from')),
        to = $('#'+block.attr('data-to'))
        ;
    self.on('click', function(){
        from.val(self.attr('data-from'));
        to.val(self.attr('data-to'));
   }) ;
});