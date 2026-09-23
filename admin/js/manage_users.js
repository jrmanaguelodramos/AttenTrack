$(document).ready(function () {

  $(document).on('click', '.user_add', function () {

    var first_name = $('#first_name').val();
    var middle_name = $('#middle_name').val();
    var last_name = $('#last_name').val();
    var student_number = $('#student_number').val();
    var course = $('#course').val();
    var rfidinput = $('#rfidinput').val();
    var sid = $('#sid').val();

    // ✅ Simple validation
    if (!first_name || !last_name || !student_number) {
      $('.alert_user').fadeIn(500).html(
        '<p class="alert alert-danger">Please fill all required fields</p>'
      );
      return;
    }

    $.ajax({
      url: 'studentReg_conf.php',
      type: 'POST',
      data: {
        Add: 1,
        first_name: first_name,
        middle_name: middle_name,
        last_name: last_name,
        student_number: student_number,
        course: course,
        rfidinput: rfidinput,
        sid: sid
      },

      success: function (response) {

        if (response.trim() == "1") {

          // ✅ Clear correct fields
          $('#first_name').val('');
          $('#middle_name').val('');
          $('#last_name').val('');
          $('#student_number').val('');
          $('#course').val('');
          $('#rfidinput').val('');
          $('#sid').val('');

          $('.alert_user').fadeIn(500).html(
            '<p class="alert alert-success">User successfully added</p>'
          );

        } else {
          $('.alert_user').fadeIn(500).html(
            '<p class="alert alert-danger">' + response + '</p>'
          );
        }

        setTimeout(function () {
          $('.alert').fadeOut(500);
        }, 5000);
      },

      error: function (xhr, status, error) {
        $('.alert_user').fadeIn(500).html(
          '<p class="alert alert-danger">AJAX Error: ' + error + '</p>'
        );
      }
    });

  });

});
// Update user
$(document).on('click', '.user_upd', function () {

  // Correct fields (match your Add form)
  var first_name = $('#first_name').val();
  var middle_name = $('#middle_name').val();
  var last_name = $('#last_name').val();
  var student_number = $('#student_number').val();
  var course = $('#course').val();
  var rfidinput = $('#rfidinput').val();
  var sid = $('#sid').val();

  // Validation
  if (!first_name || !last_name || !student_number) {
    $('.alert_user').fadeIn(500).html(
      '<p class="alert alert-danger">Please fill all required fields</p>'
    );
    return;
  }

  $.ajax({
    url: 'studentReg_conf.php', // same as add (if you're using same backend)
    type: 'POST',
    data: {
      Update: 1,
      first_name: first_name,
      middle_name: middle_name,
      last_name: last_name,
      student_number: student_number,
      course: course,
      rfidinput: rfidinput,
      sid: sid
    },

    success: function (response) {

      if (response.trim() == "1") {

        // Clear correct fields
        $('#first_name').val('');
        $('#middle_name').val('');
        $('#last_name').val('');
        $('#student_number').val('');
        $('#course').val('');
        $('#rfidinput').val('');
        $('#sid').val('');

        $('.alert_user').fadeIn(500).html(
          '<p class="alert alert-success">User successfully updated!</p>'
        );

      } else {
        $('.alert_user').fadeIn(500).html(
          '<p class="alert alert-danger">' + response + '</p>'
        );
      }

      setTimeout(function () {
        $('.alert').fadeOut(500);
      }, 5000);

      // Refresh user list
      // $.ajax({
      //   url: "manage_users_up.php",
      //   success: function (data) {
      //     $('#manage_users').html(data);
      //   }
      // });
    },

    error: function (xhr, status, error) {
      $('.alert_user').fadeIn(500).html(
        '<p class="alert alert-danger">AJAX Error: ' + error + '</p>'
      );
    }
  });
});
// Delete user
$(document).on('click', '.user_rmo', function () {

  var sid = $('#sid').val(); // use correct identifier

  if (!sid) {
    $('.alert_user').fadeIn(500).html(
      '<p class="alert alert-danger">No user selected</p>'
    );
    return;
  }

  bootbox.confirm("Do you really want to delete this User?", function (result) {
    if (result) {

      $.ajax({
        url: 'studentReg_conf.php',
        type: 'POST',
        data: {
          delete: 1,
          sid: sid
        },

        success: function (response) {

          if (response.trim() == "1") {

            // Clear fields
            $('#first_name').val('');
            $('#middle_name').val('');
            $('#last_name').val('');
            $('#student_number').val('');
            $('#course').val('');
            $('#rfidinput').val('');
            $('#sid').val('');

            $('.alert_user').fadeIn(500).html(
              '<p class="alert alert-success">User successfully deleted!</p>'
            );

          } else {
            $('.alert_user').fadeIn(500).html(
              '<p class="alert alert-danger">' + response + '</p>'
            );
          }

          setTimeout(function () {
            $('.alert').fadeOut(500);
          }, 5000);

          // Refresh list
          // $.ajax({
          //   url: "manage_users_up.php",
          //   success: function (data) {
          //     $('#manage_users').html(data);
          //   }
          // });
        },

        error: function (xhr, status, error) {
          $('.alert_user').fadeIn(500).html(
            '<p class="alert alert-danger">AJAX Error: ' + error + '</p>'
          );
        }
      });

    }
  });
});
// select user
$(document).on('click', '.select_btn', function () {
  var el = this;
  var card_uid = $(this).attr("id");
  $.ajax({
    url: 'manage_users_conf.php',
    type: 'GET',
    data: {
      'select': 1,
      'card_uid': card_uid,
    },
    success: function (response) {

      $(el).closest('tr').css('background', '#70c276');

      $('.alert_user').fadeIn(500);
      $('.alert_user').html('<p class="alert alert-success">The card has been selected!</p>');

      setTimeout(function () {
        $('.alert').fadeOut(500);
      }, 5000);

      $.ajax({
        url: "manage_users_up.php"
      }).done(function (data) {
        $('#manage_users').html(data);
      });

      console.log(response);

      var user_id = {
        User_id: []
      };
      var user_name = {
        User_name: []
      };
      var user_on = {
        User_on: []
      };
      var user_email = {
        User_email: []
      };
      var user_dev = {
        User_dev: []
      };
      var user_gender = {
        User_gender: []
      };

      var len = response.length;

      for (var i = 0; i < len; i++) {
        user_id.User_id.push(response[i].id);
        user_name.User_name.push(response[i].username);
        user_on.User_on.push(response[i].serialnumber);
        user_email.User_email.push(response[i].email);
        user_dev.User_dev.push(response[i].device_uid);
        user_gender.User_gender.push(response[i].gender);
      }
      if (user_dev.User_dev == "All") {
        user_dev.User_dev = 0;
      }
      $('#user_id').val(user_id.User_id);
      $('#name').val(user_name.User_name);
      $('#number').val(user_on.User_on);
      $('#email').val(user_email.User_email);
      $('#dev_sel').val(user_dev.User_dev);

      if (user_gender.User_gender == 'Female') {
        $('.form-style-5').find(':radio[name=gender][value="Female"]').prop('checked', true);
      }
      else {
        $('.form-style-5').find(':radio[name=gender][value="Male"]').prop('checked', true);
      }

    },
    error: function (data) {
      console.log(data);
    }
  });
});