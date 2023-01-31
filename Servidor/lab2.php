<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laboratorio 2 </title>

    <!-- Bootstrap -->
    <link href="vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <link href="vendors/nprogress/nprogress.css" rel="stylesheet">
    <!-- iCheck -->
    <link href="vendors/iCheck/skins/flat/green.css" rel="stylesheet">
	
    <!-- bootstrap-progressbar -->
    <link href="vendors/bootstrap-progressbar/css/bootstrap-progressbar-3.3.4.min.css" rel="stylesheet">
    <!-- JQVMap -->
    <link href="vendors/jqvmap/dist/jqvmap.min.css" rel="stylesheet"/>
    <!-- bootstrap-daterangepicker -->
    <link href="vendors/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="build/css/custom.min.css" rel="stylesheet">
    <script src = "https://unpkg.com/mqtt/dist/mqtt.min.js"></script>
    <script>
        let ip = '<?php echo $_SERVER['SERVER_ADDR']; ?>';
        if (ip == '::1') {
            ip = '127.0.0.1';
        }
        const clientId = 'Servidor'
        const host = 'ws:' + ip + ':8093/mqtt'
        const topicoActual = 'lab2'
        let estado = 'Apagado'
        let medicion = 0
        const options = {
            keepalive: 60,
            clientId: clientId,
            username: 'servidor',
            password: '1234',
            protocolId: 'MQTT',
            protocolVersion: 4,
            clean: true,
            reconnectPeriod: 1000,
            connectTimeout: 30 * 1000,
            will: {
                topic: 'Mensajeros',
                payload: 'Cerrada.!',
                qos: 0,
                retain: false
            },
        }
        console.log('mqtt client Listo')
        const client = mqtt.connect(host, options)

        client.on('error', (err) => {
            console.log('Error de conexion: ', err)
            client.end()
        })

        client.on('reconnect', () => {
            console.log('Reconectando...')
        })

        client.on('connect', () => {
            console.log('Cliente Listo: ' + clientId)
            // Subscribe
            client.subscribe(topicoActual, {qos: 0})
        })

        //Recibiendo datos
        client.on('message', (topic, message, packet) => {
            console.log('Recepción de Mensaje: ' + message.toString() + '\ndel Topico: '+ topic)
            if (message == "Estado: Encendido") {
                estado = 'Encendido';
                mostrarEstado();
            } else if (message == "Estado: Apagado") {
                estado = 'Apagado';
                mostrarEstado();
            }
        })
        client.publish(topicoActual, 'Estado', {qos: 0, retain: false});

        function mostrarEstado() {
            console.log(estado);
            document.getElementById("estado").innerHTML = "Estado: " + estado;
        }

        function encender() {
            client.publish('control', 'lab2.start', {qos: 0, retain: false});
            estado = "Encendido"
        }

        function apagar() {
            client.publish('control', 'lab2.stop', {qos: 0, retain: false});
            estado = "Apagado"
        }

        function interruptor() {
            if (estado == "Encendido") {
                apagar()
            } else {
                encender()
            }
        }

    </script>
  </head>

  <body class="nav-md">
    <div class="container body">
      <div class="main_container">
        <div class="col-md-3 left_col">
          <div class="left_col scroll-view">
            <div class="navbar nav_title" style="border: 0;">
              <a href="index.html" class="site_title"><i class="fa fa-paw"></i> <span>Monitoreo</span></a>
            </div>

            <div class="clearfix"></div>

            <!-- menu profile quick info -->
            <div class="profile clearfix">
              <div class="profile_info">
                <span>Bienvenido,</span>
                <h2>John Doe</h2>
              </div>
            </div>
            <!-- /menu profile quick info -->

            <br />

            <!-- sidebar menu -->
            <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
              <div class="menu_section">
                <h3>General</h3>
                <ul class="nav side-menu">
                  <li><a><i class="fa fa-home"></i> Laboratorios <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="lab1.php">Laboratorio 1</a></li>
                      <li><a href="lab2.php">Laboratorio 2</a></li>
                      <li><a href="lab4.php">Laboratorio 4</a></li>
                    </ul>
                  </li>
                  
                </ul>
              </div>

            </div>
            <!-- /sidebar menu -->

            <!-- /menu footer buttons -->
            <div class="sidebar-footer hidden-small">
              <a data-toggle="tooltip" data-placement="top" title="Settings">
                <span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
              </a>
              <a data-toggle="tooltip" data-placement="top" title="FullScreen">
                <span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span>
              </a>
              <a data-toggle="tooltip" data-placement="top" title="Lock">
                <span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span>
              </a>
              <a data-toggle="tooltip" data-placement="top" title="Logout" href="login.html">
                <span class="glyphicon glyphicon-off" aria-hidden="true"></span>
              </a>
            </div>
            <!-- /menu footer buttons -->
          </div>
        </div>

        <!-- top navigation -->
        
        <!-- /top navigation -->

        <!-- page content -->
        <div class="right_col" role="main">

        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
              <div class="dashboard_graph">

                <div class="row x_title">
                  <div class="col-md-6">
                    <h3>Últimas mediciones</h3>
                  </div>
                  <div class="col-md-6">

                  </div>
                </div>

                <div class="col-md-9 col-sm-9 col-xs-12">
                <?php
                include 'urls.php';
                $servername = $ip. ":3306";
                $username = "root";
                $password = "1234";
                $dbname = "laboratorio";

                // Create connection
                $conn = new mysqli($servername, $username, $password, $dbname);
                // Check connection
                if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
                }

                $sql = "SELECT mensaje, fecha FROM mqtt_mensajes where topic = 'lab2' order by fecha desc limit 10 ";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                // output data of each row
                while($row = $result->fetch_assoc()) {
                    echo $row["mensaje"]. " Wh - Fecha: " . $row["fecha"]. " <br>";
                }
                } else {
                echo "0 results";
                }
                $conn->close();
                ?>
                </div>
                <div class="row">
                    <!-- <h1><div class="col-12" id="estado">Estado: Apagado</div></h1> -->
                    <button type="button" class="btn btn-primary" onclick="interruptor()">Interruptor</button>

                </div>


                <div class="clearfix"></div>
              </div>
            </div>

          </div>
          <br />
          <!-- top tiles -->
          
        </div>

        <!-- /page content -->

        <!-- footer content -->
        <footer>
          <div class="pull-right">
            Gentelella - Bootstrap Admin Template by <a href="https://colorlib.com">Colorlib</a>
          </div>
          <div class="clearfix"></div>
        </footer>
        <!-- /footer content -->
      </div>
    </div>

    <!-- jQuery -->
    <script src="vendors/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap -->
    <script src="vendors/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- FastClick -->
    <script src="vendors/fastclick/lib/fastclick.js"></script>
    <!-- NProgress -->
    <script src="vendors/nprogress/nprogress.js"></script>
    <!-- Chart.js -->
    <script src="vendors/Chart.js/dist/Chart.min.js"></script>
    <!-- gauge.js -->
    <script src="vendors/gauge.js/dist/gauge.min.js"></script>
    <!-- bootstrap-progressbar -->
    <script src="vendors/bootstrap-progressbar/bootstrap-progressbar.min.js"></script>
    <!-- iCheck -->
    <script src="vendors/iCheck/icheck.min.js"></script>
    <!-- Skycons -->
    <script src="vendors/skycons/skycons.js"></script>
    <!-- Flot -->
    <script src="vendors/Flot/jquery.flot.js"></script>
    <script src="vendors/Flot/jquery.flot.pie.js"></script>
    <script src="vendors/Flot/jquery.flot.time.js"></script>
    <script src="vendors/Flot/jquery.flot.stack.js"></script>
    <script src="vendors/Flot/jquery.flot.resize.js"></script>
    <!-- Flot plugins -->
    <script src="vendors/flot.orderbars/js/jquery.flot.orderBars.js"></script>
    <script src="vendors/flot-spline/js/jquery.flot.spline.min.js"></script>
    <script src="vendors/flot.curvedlines/curvedLines.js"></script>
    <!-- DateJS -->
    <script src="vendors/DateJS/build/date.js"></script>
    <!-- JQVMap -->
    <script src="vendors/jqvmap/dist/jquery.vmap.js"></script>
    <script src="vendors/jqvmap/dist/maps/jquery.vmap.world.js"></script>
    <script src="vendors/jqvmap/examples/js/jquery.vmap.sampledata.js"></script>
    <!-- bootstrap-daterangepicker -->
    <script src="vendors/moment/min/moment.min.js"></script>
    <script src="vendors/bootstrap-daterangepicker/daterangepicker.js"></script>

    <!-- Custom Theme Scripts -->
    <script src="build/js/custom.min.js"></script>
	
  </body>
</html>


