<!DOCTYPE html>
<head>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://unpkg.com/mqtt/dist/mqtt.min.js"></script>
<script src = "urls.js"></script>
<script>

function valores(v1,v2,v3) {
	$("#x1").html(v1);
	$("#x2").html(v2);
	$("#x3").html(v3);
}

function devuelve(topic, message) {
	if(topic == "lab1") {
        $("#x1").html(message.toString());	
	} else if (topic == "lab2") {
        $("#x2").html(message.toString());
    }else if (topic == "lab3") {
        $("#x3").html(message.toString());
    }
    
}

        const clientId = 'Monitor'
        const host = 'ws:' + ip + ':8093/mqtt'
        const topicoActual = 'Monitor'
        let estado = 'Encendido'
        let medicion = 0
        const options = {
            keepalive: 60,
            clientId: clientId,
            username: 'monitor',
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
            client.subscribe("lab1", {qos: 0}, (error) => {
                if (!error) {
                    console.log("OK")
                }
                else {
                    console.log("NOK")
                }
            })
            client.subscribe("lab2", {qos: 0}, (error) => {
                if (!error) {
                    console.log("OK")
                }
                else {
                    console.log("NOK")
                }
            })
            client.subscribe("lab3", {qos: 0}, (error) => {
                if (!error) {
                    console.log("OK")
                }
                else {
                    console.log("NOK")
                }
            })
            
        })

        //Recibiendo datos
        client.on('message', (topic, message) => {
            console.log('Recepción de Mensaje: ' + message.toString()+ '\ndel Topico: '+ topic)
            devuelve(topic,message)
        })
        client.publish("lab1", "wasd", {qos: 0, retain: false});

    </script>
</head>
<body>    
Lab 1: <p id="x1"></p><br>
Lab 2: <p id="x2"></p><br>
Lab 3: <p id="x3"></p><br>
</body>
</html>