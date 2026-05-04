<html>
<head>
    <title>WholesaleBox</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <style type="text/css">
        html, body {
            font-family: arial, tahoma, verdana, sans-serif;
            height: 100%;
            margin: 0;
        }

        #legacy-browser-warning {
            position: fixed !important;
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            height: 100%;
            z-index: 11;
            width: 100%;
            display: table;
        }

        #legacy-browser-warning .bg-style {
            background-color: #333;
            filter: alpha(opacity=50);
            opacity: 0.5;
            position: absolute;
            width: 100%;
            height: 100%;
            z-index: -1;
        }

        #legacy-browser-warning .popup-wrapper {
            display: table-cell;
            zoom: 1;
            vertical-align: middle;
            position: relative;
            *width: 100%;
            *text-align: center;
        }

        #legacy-browser-warning .popup-container {
            width: 650px;
            background-color: rgba(51, 51, 51, 0.4);
            filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#4C333333', endColorstr='#4C333333');
            margin: 0 auto;
            padding: 15px;
            *text-align: left;
            *margin-top: 70px;
        }

        #legacy-browser-warning .popup-body {
            background-color: #fff;
            padding: 10px;
            *width: 100%;
        }

        #legacy-browser-warning .popup-header {
            background-color: #f2f2f2;
            border: 1px solid #e6e6e6;
            padding: 10px;
            font-size: 16px;
            font-weight: 600;
            *width: 100%;
        }

        #legacy-browser-warning .marginTop10 {
            margin-top: 10px;
        }

        #legacy-browser-warning .browser-warn {
            background: url("<?php echo $base; ?>catalog/view/theme/default/image/browsers.png") no-repeat 0 0;
            width: 52px;
            height: 72px;
            display: block;
            margin: 0 auto;
        }

        #legacy-browser-warning .browser-warn.ie {
            background-position: -51px 0px;
        }

        #legacy-browser-warning .browser-warn.firefox {
            background-position: -107px 0px;
        }

        #legacy-browser-warning .browser-warn.safari {
            background-position: -161px 0px;
        }

        #legacy-browser-warning table.browser-warn-info {
            width: 100%;
        }

        #legacy-browser-warning table.browser-warn-info td {
            width: 25%;
            text-align: center;
        }

        #legacy-browser-warning table.browser-warn-info td > a {
            color: #666;
        }

        #legacy-browser-warning table.browser-warn-info td > a:hover {
            text-decoration: underline;
            color: #2271b2;

        }
    </style>
</head>
<body>
<div style="background-color:#ffffff;*text-align:center;">
    <div >
        <div>
            <a href="/" style="display: inline-block;vertical-align: middle;*display:inline;">
                <img  src="<?php echo $base;?>catalog/view/theme/default/image/header_snapshot.png" style="border: none;"/>
            </a>



        </div>
    </div>
</div>
<div id="legacy-browser-warning">
    <div class="bg-style"></div>
    <div class="popup-wrapper">
        <div class="popup-container">
            <div class="popup-header">
                Did you know that your browser is out of date?
            </div>
            <div class="popup-body">
                <p>For an optimized WholesaleBox experience, upgrade your browser or install the latest version of
                    any other new-generation browser.</p>

                <p class="marginTop10">Simply click on the icon to download :</p>
                <table class="browser-warn-info">
                    <tr>
                        <td>
                            <a target="_blank" href="http://www.microsoft.com/windows/Internet-explorer/default.aspx">
                                <span class="browser-warn ie"></span>
                                Internet Explorer
                            </a>
                        </td>
                        <td>
                            <a target="_blank" href="http://www.mozilla.com/firefox/">
                                <span class="browser-warn firefox"></span>
                                Firefox
                            </a>
                        </td>
                        <td>
                            <a target="_blank" href="http://www.apple.com/safari/download/">
                                <span class="browser-warn safari"></span>
                                Safari
                            </a>
                        </td>
                        <td>
                            <a target="_blank" href="http://www.google.com/chrome">
                                <span class="browser-warn"></span>
                                Chrome
                            </a>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>
