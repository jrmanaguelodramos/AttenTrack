<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Room/Scanner</title>
    <style>
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .title {
            background: #e6e6e6;
            padding: 18px 50px;
            border-radius: 25px;
            font-weight: bold;
            font-size: 28px;
            border: 3px solid #2b2b2b;
            color: #1f3555;
            box-shadow: 0 4px 0 #1f3555;
            letter-spacing: 1px;
        }

        .actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .actions input {
            padding: 8px 12px;
            border-radius: 20px;
            border: none;
        }

        .add-btn {
            padding: 8px 15px;
            border-radius: 20px;
            border: none;
            background: #d9d9d9;
            cursor: pointer;
        }

        .back-btn {
            display: inline-block;
            padding: 10px 15px;
            color: black;
            background-color: #ffffff;
            text-decoration: none;
            border-radius: 50%;
            font-size: 18px;
            transition: 0.2s;
        }

        .back-btn:hover {
            background-color: #b8b8b8;
        }

        .back-btn:active {
            transform: scale(0.95);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #d9d9d9;
        }

        th,
        td {
            border: 1px solid #999;
            padding: 12px;
            text-align: center;
        }

        th {
            background: #bfbfbf;
        }


        .mode {
            padding: 5px 10px;
            margin: 2px;
            border-radius: 15px;
            border: none;
            background: #e6e6e6;
            cursor: pointer;
        }


        .delete-btn {
            background: none;
            border: none;
            font-size: 18px;
            cursor: pointer;
        }


        .bottom {
            display: flex;
            justify-content: flex-end;
            margin-top: 60px;
        }

        .emergency-btn {
            background: red;
            color: white;
            border: none;
            padding: 20px 40px;
            border-radius: 40px;
            font-size: 20px;
            font-weight: bold;
            cursor: pointer;
        }
    </style>
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
    <?php include 'header.php'; ?>
    <div class="container">

        <div class="alert_dev"></div>
        <div class="top-bar">
            <div class="title">MANAGE Rooms/Scanners</div>

            <div class="actions">
                <input type="text" placeholder="🔍">
                <input type="text" placeholder="Room">
                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#new-device" style="font-size: 18px; float: right; margin-top: -6px;">New Device</button>
                <a href="dashboard.php" class="back-btn">←</a>
            </div>
        </div>

        <div id="devices"></div>

        <div class="bottom">
            <button class="emergency-btn">
                EMERGENCY EXIT ⚠
            </button>
        </div>

    </div>

    <div class="modal fade" id="new-device" tabindex="-1" role="dialog" aria-labelledby="New Device" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="exampleModalLongTitle">Add new device:</h3>
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
</body>

</html>