<html>
<head>
    <title>Menu del dia</title>
    <style type="text/css">
        @page { margin: 0px; }
        @font-face {
            font-family: 'Bellerose Light:1.0';
            font-style: normal;
            font-weight: normal;
            src: local('Bellerose Light:1.0'), url('../../assets/plugins/font-awesome/fonts/Bellerose.woff') format('woff');
        }
        @font-face {
            font-family:"Be Kind To The Earth";
            src:url('../../assets/plugins/font-awesome/fonts/Be_Kind_To_The_Earth.eot?') format('eot'),url('../../assets/plugins/font-awesome/fonts/Be_Kind_To_The_Earth.woff') format('woff'),url('../../assets/plugins/font-awesome/fonts/Be_Kind_To_The_Earth.ttf') format('truetype'),url('../../assets/plugins/font-awesome/fonts/Be_Kind_To_The_Earth.svg#BeKindToTheEarth') format('svg');
            font-weight:normal;
            font-style:normal;
        }

        html,body {
            font-family:'Courier',Sans-Serif;

            font-size: 11px;
            line-height: 1;
            height: 100%;
        }
        p{
            color: #ffffff;
        }
        .main{
            position: fixed;
            background-image: url("{$WEB_ROOT}/images/menu-carta.jpg");
            background-repeat: no-repeat;
            height: 100%;
        }
        #page-wrap {
            position:relative;
            width: 600px;
            max-height: 55%;
            margin-right: 0px;
            margin-left: 110px;
            top:30%;
        }
        table {
            font-size: 11px;
            line-height: 20px;
        }
        table.outline-table {
            border: 2px solid #ccc;
            border-spacing: 0;
        }
        tr.border-bottom td, td.border-bottom {
            border-bottom: 1px solid #ccc;
        }
        tr.border-top td, td.border-top {
            border-top: 1px solid #ccc;
        }
        tr.border-right td, td.border-right {
            border-right: 1px solid #ccc;
        }
        tr.border-left td, td.border-left {
            border-left: 1px solid #ccc;
        }
        tr.border-right td:last-child {
            border-right: 0px;
        }
        tr.center td, td.center {
            text-align: center;
            vertical-align: text-top;
        }
        td.pad-left {
            padding-left: 5px;
        }
        tr.right-center td, td.right-center {
            text-align: right;
            padding-right: 50px;
        }
        tr.right td, td.right {
            text-align: right;
        }
        header{
            position: fixed;
            height: 30%;
            z-index: 1;
        }
        .titulo-container
        {
            font-family: "Courier",Sans-Serif;
            height: 5%;
            margin:22% 35% 0 36%;
            border: 2px solid #E44B3C;
            border-radius: 10px;
            font-weight: bolder;
            line-height: 5px;
        }

        .titulo-container>p{
            font-size: 24px;
            margin-left: 10%;
            margin-right: 10%;
            color: #CCCCCC;
            text-align: center;
            vertical-align: middle;

        }
        .list-platillos{
            font-size: 22px;
            color: #fff;
            margin:0 5% 0 5%;
            text-align: center;
            height: 50%;
        }
        .list-platillos>ul>li{
            font-family: "Courier",Sans-Serif;
            list-style: none;
            line-height: 2;
            text-decoration: none;
            color: #ffffff;
            border-bottom: 3px dotted #ffffff;
            font-weight: bolder;
            text-transform: uppercase;
        }
        list-platillos>ul>li:after{
            content: '';
            padding-bottom: 2px;
        }
    </style>
    <meta charset="UTF-8">
    <meta http-equiv="Content-type" content="text/html; charset=UTF-8">
</head>
<body>
<header>
    <div class="titulo-container">
        <p>MENU DEL DIA</p>
    </div>
</header>
<div class="main"></div>
<div id="page-wrap">
    <div class="list-platillos">
        <ul>
            {foreach from=$elements key=key item=item}
                <li>{$item}</li>
            {foreachelse}
                <li>Sin platillos en el menu</li>
            {/foreach}
        </ul>
    </div>
</div>
</body>
</html>