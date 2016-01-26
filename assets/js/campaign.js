(function() {
  var $ = jQuery,
      container = $('.mailchimp-tools');

  container.find('.segment input').on('click', function() {
    var list = $(this).closest('.list');
    list.find('> input').attr('checked', 'checked');
  });

  container.find('.list > input').on('click', function() {
    var list = $(this).closest('.list');
    list.siblings().find('.segment input').removeAttr('checked');
  });

  container.find(':button, :submit').on('click', function(event) {
    $('form#post').off('submit.edit-post').on('submit.edit-post', function(event) {
        if (event.isDefaultPrevented())
            return;

        $(window).off('beforeunload.edit-post');
    });
  });

  container.find('input[name="mailchimp[type]"]').on('click', function() {
    console.log('here');
    if ($(this).val() == 'plaintext') {
      container.find('#mailchimp-tools-template').hide();
    } else {
      container.find('#mailchimp-tools-template').show();
    }
  })
})();
