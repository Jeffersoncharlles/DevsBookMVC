<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404</title>
    <link rel="stylesheet" href="<?=$base?>/assets/css/404.css">
</head>
<body>
    <div class="cont_principal">
        <div class="cont_error">
        
            <h1>Oops</h1>  
            <p>A página que você está  <br>procurando <br>não está aqui.</p>
        </div>
        <div class="cont_aura_1"></div>
        <div class="cont_aura_2"></div>
    </div>
    <script>
        window.onload = function(){
        document.querySelector('.cont_principal').className= "cont_principal cont_error_active";
    }
    </script>
</body>
</html>