<html>
    <head>
        <title><?php echo $this->lang->line('profiler_empresa'); ?></title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <base href="<?php echo base_url(); ?>">

        <meta name="author" content="Setiar">
        <?php $base_url = base_url(); ?>
        <script type="text/javascript">
            root = "<?php echo $base_url; ?>";
        </script>
        <!-- The styles -->
        <link href="<?php echo base_url(); ?>static/charisma/bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="<?php echo base_url(); ?>static/charisma/css/charisma-app.css" rel="stylesheet">
        <link href='<?php echo base_url(); ?>static/charisma/bower_components/chosen/chosen.min.css' rel='stylesheet'>
        <link href='<?php echo base_url(); ?>static/charisma/bower_components/colorbox/example3/colorbox.css' rel='stylesheet'>
        <link href='<?php echo base_url(); ?>static/charisma/bower_components/responsive-tables/responsive-tables.css' rel='stylesheet'>
        <!--<link href='<?php echo base_url(); ?>static/charisma/bower_components/datepicker/css/datepicker.css' rel='stylesheet'>-->
        <link href='<?php echo base_url(); ?>static/charisma/bower_components/bootstrap-tour/build/css/bootstrap-tour.min.css' rel='stylesheet'>
        <link href='<?php echo base_url(); ?>static/charisma/css/jquery.noty.css' rel='stylesheet'>
        <link href='<?php echo base_url(); ?>static/charisma/css/noty_theme_default.css' rel='stylesheet'>
        <link href='<?php echo base_url(); ?>static/charisma/css/elfinder.min.css' rel='stylesheet'>
        <link href='<?php echo base_url(); ?>static/charisma/css/elfinder.theme.css' rel='stylesheet'>
        <link href='<?php echo base_url(); ?>static/charisma/css/jquery.iphone.toggle.css' rel='stylesheet'>
        <link href='<?php echo base_url(); ?>static/charisma/css/uploadify.css' rel='stylesheet'>
        <link href='<?php echo base_url(); ?>static/charisma/css/animate.min.css' rel='stylesheet'>
        <link href='<?php echo base_url(); ?>static/charisma/css/fuentes.css' rel='stylesheet'>
        <link href='<?php echo base_url(); ?>static/charisma/css/union.css' rel='stylesheet'>
        <link href='<?php echo base_url(); ?>static/crei/css/timeline.css' rel='stylesheet'>

        <!-- jQuery -->
        <script src="<?php echo base_url(); ?>static/charisma/bower_components/jquery/jquery.js"></script>
        <script src="<?php echo base_url(); ?>static/crei/js/jquery.validate.min.js"></script>
        <script src="<?php echo base_url(); ?>static/crei/js/localization/messages_es_AR.min.js"></script>

        <!-- The HTML5 shim, for IE6-8 support of HTML5 elements -->
        <!--[if lt IE 9]>
        <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->

        <!-- The fav icon -->

        <script>
            function validarFechaCarga(fechaAChequear) {
                // 1. Obtener la fecha actual (la que se está ejecutando el código)
                const fechaActual = new Date(); // Esto tomará la fecha y hora actual del sistema.

                // 2. Calcular la fecha de inicio del rango (el día 1 del mes actual)
                const inicioRango = new Date(fechaActual.getFullYear(), fechaActual.getMonth(), 1);
                // El tercer parámetro (1) establece el día del mes al primero.

                // 3. Calcular la fecha de fin del rango (el día 5 del mes siguiente)
                // Primero, avanzamos al siguiente mes
                const finRango = new Date(fechaActual.getFullYear(), fechaActual.getMonth() + 1, 5);
                // getMonth() devuelve un índice de 0 a 11. Sumarle 1 nos da el mes siguiente.
                // El tercer parámetro (5) establece el día del mes al quinto.
                if(fechaActual.getDate() <= 5){
                    inicioRango.setMonth(fechaActual.getMonth()-1);
                    finRango.setMonth(fechaActual.getMonth());
                }
                // Opcional: Para asegurar que el día 5 del mes siguiente incluya todo el día,
                // puedes establecer la hora al final del día.
                finRango.setHours(23, 59, 59, 999);

                // 4. Convertir todas las fechas a milisegundos para una comparación precisa
                const tiempoFechaAChequear = fechaAChequear.getTime();
                const tiempoInicioRango = inicioRango.getTime();
                const tiempoFinRango = finRango.getTime();

                // 5. Realizar la validación
                const estaDentroDelRango = tiempoFechaAChequear >= tiempoInicioRango && tiempoFechaAChequear <= tiempoFinRango;

                if (estaDentroDelRango) {
                    console.log(`La fecha ${fechaAChequear.toLocaleDateString()} ESTÁ dentro del rango permitido (${inicioRango.toLocaleDateString()} - ${finRango.toLocaleDateString()}). Se permite la carga.`);
                    return true; // Se permite la carga
                } else {
                    console.log(`La fecha ${fechaAChequear.toLocaleDateString()} NO está dentro del rango permitido (${inicioRango.toLocaleDateString()} - ${finRango.toLocaleDateString()}). NO se permite la carga.`);
                    return false; // No se permite la carga
                }
            }

            function crearFechaDesdeFormatoEspanol(fechaEnEspanol) {
                // 1. Reemplazar guiones por barras si los hay, para unificar el formato.
                const fechaLimpia = fechaEnEspanol.replace(/-/g, '/');

                // 2. Dividir la cadena en partes (día, mes, año).
                const partes = fechaLimpia.split('/');

                // 3. Validar que tengamos 3 partes (día, mes, año).
                if (partes.length !== 3) {
                    console.warn(`Formato de fecha inválido: "${fechaEnEspanol}". Se esperaba DD/MM/AAAA o DD-MM-AAAA.`);
                    return null;
                }

                // 4. Convertir las partes a números enteros.
                // Es importante restar 1 al mes, ya que JavaScript usa un índice base 0 (0 = Enero, 11 = Diciembre).
                const dia = parseInt(partes[0], 10);
                const mes = parseInt(partes[1], 10) - 1; // ¡Aquí la clave!
                const anio = parseInt(partes[2], 10);

                // 5. Validar que las partes sean números válidos.
                if (isNaN(dia) || isNaN(mes) || isNaN(anio)) {
                    console.warn(`Una o más partes de la fecha "${fechaEnEspanol}" no son números válidos.`);
                    return null;
                }

                // 6. Crear el objeto Date.
                // new Date(año, mesIndex, día)
                const fechaCreada = new Date(anio, mes, dia);

                // Opcional: Validar si la fecha creada es realmente válida
                // (Esto detecta fechas como "31/02/2024" que Date ajustaría a "02/03/2024")
                if (fechaCreada.getFullYear() !== anio ||
                        fechaCreada.getMonth() !== mes ||
                        fechaCreada.getDate() !== dia) {
                    console.warn(`La fecha "${fechaEnEspanol}" no es una fecha calendario válida (ej: 31 de Febrero).`);
                    return null;
                }

                return fechaCreada;
            }

            $.validator.addMethod("time24", function (value, element) {
                if (!/^\d{2}:\d{2}$/.test(value))
                    return false;
                var parts = value.split(':');
                if (parts[0] > 23 || parts[1] > 59)
                    return false;
                return true;
            }, "Hora inválida.");
            $.validator.addMethod("empty", function (value, element) {
                if (!/^\d{2}:\d{2}$/.test(value))
                    return false;
                var parts = value.split(':');
                if (parts[0] > 23 || parts[1] > 59)
                    return false;
                return true;
            }, "Hora inválida.");
            $.validator.addMethod("sinAlumnos", function (value, element) {
                if (value.length === 0)
                    return false;
                return true;
            }, "Hora inválida.");
            jQuery.validator.addMethod(
                    "dateES",
                    function (value, element) {
                        let check = false;debugger;
                        let re = /^\d{1,2}\/\d{1,2}\/\d{4}$/;
                        if (re.test(value)) {
                            let valor = crearFechaDesdeFormatoEspanol(value);
                            check = validarFechaCarga(valor);

                            /*    var adata = value.split('/');
                             var mm = parseInt(adata[1],10);
                             var dd = parseInt(adata[0],10);
                             var yyyy = parseInt(adata[2],10);
                             var xdata = new Date(yyyy,mm-1,dd);
                             var now = new Date();
                             var past = new Date();
                             past.setMonth(past.getMonth(), 1);
                             past.setHours(0,0,0,0);
                             console.log('past='+past);
                             console.log('xdata='+xdata);
                             console.log('mm='+mm);
                             console.log('xdata.getMonth ()='+xdata.getMonth ());
                             if ( ( xdata.getFullYear() == yyyy ) && ( xdata.getMonth () == mm-1) && ( xdata.getDate() == dd ) && (xdata <= now) && (xdata >= past) )
                             {    
                             check = true;
                             }
                             else
                             check = false;*/
                        } else {
                            check = false;
                        }
                        return this.optional(element) || check;
                    }
            ,
                    "Por favor ingrese una fecha válida. Debe estar dentro del mes en curso o el mes anterior si la fecha actual es menor al día "
                    );
        </script>
    </head>

    <body>
