$(function () {
    // $('#contact-form').validator();

    $('#contact-form').on('submit', function (e) {
    if (!e.isDefaultPrevented()) {
        var url = "../../mail_handler.php";
        let name = e.target[0].value;
        let email = e.target[1].value;
        let message = e.target[2].value;

        if(name == '' || email == '' || message == '') {
            modal('You must complete the form');

            return false;
        }

        $.ajax({
            type: "POST",
            url: url,
            data: {
                name: name,
                email: email,
                message: message
            },
            success: function (data) {
                var messageAlert = 'alert-' + data;
                var messageText = data;

                if (messageAlert && messageText) {
                    modal(messageText);
                }
            }
        });
        return false;
    }

    function modal(messageText) {
        var modal = document.getElementById('myModal');
        var span = document.getElementsByClassName("close")[0];

        // When the user clicks on <span> (x), close the modal
        span.onclick = function() {
            modal.style.display = "none";
        };

        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        };

        $('.message').empty();
        $('.message').append('<div>' + messageText + '</div>');

        modal.style.display = "block";

        $('#contact-form')[0].reset();
    }
})
});