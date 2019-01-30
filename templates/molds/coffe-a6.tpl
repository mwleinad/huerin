<html>
<head>
    <title>Menu del dia</title>
    <style type="text/css">
        @page { margin: 0px; }
        body {
            font-family:'Courier','Sans-Serif';
            font-size: 11px;
            height: 100%;
            background: #252525;
        }
        p{
            color: #ffffff;
        }
        .img_menu_left{
            position: fixed;
            top: 18%;
            margin-left:0px;
            z-index: 2;
        }
        .img_menu_right{
            position: fixed;
            margin-left:300px;
            top: 18%;
            z-index: 3;
        }
        #page-wrap {
            position:relative;
            width: 300px;
            margin: 0 auto;
            top:30%;
            z-index: 4;
            page-break-inside: auto;
        }
        header{
            position: fixed;
            height: 30%;
            z-index: 1;
        }
        header>img{
          margin:3% 10% 0 10%;
        }
        .titulo-container
        {
            font-family: "Courier",Sans-Serif;
            height: 7%;
            margin:5% 30% 0 30%;
            border: 2px solid #E44B3C;
            border-radius: 10px;
            font-weight: bolder;
            line-height: 5px;
        }
        .titulo-container>p{
            position: absolute;
            font-size: 17px;
            margin-left: 10%;
            margin-right: 10%;
            color: #CCCCCC;
            text-align: center;
            vertical-align: middle;
        }
        .list-platillos{
            color: #fff;
            width: 100%;
        }
        .list-platillos table {
            position: relative;
            width: 100%;
        }
        .list-platillos table tr td {
            font-size: 16px;
            vertical-align: middle;
            line-height: 1.5em;
            color: #ffffff;
            border-bottom: 3px dotted #ffffff;
            text-align: center;
            font-weight: bold;
            padding-top: 15px;
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
    <img src="{$DOC_ROOT}/images/img_menu_left.png" height="450" width="100">
</div>
<div class="img_menu_right">
    <img src="{$DOC_ROOT}/images/img_menu_rigth.png" height="450" width="100">
</div>
<div id="page-wrap">
    <div class="list-platillos">
        <table cellpadding="0" cellpadding="0">
            {assign var=con value=1}
            {foreach from=$elements key=key item=item name=foo}
                <tr><td>{$item}</td></tr>
                {assign var=con value=$con+1}
                {foreachelse}
                <tr><td>Sin platillos en el menu</td></tr>
            {/foreach}
        </table>
    </div>
</div>

</body>
</html>
