<html>
<head>
    <title>Menu del dia</title>
    <style type="text/css">
        @page { margin: 0px; }
        body {
            font-family:'Courier',Sans-Serif;
            font-size: 11px;
            height: 100%;
            background: #252525;
        }
        p{
            color: #ffffff;
        }
        .img_menu_left{
            position: fixed;
            top: 15%;
            float: left;
            z-index: 2;
        }
        .img_menu_right{
            position: fixed;
            top: 15%;
            float: right;
            z-index: 3;
        }
        #page-wrap {
            position:relative;
            width: 600px;
            max-height:100%;
            margin: 0 auto;
            top:30%;
            z-index: 4;
        }
        header{
            position: fixed;
            height: 30%;
            z-index: 1;
        }
        header>img{
          margin:10% 30% 0 30%;

        }
        .titulo-container
        {
            font-family: "Courier",Sans-Serif;
            height: 5%;
            margin:5% 35% 0 36%;
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
            position: absolute;
            font-family: "Courier",Sans-Serif;
            font-size: 14px !important;
            color: #fff;
            max-height: 100%;
            text-align: center !important;
        }
        .list-platillos>ul>li{
            display: block;
            line-height: 2.5em;
            list-style: none;
            text-decoration: none;
            color: #ffffff;
            border-bottom: 3px dotted #ffffff;
            font-weight: bolder;
            text-transform: uppercase;
        }
    </style>
    <meta charset="UTF-8">
    <meta http-equiv="Content-type" content="text/html; charset=UTF-8">
</head>
<body>
<header>
    <img src="{$DOC_ROOT}/images/img_menu_logo.png" />
    <div class="titulo-container">
        <p>MENU DEL DIA</p>
    </div>
</header>
<div class="img_menu_left">
    <img src="{$DOC_ROOT}/images/img_menu_left.png">
</div>
<div class="img_menu_right">
    <img src="{$DOC_ROOT}/images/img_menu_rigth.png">
</div>
    <div id="page-wrap">
        <div class="list-platillos">
            <ul>
                {assign var=con value=1}
                {foreach from=$elements key=key item=item name=foo}
                    <li>{$item}</li>
                {assign var=con value=$con+1}
                {foreachelse}
                    <li>Sin platillos en el menu</li>
                {/foreach}
            </ul>
        </div>
    </div
</body>
</html>