<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>CEPROZAC</title>
  <META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
    <link href="css/font-awesome.css" rel="stylesheet" type="text/css" />
    <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="css/animate.css" rel="stylesheet" type="text/css" />
    <link href="css/admin.css" rel="stylesheet" type="text/css" />
    <style type="text/css">
      body {

        background-image: url(CEPROZAC.jpeg);

        background-position: center center;

        background-repeat: no-repeat;

        background-attachment: fixed;

        background-size: cover;

  /* Fijamos un color de fondo para que se muestre mientras se está

  cargando la imagen de fondo o si hay problemas para cargarla  */

  background-color: #464646;



}
</style>
</head>

<body >
  <div class="wrapper">
    <!--\\\\\\\ wrapper Start \\\\\\-->





    <div class="login_page ">
      <div class="login_content ">
        <div class="panel-heading border login_heading" style="background-color:#1e90ff;" >INICIA SESIÓN AHORA</div>  
        <form role="form" class="form-horizontal" method="POST" action="/auth/login">
         {!! csrf_field() !!}
         <div class="form-group">

          <div class="col-sm-10">
            <input type="email"  name="email" placeholder="Correo" id="inputEmail3" class="form-control">
          </div>
        </div>
        <div class="form-group">

          <div class="col-sm-10">
          <input type="text" name="password" placeholder="Contraseña" id="inputPassword3" class="form-control">
          </div>
        </div>
        <div class="form-group">
          <div class=" col-sm-10">
            <div class="checkbox checkbox_margin">
              <label class="lable_margin">
                <input type="checkbox" name="remember">><p class="pull-left"> Recordar Contraseña</p></label>
                <a href="index.html">
                  <button class="btn btn-default pull-right" type="submit">Iniciar Sesion</button>
                </a></div>
              </div>
            </div>

          </form>
        </div>
      </div>






    </div>





    <script src="js/jquery-2.1.0.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/common-script.js"></script>
    <script src="js/jquery.slimscroll.min.js"></script>
  </body>
  </html>
