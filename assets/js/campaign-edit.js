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

    $.each(required, function(idx, req) {
      if (names.indexOf(req) < 0) {
        valid = false;
        return false;
      }
    });

    return valid;
  };

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

  container.find('input[name="mailchimp[send]"]').on('click', function() {
    if (!validate_form_data()) {
      return false;
    }

    if (!confirm('Are you sure you want to send this campaign?')) {
      return false;
    }
  });
})();
