    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <title>Manage Room/Scanner</title>
        <link rel="stylesheet" type="text/css" href="css/devices.css" />
        <script type="text/javascript" src="js/jquery-2.2.3.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.3.1.js"
            integrity="sha1256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60="
            crossorigin="anonymous">
        </script>
        <script type="text/javascript" src="js/bootbox.min.js"></script>
        <script type="text/javascript" src="js/bootstrap.js"></script>
        <script src="js/dev_config.js"></script>
        <script>
            $(window).on("load resize ", function() {
                var scrollWidth = $('.tbl-content').width() - $('.tbl-content table').width();
                $('.tbl-header').css({
                    'padding-right': scrollWidth
                });
            }).resize();
        </script>
        <script>
            $(document).ready(function() {
                $.ajax({
                    url: "dev_up.php",
                    type: 'POST',
                    data: {
                        'dev_up': 1,
                    }
                }).done(function(data) {
                    $('#devices').html(data);
                });
            });
        </script>
    </head>

    <body>

        <div class="container">

            <div class="alert_dev"></div>

            <div class="header">
                <div class="title-box">
                    Manage ROOM/SCANNER
                </div>
                <a href="dashboard.php" class="back-btn">←</a>
            </div>

        <div class="sub-bar">
            <form method="GET" action="list_student.php" class="search-box">
                <input type="text" name="search" id="searchInput" placeholder="Search devices...">
                <button type="button" class="clear-btn" onclick="clearSearch()">
                    <i class="fa-solid fa-xmark"></i>
                </button>
                <button type="submit" class="search-btn">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </form>
            <button type="button" class="btn-new-device" data-toggle="modal" data-target="#new-device">
                <i class="fa-solid fa-plus"></i> New Device
            </button>
        </div>

          

            <div id="devices"></div>

            <div class="bottom">
                <button class="emergency-btn"> 
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    EMERGENCY EXIT 
                </button>
            </div>

        </div>

        <div class="modal fade" id="new-device" tabindex="-1" role="dialog" aria-labelledby="New Device" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title" id="exampleModalLongTitle">ADD NEW DEVICE</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="modal-body">
                            <label for="User-mail"><b>Room:</b></label>
                            <input type="text" name="dev_name" id="dev_name" placeholder="Room..." required /><br>
                            <label for="User-mail"><b>Building:</b></label>
                            <input type="text" name="dev_dep" id="dev_dep" placeholder="Building..." required /><br>
                        </div>
                        
                        <div class="modal-footer">
                            <button type="button" name="dev_add" id="dev_add" class="btn btn-success">Create new Device</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script src="ManageClass22.js"></script>
        <div class="toast-container" id="toast-container"></div>
    </body>

    </html>