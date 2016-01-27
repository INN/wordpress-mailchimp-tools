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

  container.find('input[name*="[type]"]').on('click', function() {
    if ($(this).val() == 'plaintext') {
      container.find('#mailchimp-tools-template').hide();
    } else {
      container.find('#mailchimp-tools-template').show();
    }
  });
})();
