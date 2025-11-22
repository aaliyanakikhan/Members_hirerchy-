<?php
require_once __DIR__ . "/db.php";
require_once __DIR__ . "/Member.php";

$db = (new Database())->getConnection();
$memberObj = new Member($db);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Members Hierarchy</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* Tree styling */
        #tree ul {
            list-style-type: none;
            padding-left: 20px;
            position: relative;
        }
        #tree ul::before {
            content: '';
            position: absolute;
            top: 0;
            left: 10px;
            border-left: 2px solid #ccc;
            height: 100%;
        }
        #tree li {
            position: relative;
            padding-left: 20px;
            margin: 10px 0;
        }
        #tree li::before {
            content: '';
            position: absolute;
            top: 10px;
            left: 0;
            border-top: 2px solid #ccc;
            width: 10px;
        }
        #tree li::after {
            content: '➤';
            position: absolute;
            left: 0;
            top: 0;
            font-size: 12px;
            color: #007BFF;
        }

        /* Overlay background */
        #overlay {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 999;
        }

        /* Popup dialog */
        #popup {
            display: none;
            position: fixed;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            border: 1px solid #ccc;
            padding: 20px;
            background: #fff;
            width: 320px;
            z-index: 1000;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
            border-radius: 6px;
        }

        #popup h3 {
            margin-top: 0;
        }

        #addMemberBtn {
            background-color: #007BFF;
            color: white;
            padding: 10px 20px;
            border: none;
            font-size: 16px;
            cursor: pointer;
            margin-top: 20px;
        }
        #addMemberBtn:hover {
            background-color: #0056b3;
        }

        #popup button {
            margin-right: 10px;
        }
    </style>
</head>
<body>

<h2>Members Tree</h2>
<div id="tree">
    <?php $memberObj->buildTree(); ?>
</div>

<button id="addMemberBtn">Add Member</button>

<!-- Overlay + Popup -->
<div id="overlay"></div>
<div id="popup">
    <h3>Add Member</h3>
    <form id="addMemberForm">
        <label>Parent Name:</label><br>
        <input type="text" name="parentName" placeholder="Type parent name"><br><br>

        <label>New Member Name:</label><br>
        <input type="text" name="name" required pattern="[A-Za-z ]+"><br><br>

        <button type="submit">Save Changes</button>
        <button type="button" id="closePopup">Close</button>
    </form>
</div>

<script>
$(document).ready(function(){
    // Open popup
    $("#addMemberBtn").click(function(){
        $("#overlay, #popup").fadeIn();
    });

    // Close popup
    $("#closePopup, #overlay").click(function(){
        $("#overlay, #popup").fadeOut();
    });

    // Handle form submit
    $("#addMemberForm").on("submit", function(e){
        e.preventDefault();
        $.ajax({
            url: "insert.php",
            type: "POST",
            data: $(this).serialize(),
            success: function(response){
                let data = JSON.parse(response);
                if(data.success){
                    let newLi = "<li id='member-" + data.id + "'>" + data.name + "</li>";
                    if(data.parentId){
                        let parentLi = $("#member-" + data.parentId);
                        if(parentLi.length){
                            if(parentLi.children("ul").length === 0){
                                parentLi.append("<ul></ul>");
                            }
                            parentLi.children("ul").append(newLi);
                        } else {
                            location.reload();
                        }
                    } else {
                        if($("#tree > ul").length === 0){
                            $("#tree").append("<ul></ul>");
                        }
                        $("#tree > ul").append(newLi);
                    }
                    $("#overlay, #popup").fadeOut();
                } else {
                    alert("Error: " + data.message);
                }
            }
        }).fail(function(xhr){
            alert("Server error: " + xhr.statusText);
        });
    });
});
</script>

</body>
</html>