<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style type="text/css">
        .close{
            position: absolute;
            bottom: 0;
            left: 0;
            width:100%;
            height:50px;
            background: #CD061B;
            border:1px solid #CD061B;
            text-transform:uppercase;
            color:#fff;
            font-size: 16px;
        }
        body{margin:0;padding:0;}
    </style>
</head>
<body>
<img src="<?php echo $image; ?>" width="300" height="400" />

<button name="close" class="close" value="Close" onclick="closeWindow();" >Close</button>

<script type="text/javascript" >
    function closeWindow(){
        window.close();
    }
</script>
</body>
</html>