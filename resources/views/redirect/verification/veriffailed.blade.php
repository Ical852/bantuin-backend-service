<html>

<head>
    <link href="https://fonts.googleapis.com/css?family=Nunito+Sans:400,400i,700,900&display=swap" rel="stylesheet">
</head>
<title>Verification Failed</title>
<link rel="icon" href="{{ asset('images/logo.png') }}" rel="icon" type="image/png" sizes="192x192">
<link rel="shortcut icon" href="{{ asset('images/logo.png') }}" rel="icon" type="image/png" sizes="192x192">
<style>
    body {
        text-align: center;
        padding: 40px 0;
        background: #EBF0F5;
    }

    h1 {
        color: #E64848;
        font-family: "Nunito Sans", "Helvetica Neue", sans-serif;
        font-weight: 900;
        font-size: 40px;
        margin-bottom: 10px;
    }

    p {
        color: #404F5E;
        font-family: "Nunito Sans", "Helvetica Neue", sans-serif;
        font-size: 20px;
        margin: 0;
    }

    .checkmark {
        color: #E64848;
        font-size: 100px;
        line-height: 200px;
    }

    .card {
        background: white;
        padding: 60px;
        border-radius: 4px;
        box-shadow: 0 2px 3px #C8D0D8;
        display: inline-block;
        margin: 0 auto;
    }

    .button-3 {
        appearance: none;
        background-color: #E64848;
        border: 1px solid rgba(27, 31, 35, .15);
        border-radius: 6px;
        box-shadow: rgba(27, 31, 35, .1) 0 1px 0;
        box-sizing: border-box;
        color: #fff;
        cursor: pointer;
        display: inline-block;
        font-family: -apple-system, system-ui, "Segoe UI", Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji";
        font-size: 14px;
        font-weight: 600;
        line-height: 20px;
        padding: 6px 16px;
        position: relative;
        text-align: center;
        text-decoration: none;
        user-select: none;
        -webkit-user-select: none;
        touch-action: manipulation;
        vertical-align: middle;
        white-space: nowrap;
        margin-top: 16px;
        padding-inline: 20px;
        padding-block: 10px;
    }

    .button-3:focus:not(:focus-visible):not(.focus-visible) {
        box-shadow: none;
        outline: none;
    }

    .button-3:hover {
        background-color: #972C2C;
    }

    .button-3:focus {
        box-shadow: #972C2C 0 0 0 3px;
        outline: none;
    }

    .button-3:disabled {
        background-color: #D39494;
        border-color: rgba(27, 31, 35, .1);
        color: rgba(255, 255, 255, .8);
        cursor: default;
    }

    .button-3:active {
        background-color: #8E2929;
        box-shadow: rgba(20, 70, 32, .2) 0 1px 0 inset;
    }
</style>

<body>
    <div class="card">
        <div style="border-radius:200px; height:200px; width:200px; background: #FAF5F5; margin:0 auto;">
            <p class="checkmark">X</p>
        </div>
        <h1>Failed</h1>
        <p>Verifikasi Email Anda Gagal,<br />Anda belum dapat mengakses aplikasi,<br />token invalid atau expired</p>
        <button class="button-3" role="button">Go to Home</button>
    </div>
</body>

</html>

<script type="text/javascript">
    setTimeout(() => {
        document.cookie = 'verifyfailed' + '=; Max-Age=-99999999;';
    }, 1000);
</script>
