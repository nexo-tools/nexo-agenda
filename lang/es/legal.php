<?php

// Legal pages (privacy + terms), rendered by legal/show.
//
// NOT reviewed by a lawyer. Written to describe accurately what this codebase
// actually does — which is the part an agent can get right — so that a review,
// if the owner wants one, starts from something true rather than from a
// template full of clauses about data the app never collects.
//
// Spanish is the source locale; en/pt are translations of this file.
return [
    'updated' => 'Última actualización: 28 de julio de 2026',

    // Rendered only when NEXO_LEGAL_OPERATOR / NEXO_LEGAL_CONTACT are set.
    'operator' => [
        'h' => 'Quién opera esta instancia',
        'p' => 'Esta instancia la opera :operator.',
        'contact' => 'Para cualquier consulta sobre tus datos puedes escribir a :contact.',
    ],

    'privacy' => [
        'title' => 'Privacidad',
        'intro' => 'Esta instancia de Nexo Agenda es open source y self-hosted. Recogemos lo mínimo para que una reserva funcione, y nada más. No usamos cookies de seguimiento, no hay analítica de terceros y no se envía información a redes publicitarias.',
        'sections' => [
            [
                'h' => 'Dos relaciones distintas',
                'p' => 'Aquí conviven dos tipos de personas: el negocio, que abre una cuenta para recibir reservas, y quien reserva un turno en la página de ese negocio, sin crear ninguna cuenta. Quien opera esta instancia trata los datos de ambos para que el servicio funcione. El negocio, en cambio, es el responsable frente a su propio cliente: decide qué hace con esos datos después de la reserva. Si reservaste un turno y quieres que tus datos se corrijan o se borren, puedes pedírselo al negocio o a quien opera esta instancia; los dos pueden hacerlo.',
            ],
            [
                'h' => 'Qué guardamos de la cuenta del negocio',
                'p' => 'Nombre, email y una versión cifrada (hash) de la contraseña. Del negocio guardamos además lo que publica en su página: nombre, rubro, ciudad, dirección, teléfono de WhatsApp, descripción, color, logo y zona horaria, junto con sus servicios y los nombres de las personas de su equipo. Si inicias sesión con Nexo ID, guardamos también el identificador que ese servicio nos da para reconocerte.',
            ],
            [
                'h' => 'Qué guardamos de quien reserva un turno',
                'p' => 'El nombre que escribes al reservar, un email o un teléfono para poder confirmarte y avisarte, la nota que dejes si quieres, y el idioma en el que reservaste —para escribirte en ese idioma y no en otro—. Con eso se arma el turno: servicio, profesional, día y hora. No creamos una cuenta ni te pedimos contraseña. Esos datos son visibles para el negocio donde reservaste —es quien necesita saber a quién atiende— y aparecen en su agenda, en su listado de clientes y en los archivos CSV que descargue.',
            ],
            [
                'h' => 'El enlace para gestionar tu turno',
                'p' => 'Al reservar te llega un enlace propio con el que puedes ver, cancelar o reprogramar el turno sin tener cuenta. Ese enlace es la llave: en la base de datos guardamos solo su huella (hash), nunca el enlace en sí, así que aunque alguien accediera a la base no podría reconstruirlo. Quien tenga el enlace puede gestionar ese turno, por eso conviene no compartirlo.',
            ],
            [
                'h' => 'Lista de espera y recordatorios',
                'p' => 'Si te anotas en la lista de espera de un día, guardamos tu nombre y tu email para avisarte si se libera un lugar. El recordatorio del turno (24 horas antes) sale del mismo dato de contacto que dejaste al reservar. Nada de esto se usa para mandarte publicidad.',
            ],
            [
                'h' => 'Reseñas',
                'p' => 'Si tu turno quedó marcado como asistido, puedes dejar una puntuación y un comentario. Se publican en la página del negocio junto al nombre con el que reservaste, y el negocio puede ocultarlos.',
            ],
            [
                'h' => 'Métricas sin cookies',
                'p' => 'Contamos cuántas personas distintas vieron la página de un negocio con una huella que se calcula con la fecha del día y se descarta: no guardamos tu IP ni tu navegador, y la huella de hoy no se puede comparar con la de mañana. No sabemos quién sos ni podemos seguirte entre sitios.',
            ],
            [
                'h' => 'Contador del ecosistema',
                'p' => 'Si esta instancia lo activa, las páginas del panel del negocio envían una visita anónima (qué herramienta y qué ruta, sin datos personales) al hub de Nexo. Nunca se envía desde la página pública de un negocio, y se respeta la señal "Do Not Track" del navegador.',
            ],
            [
                'h' => 'Cookies',
                'p' => 'Solo las necesarias para que la web funcione: la de sesión (para mantener identificado al negocio que inicia sesión) y las que recuerdan el idioma y el tema claro u oscuro que elegiste. Ninguna sirve para publicidad ni para seguimiento.',
            ],
            [
                'h' => 'Correos',
                'p' => 'Las confirmaciones, los recordatorios, los avisos de cancelación o reprogramación y los correos de la cuenta se envían a través de un proveedor de email externo, que necesariamente procesa la dirección de destino y el contenido del mensaje para poder entregarlo.',
            ],
            [
                'h' => 'La agenda del profesional en su calendario',
                'p' => 'Cada profesional puede suscribir su agenda a un calendario externo mediante un enlace secreto. Ese calendario incluye el nombre de quien reservó y el servicio, así que ese enlace no debería compartirse; el negocio puede regenerarlo cuando quiera y el anterior deja de funcionar.',
            ],
            [
                'h' => 'Formulario de contacto',
                'p' => 'Si nos escribes por el formulario de contacto guardamos el mensaje, el tipo de consulta, la página desde la que escribiste y tu email si lo dejas, para poder responderte.',
            ],
            [
                'h' => 'Cuánto tiempo',
                'p' => 'Los turnos, las reseñas y las estadísticas se conservan mientras el negocio mantenga su cuenta: son su historial. No hay borrado automático por antigüedad. Al eliminarse una cuenta se borran en cascada su negocio, sus servicios, su equipo, sus turnos, su lista de espera, sus reseñas y sus métricas.',
            ],
            [
                'h' => 'Tus derechos',
                'p' => 'Puedes pedir acceso a tus datos, su corrección o su borrado. Si tienes una cuenta, escribe a quien opera esta instancia (el contacto está más abajo y en la página de ayuda): hoy el borrado de una cuenta se hace a pedido, no hay un botón para eso. Si reservaste un turno, puedes cancelarlo desde tu enlace y pedirle al negocio que borre tus datos de su listado.',
            ],
            [
                'h' => 'Otras instancias',
                'p' => 'Nexo Agenda se puede instalar en cualquier servidor. Cada instalación es independiente y responsable de sus propios datos: esta política habla solo de esta instancia.',
            ],
        ],
    ],

    'terms' => [
        'title' => 'Términos de uso',
        'intro' => 'Al usar esta instancia de Nexo Agenda aceptas lo que sigue. Es un servicio gratuito, ofrecido tal cual está.',
        'sections' => [
            [
                'h' => 'Qué es el servicio',
                'p' => 'Una herramienta para que un negocio publique su página de reservas, cargue sus servicios, su equipo y sus horarios, y reciba turnos online con confirmación y recordatorio por email. No procesamos pagos, no cobramos señas ni comisiones y no somos parte del acuerdo entre el negocio y su cliente.',
            ],
            [
                'h' => 'La cuenta del negocio',
                'p' => 'Necesitas una cuenta para publicar una página y recibir reservas. Eres responsable de lo que pase con tu cuenta y de mantener tu contraseña a salvo. Reservar un turno, en cambio, no requiere cuenta.',
            ],
            [
                'h' => 'El negocio responde por los datos de sus clientes',
                'p' => 'Los datos de quien reserva se recogen para que el negocio pueda prestar el servicio, y el negocio es el responsable frente a esa persona: tiene que usarlos solo para atender el turno, no cederlos ni usarlos para publicidad sin permiso, atender los pedidos de acceso o borrado y cumplir la normativa que le corresponda. Lo mismo vale para lo que exporte a CSV o suscriba a su calendario: una vez descargado, ese archivo queda bajo su control.',
            ],
            [
                'h' => 'El turno es entre el negocio y su cliente',
                'p' => 'La prestación del servicio, su precio, la puntualidad, la política de cancelación y cualquier reclamo son asuntos entre el negocio y quien reservó. Nexo Agenda solo pone la agenda.',
            ],
            [
                'h' => 'Uso indebido',
                'p' => 'No se permite publicar negocios falsos o engañosos, suplantar a terceros, subir contenido ilegal, reservar turnos a nombre de otras personas ni usar la herramienta para recolectar datos con fines ajenos a la reserva. Cualquiera puede reportar un negocio desde el formulario de contacto, y quien opera esta instancia puede darlo de baja: su página deja de estar disponible y no se pueden reservar más turnos.',
            ],
            [
                'h' => 'Disponibilidad',
                'p' => 'El servicio se ofrece sin garantías de disponibilidad. Hacemos lo razonable para que esté en línea, pero puede haber interrupciones: un negocio no debería depender solo de esta agenda para trabajar.',
            ],
            [
                'h' => 'Límite de responsabilidad',
                'p' => 'Quien opera esta instancia no se hace responsable de daños derivados del uso del servicio, incluidos turnos que no se cumplan, avisos que no lleguen o pérdidas de datos.',
            ],
            [
                'h' => 'Software libre',
                'p' => 'Nexo Agenda se distribuye con licencia MIT: puedes leer el código, modificarlo y alojar tu propia instancia. El software se entrega sin garantías, según indica esa licencia.',
            ],
            [
                'h' => 'Cambios',
                'p' => 'Estos términos pueden cambiar. La fecha de arriba indica la última actualización.',
            ],
        ],
    ],
];
