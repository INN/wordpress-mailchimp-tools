(function() {
  var $ = jQuery,
      container = $('.mailchimp-tools');

  var validate_form_data = function() {
    var form_data = container.find(':input').serializeArray();
        names = [],
        required = [
          'mailchimp[type]',
          'mailchimp[list_id]',
          'mailchimp[title]',
          'mailchimp[subject]'
        ],
        valid = true;

    $.each(form_data, function(idx, val) {
      names.push(val.name);
    });

    $.each(required, function(req) {
      if (names.indexOf(required) < 0) {
        valid = false;
        return false;
      }
    });

    return valid;
  };

  container.find('.segment input').on('click', function() {
    var list = $(this).closest('.list');
    list.find('> input').attr('checked', 'checked');
  });

  container.find('.list > input').on('click', function() {
    var list = $(this).closest('.list');
    list.siblings().find('.segment input').removeAttr('checked');
  });

  container.find(':button, :submit').on('click', function(event) {
    if (!validate_form_data()) {
      alert('Please complete all fields before submitting your campaign.');
      return false;
    }

    $('form#post').off('submit.edit-post').on('submit.edit-post', function(event) {
        if (event.isDefaultPrevented())
            return;

        $(window).off('beforeunload.edit-post');
    });
  });

  container.find('input[name="mailchimp[type]"]').on('click', function() {
    if ($(this).val() == 'plaintext') {
      container.find('#mailchimp-tools-template').hide();
    } else {
      container.find('#mailchimp-tools-template').show();
    }
  });

  container.find('input[name="mailchimp[send]"]').on('click', function() {
    if (!validate_form_data()) {
      return false;
    }

    if (!confirm('Are you sure you want to send this campaign?')) {
      return false;
    }
  });
})();
