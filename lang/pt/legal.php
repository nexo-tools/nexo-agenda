<?php

// Translation of lang/es/legal.php (the source locale). Keep the sections in the
// same order and describe the same behaviour — this is not a generic template.
return [
    'updated' => 'Última atualização: 28 de julho de 2026',

    'operator' => [
        'h' => 'Quem opera esta instância',
        'p' => 'Esta instância é operada por :operator.',
        'contact' => 'Para qualquer questão sobre seus dados, escreva para :contact.',
    ],

    'privacy' => [
        'title' => 'Privacidade',
        'intro' => 'Esta instância do Nexo Agenda é open source e self-hosted. Coletamos o mínimo para que um agendamento funcione, e nada além disso. Não usamos cookies de rastreamento, não há analytics de terceiros e nada é enviado para redes de publicidade.',
        'sections' => [
            [
                'h' => 'Duas relações diferentes',
                'p' => 'Aqui convivem dois tipos de pessoas: o negócio, que cria uma conta para receber agendamentos, e quem agenda um horário na página desse negócio, sem criar conta nenhuma. Quem opera esta instância trata os dados dos dois para que o serviço funcione. Já o negócio é o responsável perante o próprio cliente: é ele que decide o que fazer com esses dados depois do agendamento. Se você agendou um horário e quer que seus dados sejam corrigidos ou apagados, pode pedir ao negócio ou a quem opera esta instância; os dois podem fazer isso.',
            ],
            [
                'h' => 'O que guardamos da conta do negócio',
                'p' => 'Nome, e-mail e uma versão criptografada (hash) da senha. Do negócio guardamos também o que ele publica na sua página: nome, categoria, cidade, endereço, telefone de WhatsApp, descrição, cor, logo e fuso horário, além dos seus serviços e dos nomes das pessoas da equipe. Se você entrar com o Nexo ID, guardamos ainda o identificador que esse serviço nos dá para reconhecer você.',
            ],
            [
                'h' => 'O que guardamos de quem agenda um horário',
                'p' => 'O nome que você escreve ao agendar, um e-mail ou telefone para confirmarmos e avisarmos você, a observação que quiser deixar, e o idioma em que você agendou — para escrevermos nesse idioma e não em outro. É com isso que o horário é montado: serviço, profissional, dia e hora. Não criamos conta nem pedimos senha. Esses dados ficam visíveis para o negócio onde você agendou — é ele que precisa saber quem vai atender — e aparecem na agenda dele, na sua lista de clientes e nos arquivos CSV que ele baixar.',
            ],
            [
                'h' => 'O link para gerenciar seu horário',
                'p' => 'Ao agendar, você recebe um link só seu para ver, cancelar ou remarcar o horário sem ter conta. Esse link é a chave: no banco de dados guardamos apenas a impressão dele (hash), nunca o link em si, então nem alguém com acesso ao banco conseguiria reconstruí-lo. Quem tiver o link pode gerenciar aquele horário, por isso é melhor não compartilhá-lo.',
            ],
            [
                'h' => 'Lista de espera e lembretes',
                'p' => 'Se você entrar na lista de espera de um dia, guardamos seu nome e seu e-mail para avisar caso vague um horário. O lembrete do agendamento (24 horas antes) usa o mesmo contato que você deixou ao agendar. Nada disso é usado para enviar publicidade.',
            ],
            [
                'h' => 'Avaliações',
                'p' => 'Se o seu horário foi marcado como atendido, você pode deixar uma nota e um comentário. Eles são publicados na página do negócio junto ao nome com que você agendou, e o negócio pode ocultá-los.',
            ],
            [
                'h' => 'Métricas sem cookies',
                'p' => 'Contamos quantas pessoas diferentes viram a página de um negócio com uma impressão calculada a partir da data do dia e depois descartada: não guardamos seu IP nem seu navegador, e a impressão de hoje não pode ser comparada com a de amanhã. Não sabemos quem você é nem conseguimos seguir você entre sites.',
            ],
            [
                'h' => 'Contador do ecossistema',
                'p' => 'Se esta instância ativar esse recurso, as páginas do painel do negócio enviam uma visita anônima (qual ferramenta e qual rota, sem dados pessoais) para o hub do Nexo. Nunca é enviada a partir da página pública de um negócio, e o sinal "Do Not Track" do navegador é respeitado.',
            ],
            [
                'h' => 'Cookies',
                'p' => 'Apenas os necessários para o site funcionar: o de sessão (para manter identificado o negócio que fez login) e os que lembram o idioma e o tema claro ou escuro que você escolheu. Nenhum serve para publicidade ou rastreamento.',
            ],
            [
                'h' => 'E-mails',
                'p' => 'As confirmações, os lembretes, os avisos de cancelamento ou remarcação e os e-mails da conta são enviados por um provedor de e-mail externo, que necessariamente processa o endereço de destino e o conteúdo da mensagem para poder entregá-la.',
            ],
            [
                'h' => 'A agenda do profissional no calendário dele',
                'p' => 'Cada profissional pode assinar a própria agenda em um calendário externo através de um link secreto. Esse calendário inclui o nome de quem agendou e o serviço, então esse link não deveria ser compartilhado; o negócio pode gerá-lo de novo quando quiser e o anterior deixa de funcionar.',
            ],
            [
                'h' => 'Formulário de contato',
                'p' => 'Se você nos escrever pelo formulário de contato, guardamos a mensagem, o tipo de consulta, a página de onde você escreveu e seu e-mail, se deixar, para podermos responder.',
            ],
            [
                'h' => 'Por quanto tempo',
                'p' => 'Os agendamentos, as avaliações e as estatísticas ficam guardados enquanto o negócio mantiver a conta: são o histórico dele. Não há exclusão automática por tempo. Quando uma conta é excluída, o negócio, seus serviços, sua equipe, seus agendamentos, sua lista de espera, suas avaliações e suas métricas são excluídos junto.',
            ],
            [
                'h' => 'Seus direitos',
                'p' => 'Você pode pedir acesso aos seus dados, sua correção ou sua exclusão. Se tiver conta, escreva para quem opera esta instância (o contato está abaixo e na página de ajuda): hoje a exclusão de uma conta é feita a pedido, não existe um botão para isso. Se você agendou um horário, pode cancelá-lo pelo seu link e pedir ao negócio que apague seus dados da lista dele.',
            ],
            [
                'h' => 'Outras instâncias',
                'p' => 'O Nexo Agenda pode ser instalado em qualquer servidor. Cada instalação é independente e responsável pelos próprios dados: esta política fala apenas desta instância.',
            ],
        ],
    ],

    'terms' => [
        'title' => 'Termos de uso',
        'intro' => 'Ao usar esta instância do Nexo Agenda você aceita o que segue. É um serviço gratuito, oferecido como está.',
        'sections' => [
            [
                'h' => 'O que é o serviço',
                'p' => 'Uma ferramenta para um negócio publicar sua página de agendamentos, cadastrar seus serviços, sua equipe e seus horários, e receber agendamentos online com confirmação e lembrete por e-mail. Não processamos pagamentos, não cobramos sinal nem comissões e não fazemos parte do acordo entre o negócio e o cliente dele.',
            ],
            [
                'h' => 'A conta do negócio',
                'p' => 'Você precisa de uma conta para publicar uma página e receber agendamentos. Você é responsável pelo que acontecer com a sua conta e por manter a senha em segurança. Agendar um horário, por outro lado, não exige conta.',
            ],
            [
                'h' => 'O negócio responde pelos dados dos clientes dele',
                'p' => 'Os dados de quem agenda são coletados para que o negócio possa prestar o serviço, e o negócio é o responsável perante essa pessoa: deve usá-los só para atender o horário, não repassá-los nem usá-los para publicidade sem permissão, atender aos pedidos de acesso ou exclusão e cumprir a legislação que se aplica a ele. O mesmo vale para o que ele exportar em CSV ou assinar no calendário: uma vez baixado, esse arquivo fica sob o controle dele.',
            ],
            [
                'h' => 'O horário é entre o negócio e o cliente dele',
                'p' => 'A prestação do serviço, o preço, a pontualidade, a política de cancelamento e qualquer reclamação são assuntos entre o negócio e quem agendou. O Nexo Agenda só fornece a agenda.',
            ],
            [
                'h' => 'Uso indevido',
                'p' => 'Não é permitido publicar negócios falsos ou enganosos, se passar por terceiros, subir conteúdo ilegal, agendar horários em nome de outras pessoas nem usar a ferramenta para coletar dados com fins alheios ao agendamento. Qualquer pessoa pode denunciar um negócio pelo formulário de contato, e quem opera esta instância pode tirá-lo do ar: a página dele deixa de ficar disponível e não é mais possível agendar horários.',
            ],
            [
                'h' => 'Disponibilidade',
                'p' => 'O serviço é oferecido sem garantia de disponibilidade. Fazemos o razoável para mantê-lo no ar, mas pode haver interrupções: um negócio não deveria depender só desta agenda para trabalhar.',
            ],
            [
                'h' => 'Limite de responsabilidade',
                'p' => 'Quem opera esta instância não se responsabiliza por danos decorrentes do uso do serviço, incluindo horários que não sejam cumpridos, avisos que não cheguem ou perdas de dados.',
            ],
            [
                'h' => 'Software livre',
                'p' => 'O Nexo Agenda é distribuído sob a licença MIT: você pode ler o código, modificá-lo e hospedar sua própria instância. O software é entregue sem garantias, como diz essa licença.',
            ],
            [
                'h' => 'Mudanças',
                'p' => 'Estes termos podem mudar. A data acima indica a última atualização.',
            ],
        ],
    ],
];
