<?php
/**
 * Conteúdo padrão do site (usado pelo importador e como reserva).
 * Os textos podem ser editados depois pelo painel do WordPress.
 */

defined( 'ABSPATH' ) || exit;

function wg_option_defaults() {
	return array(
		'company_name'      => 'WG Soluções Audiovisuais',
		'whatsapp_message'  => 'Olá! Gostaria de solicitar um orçamento para infraestrutura audiovisual de um evento corporativo.',
		'hero_eyebrow'      => 'Soluções audiovisuais para eventos corporativos',
		'hero_title'        => 'Seu evento merece uma estrutura à altura da sua experiência.',
		'hero_text'         => 'Soluções audiovisuais completas para eventos corporativos, com tecnologia, planejamento e execução profissional.',
		'hero_video'        => '1',
		'value_title'       => 'Transformamos eventos em experiências através de soluções audiovisuais completas.',
		'value_text'        => 'Da concepção à execução, entregamos tecnologia, estrutura e profissionais especializados para que seu evento aconteça com excelência.',
		'cta_title'         => 'Vamos construir a estrutura do seu próximo evento?',
		'cta_text'          => 'Conte para nossa equipe o que você está planejando. Vamos entender suas necessidades e desenvolver uma proposta sob medida.',
		'stats'             => "Anos de experiência | \nEventos realizados | \nClientes atendidos | \nProjetos executados | \nRegiões atendidas | ",
		'differentials'     => implode( "\n", wg_default_differentials() ),
		'faq'               => implode( "\n", array_map( function ( $q ) { return $q[0] . ' | ' . $q[1]; }, wg_default_faq() ) ),
		'lead_email'        => '',
		'redirects'         => '',
	);
}

function wg_default_differentials() {
	return array(
		'headset | Atendimento consultivo | Entendemos o objetivo do evento antes de indicar equipamentos.',
		'target | Projetos personalizados | Cada estrutura é dimensionada para o espaço, o público e o formato.',
		'som | Equipamentos profissionais | Soluções de áudio, vídeo e iluminação de padrão profissional.',
		'users | Equipe técnica especializada | Profissionais preparados para montar, operar e resolver.',
		'clipboard | Planejamento e organização | Cronograma técnico, checklists e logística definidos com antecedência.',
		'clock | Pontualidade | Montagem e testes concluídos antes da abertura das portas.',
		'truck | Montagem e desmontagem | Cuidamos da instalação e da retirada de toda a estrutura contratada.',
		'sliders | Operação durante o evento | Técnicos acompanhando cada momento da programação.',
		'cenografia | Soluções integradas | Som, luz, vídeo e estrutura em um único projeto e um único contato.',
	);
}

function wg_default_process() {
	return array(
		array( 'chat', 'Briefing', 'Entendemos o objetivo, o público, o espaço e a programação do seu evento.' ),
		array( 'clipboard', 'Planejamento', 'Dimensionamos a infraestrutura técnica necessária para cada ambiente.' ),
		array( 'file', 'Proposta', 'Apresentamos uma solução personalizada, clara e detalhada.' ),
		array( 'wrench', 'Montagem', 'Realizamos a instalação, a configuração e os testes técnicos.' ),
		array( 'headset', 'Operação', 'Acompanhamos a execução com técnicos dedicados durante todo o evento.' ),
		array( 'box', 'Desmontagem', 'Retiramos toda a estrutura contratada com organização e cuidado.' ),
	);
}

function wg_default_audiences() {
	return array(
		'Agências de eventos',
		'Produtoras de eventos',
		'Departamentos de marketing',
		'Recursos humanos',
		'Organizadores de congressos',
		'Convenções corporativas',
		'Feiras e exposições',
		'Hotéis e centros de convenções',
		'Lançamentos de produtos',
		'Eventos institucionais',
	);
}

function wg_default_faq() {
	return array(
		array(
			'Vocês fazem apenas a locação dos equipamentos?',
			'Não. Trabalhamos com soluções completas: participamos do planejamento, dimensionamos a estrutura, cuidamos da logística, da montagem, dos testes, da operação técnica durante o evento e da desmontagem.',
		),
		array(
			'Como é calculado o orçamento?',
			'O valor depende do tipo de evento, do número de participantes, do espaço, da quantidade de dias, dos horários e das soluções necessárias. Quanto mais informações você enviar no formulário, mais precisa será a proposta.',
		),
		array(
			'Com quanto tempo de antecedência devo solicitar?',
			'Recomendamos entrar em contato assim que tiver a data e o local definidos. Eventos de maior porte exigem visita técnica e planejamento antecipado, mas também avaliamos demandas com prazos mais curtos.',
		),
		array(
			'Vocês atendem agências e produtoras?',
			'Sim. Atuamos como parceiros técnicos de agências e produtoras, assumindo a responsabilidade pela infraestrutura audiovisual do evento.',
		),
		array(
			'É possível fazer uma visita técnica ao local?',
			'Sim. Para eventos que exigem dimensionamento mais detalhado, agendamos uma visita técnica para avaliar o espaço, a energia disponível e as condições de montagem.',
		),
		array(
			'Ainda não sei exatamente o que preciso. Podem me orientar?',
			'Claro. No formulário de orçamento, selecione “Ainda preciso de orientação” e conte o que está planejando. Nossa equipe indica a melhor solução para o seu formato de evento.',
		),
	);
}

/**
 * Serviços (soluções).
 */
function wg_default_services() {
	return array(
		array(
			'slug'     => 'sonorizacao-para-eventos',
			'title'    => 'Sonorização profissional',
			'icon'     => 'som',
			'stock'    => 'servico-sonorizacao',
			'excerpt'  => 'Sonorização profissional dimensionada para garantir clareza, qualidade e excelente distribuição sonora em todo o ambiente.',
			'headline' => 'Som claro em cada assento, do palco à última fileira.',
			'items'    => "Sistemas de sonorização profissional\nLine array\nCaixas acústicas e subwoofers\nMicrofones sem fio\nMicrofones de lapela e headset\nMesas digitais de áudio\nMonitoração de palco\nOperação técnica de áudio\nSonorização para palestras, plenárias e grandes eventos",
			'benefits' => "Inteligibilidade | Projetos pensados para que cada palavra seja ouvida com clareza.\nCobertura uniforme | Distribuição sonora equilibrada em todo o ambiente.\nOperação dedicada | Técnico de áudio acompanhando a programação do início ao fim.",
			'seo_title'=> 'Sonorização para eventos corporativos',
			'seo_desc' => 'Sonorização profissional para eventos corporativos, congressos e convenções: line array, microfones sem fio, mesas digitais e operação técnica. Solicite um orçamento.',
			'content'  => '<h2>Sonorização para eventos corporativos</h2><p>Em um evento corporativo, o áudio é o que garante que a mensagem chegue a todos. Uma palestra inaudível, uma microfonia no meio de uma apresentação ou um vídeo sem som comprometem a experiência e a imagem de quem organiza.</p><p>Por isso, cada projeto de sonorização começa pelo entendimento do espaço, do público e da programação. A partir disso, dimensionamos o sistema, a quantidade e o tipo de microfones, a monitoração e a operação técnica necessárias.</p><h2>Onde aplicamos</h2><p>Plenárias, congressos, convenções de vendas, treinamentos, lançamentos de produtos, premiações e eventos com apresentações musicais. Atendemos desde salas de reunião até grandes auditórios e espaços abertos.</p><h2>Como trabalhamos</h2><p>Planejamento técnico, montagem, passagem de som, testes com os palestrantes e operação durante todo o evento, com profissionais dedicados ao áudio.</p>',
		),
		array(
			'slug'     => 'iluminacao-para-eventos',
			'title'    => 'Iluminação cênica',
			'icon'     => 'luz',
			'stock'    => 'servico-iluminacao',
			'excerpt'  => 'Projetos de iluminação cênica que valorizam o palco, destacam apresentações e criam a atmosfera ideal para cada momento.',
			'headline' => 'A luz certa para cada momento da sua programação.',
			'items'    => "Moving heads\nRefletores LED\nIluminação de palco\nIluminação arquitetural\nIluminação decorativa\nIluminação para apresentações e captação de vídeo\nConsoles de iluminação\nEfeitos especiais",
			'benefits' => "Palco valorizado | Destaque para palestrantes, apresentações e marcas.\nAtmosfera sob medida | Cenas diferentes para abertura, conteúdo, premiação e festa.\nPronta para câmera | Iluminação adequada para transmissão e gravação.",
			'seo_title'=> 'Iluminação para eventos corporativos',
			'seo_desc' => 'Iluminação cênica para eventos corporativos: moving heads, refletores LED, iluminação de palco, arquitetural e decorativa, com operação técnica. Peça seu orçamento.',
			'content'  => '<h2>Iluminação para eventos corporativos</h2><p>A iluminação define a percepção do público desde a entrada. Ela conduz a atenção para o palco, valoriza a marca, cria transições entre os momentos do evento e garante boas imagens para fotografia, gravação e transmissão.</p><p>Desenvolvemos projetos de iluminação cênica, arquitetural e decorativa de acordo com o espaço e com o roteiro do evento, programando cenas específicas para cada etapa da programação.</p><h2>Onde aplicamos</h2><p>Palcos de convenções e congressos, premiações, lançamentos de produtos, shows corporativos, jantares e confraternizações, além da ambientação de fachadas e áreas de recepção.</p><h2>Como trabalhamos</h2><p>Projeto de luz, montagem das estruturas, endereçamento e programação dos equipamentos, ensaio com o roteiro e operação durante o evento.</p>',
		),
		array(
			'slug'     => 'painel-de-led-para-eventos',
			'title'    => 'Painéis de LED',
			'icon'     => 'led',
			'stock'    => 'servico-painel-led',
			'excerpt'  => 'Painéis de LED profissionais para transformar seu palco em uma experiência visual de alto impacto.',
			'headline' => 'Imagens nítidas e brilhantes, em qualquer formato de palco.',
			'items'    => "Painéis de LED indoor\nPainéis de LED outdoor\nBackdrops em LED\nPainéis de alta resolução\nPainéis em diferentes dimensões\nPainéis para palco e para estandes\nProcessadoras e controle de vídeo\nOperação técnica",
			'benefits' => "Alto impacto visual | Conteúdo vivo e brilhante, mesmo em ambientes iluminados.\nFormatos flexíveis | Montagens retas, em recortes ou integradas à cenografia.\nControle profissional | Processamento e operação de vídeo dedicados.",
			'seo_title'=> 'Painel de LED para eventos',
			'seo_desc' => 'Locação e operação de painel de LED para eventos corporativos: indoor, outdoor, backdrops e telas de alta resolução para palcos e estandes. Solicite um orçamento.',
			'content'  => '<h2>Painel de LED para eventos</h2><p>O painel de LED é hoje o principal elemento visual de um palco corporativo. Ele exibe apresentações, vídeos, transmissões ao vivo e conteúdos de marca com brilho e nitidez, mesmo em ambientes com bastante luz.</p><p>Indicamos a resolução, o tamanho e o formato ideais de acordo com a distância do público, o tamanho do espaço e o conteúdo que será exibido, sempre com processamento e operação técnica.</p><h2>Onde aplicamos</h2><p>Palcos de convenções, congressos e premiações, backdrops, estandes em feiras, lançamentos de produtos e eventos ao ar livre.</p><h2>Como trabalhamos</h2><p>Dimensionamento técnico, estrutura de sustentação, montagem, configuração das processadoras, testes de conteúdo e operação durante o evento.</p>',
		),
		array(
			'slug'     => 'projecao-e-telas',
			'title'    => 'Projeção e telas',
			'icon'     => 'projecao',
			'stock'    => 'servico-projecao',
			'excerpt'  => 'Projetores, telões, TVs e video walls para que cada conteúdo seja visto com clareza por todo o público.',
			'headline' => 'Conteúdo visível para todos, em qualquer ponto da sala.',
			'items'    => "Projetores profissionais\nProjetores laser\nTelões\nTVs profissionais\nVideo wall\nMonitores de retorno para palco\nProjeção mapeada\nDistribuição de sinal de vídeo",
			'benefits' => "Boa leitura | Telas dimensionadas para a distância e o ângulo do público.\nRetorno para palestrantes | Monitores de palco com slides e cronômetro.\nSinal estável | Distribuição de vídeo profissional entre salas e telas.",
			'seo_title'=> 'Projeção e telas para eventos',
			'seo_desc' => 'Projetores profissionais e laser, telões, TVs, video wall e projeção mapeada para eventos corporativos, congressos e treinamentos. Solicite um orçamento.',
			'content'  => '<h2>Projeção e telas para eventos</h2><p>Apresentações, vídeos institucionais e transmissões precisam ser vistos com clareza por todos os participantes. Escolhemos a solução de projeção ou de telas mais adequada ao espaço, à iluminação do ambiente e ao número de pessoas.</p><h2>Onde aplicamos</h2><p>Auditórios, salas de treinamento, plenárias de congressos, salas simultâneas, estandes, recepções e ambientes com projeção mapeada em fachadas ou cenários.</p><h2>Como trabalhamos</h2><p>Dimensionamento das telas, posicionamento dos equipamentos, distribuição de sinal, testes de conteúdo e acompanhamento técnico durante o evento.</p>',
		),
		array(
			'slug'     => 'palcos-e-estruturas',
			'title'    => 'Palcos e estruturas',
			'icon'     => 'palco',
			'stock'    => 'servico-palcos',
			'excerpt'  => 'Palcos, praticáveis, box truss e estruturas de alumínio com montagem segura e acabamento profissional.',
			'headline' => 'A base segura para todo o seu projeto audiovisual.',
			'items'    => "Palcos profissionais\nPraticáveis\nBox truss\nEstruturas de alumínio e treliças\nBackdrops\nEstruturas para painéis de LED\nPassarelas\nCoberturas e suportes técnicos",
			'benefits' => "Segurança | Estruturas dimensionadas para cargas de som, luz e LED.\nAcabamento | Palcos e backdrops com aparência profissional.\nIntegração | Estrutura pensada junto com o projeto audiovisual.",
			'seo_title'=> 'Palcos e estruturas para eventos',
			'seo_desc' => 'Palcos, praticáveis, box truss, treliças, backdrops e estruturas para painéis de LED em eventos corporativos. Montagem segura e profissional. Peça seu orçamento.',
			'content'  => '<h2>Palcos e estruturas para eventos</h2><p>Toda a infraestrutura audiovisual depende de uma base segura. Palcos, praticáveis e estruturas de alumínio sustentam equipamentos de som, iluminação e painéis de LED, e também definem a aparência do espaço.</p><h2>Onde aplicamos</h2><p>Convenções, congressos, lançamentos, shows corporativos, feiras e eventos ao ar livre, em diferentes alturas, formatos e dimensões.</p><h2>Como trabalhamos</h2><p>Projeto da estrutura de acordo com o espaço e com as cargas previstas, logística, montagem, conferência e desmontagem.</p>',
		),
		array(
			'slug'     => 'streaming-para-eventos-corporativos',
			'title'    => 'Transmissão ao vivo e streaming',
			'icon'     => 'streaming',
			'stock'    => 'servico-streaming',
			'excerpt'  => 'Transmissões multicâmera e eventos híbridos para levar seu conteúdo a participantes de qualquer lugar.',
			'headline' => 'Seu evento ao vivo para quem está na sala e para quem está longe.',
			'items'    => "Transmissão multicâmera\nCaptação de áudio e vídeo\nDireção de corte\nStreaming para YouTube\nStreaming para LinkedIn\nTransmissões privadas\nEventos híbridos\nGravação de eventos\nOperação técnica de transmissão",
			'benefits' => "Alcance ampliado | Participantes remotos com a mesma qualidade de conteúdo.\nDireção profissional | Cortes entre câmeras, slides e vídeos em tempo real.\nConteúdo gravado | O evento registrado para uso posterior.",
			'seo_title'=> 'Streaming para eventos corporativos',
			'seo_desc' => 'Transmissão ao vivo e streaming para eventos corporativos: multicâmera, direção de corte, eventos híbridos, YouTube, LinkedIn e plataformas privadas. Solicite um orçamento.',
			'content'  => '<h2>Streaming para eventos corporativos</h2><p>A transmissão ao vivo amplia o alcance do seu evento, permitindo a participação de equipes, clientes e parceiros de outras cidades. Em eventos híbridos, a experiência de quem está a distância precisa ser tão cuidada quanto a de quem está no local.</p><h2>Onde aplicamos</h2><p>Convenções nacionais, treinamentos, lançamentos, congressos com palestrantes remotos, assembleias, coletivas e eventos institucionais.</p><h2>Como trabalhamos</h2><p>Planejamento de câmeras e conectividade, captação de áudio e vídeo, direção de corte, testes de transmissão, operação durante o evento e entrega da gravação.</p>',
		),
		array(
			'slug'     => 'cenografia-audiovisual',
			'title'    => 'Cenografia audiovisual',
			'icon'     => 'cenografia',
			'stock'    => 'servico-cenografia',
			'excerpt'  => 'Cenários corporativos que integram estrutura, tecnologia e identidade visual em uma única experiência.',
			'headline' => 'Cenários que traduzem a sua marca no palco.',
			'items'    => "Cenários corporativos\nPalcos personalizados\nBackdrops\nPainéis integrados à cenografia\nEstruturas personalizadas\nCenografia tecnológica\nAmbientação de palco",
			'benefits' => "Identidade de marca | Cenário alinhado ao conceito e à comunicação do evento.\nTecnologia integrada | LED, luz e estrutura pensados como um só projeto.\nImpacto visual | Palcos memoráveis para o público e para as câmeras.",
			'seo_title'=> 'Cenografia para eventos corporativos',
			'seo_desc' => 'Cenografia audiovisual para eventos corporativos: cenários, palcos personalizados, backdrops e painéis de LED integrados à identidade da sua marca.',
			'content'  => '<h2>Cenografia audiovisual para eventos</h2><p>A cenografia transforma o palco em uma extensão da marca. Quando estrutura, painéis de LED e iluminação são pensados em conjunto, o resultado é um ambiente coerente, bonito ao vivo e nas imagens.</p><h2>Onde aplicamos</h2><p>Convenções, premiações, lançamentos de produtos, congressos e eventos institucionais que precisam de um palco com identidade própria.</p><h2>Como trabalhamos</h2><p>Desenvolvimento do conceito com a agência ou o cliente, projeto técnico, produção, montagem e integração com os sistemas audiovisuais.</p>',
		),
		array(
			'slug'     => 'producao-audiovisual',
			'title'    => 'Produção audiovisual',
			'icon'     => 'camera',
			'stock'    => 'servico-producao',
			'excerpt'  => 'Captação de vídeo, fotografia e conteúdo para que o seu evento continue gerando resultado depois de acabar.',
			'headline' => 'O registro profissional do seu evento, pronto para comunicar.',
			'items'    => "Captação de vídeo\nFotografia de eventos\nFilmagem multicâmera\nEdição de vídeos\nVídeos institucionais\nConteúdo para redes sociais\nPodcast corporativo\nGravação de palestras",
			'benefits' => "Conteúdo reaproveitável | Vídeos e fotos para comunicação interna e redes sociais.\nVisão de evento | Equipe que entende o ritmo e os momentos-chave.\nEntrega organizada | Material editado nos formatos combinados.",
			'seo_title'=> 'Produção audiovisual para eventos',
			'seo_desc' => 'Produção audiovisual para eventos corporativos: filmagem multicâmera, fotografia, edição, vídeos institucionais, conteúdo para redes sociais e gravação de palestras.',
			'content'  => '<h2>Produção audiovisual para eventos</h2><p>Um evento bem registrado continua gerando valor: vídeos de resumo, cortes para redes sociais, palestras gravadas para treinamento e fotografias para a comunicação da empresa.</p><h2>Onde aplicamos</h2><p>Convenções, lançamentos, premiações, treinamentos, eventos institucionais e gravações de conteúdo corporativo, incluindo podcasts.</p><h2>Como trabalhamos</h2><p>Roteiro de captação alinhado à programação, equipe de filmagem e fotografia, edição e entrega nos formatos combinados.</p>',
		),
		array(
			'slug'     => 'infraestrutura-complementar',
			'title'    => 'Infraestrutura complementar',
			'icon'     => 'energia',
			'stock'    => 'servico-infraestrutura',
			'excerpt'  => 'Energia, distribuição elétrica, cabeamento e estruturas de apoio para que nada interrompa o seu evento.',
			'headline' => 'Tudo o que está por trás de um evento sem interrupções.',
			'items'    => "Geradores de energia\nDistribuição elétrica\nCabeamento\nTorres de iluminação\nEstruturas de suporte\nEquipamentos complementares",
			'benefits' => "Continuidade | Energia dimensionada para toda a operação audiovisual.\nOrganização | Cabeamento e distribuição feitos com segurança.\nUm só fornecedor | Infraestrutura integrada ao projeto técnico.",
			'seo_title'=> 'Infraestrutura para eventos corporativos',
			'seo_desc' => 'Infraestrutura complementar para eventos: geradores de energia, distribuição elétrica, cabeamento, torres de iluminação e estruturas de suporte.',
			'content'  => '<h2>Infraestrutura complementar para eventos</h2><p>Por trás de cada palco existe uma infraestrutura que o público não vê, mas que garante que tudo funcione: energia, distribuição elétrica, cabeamento e estruturas de apoio.</p><h2>Onde aplicamos</h2><p>Eventos ao ar livre, espaços sem energia suficiente, feiras, montagens de grande porte e locais que exigem estruturas de suporte adicionais.</p><h2>Como trabalhamos</h2><p>Levantamento das necessidades elétricas e técnicas, dimensionamento, instalação, acompanhamento durante o evento e desmontagem.</p>',
		),
	);
}

/**
 * Tipos de evento. "page" = true cria página própria (indexável).
 */
function wg_default_events() {
	return array(
		array(
			'slug'     => 'convencoes-corporativas',
			'title'    => 'Convenções corporativas',
			'stock'    => 'evento-convencoes-corporativas',
			'excerpt'  => 'Palco, som, LED e transmissão para alinhar e engajar equipes inteiras.',
			'page'     => true,
			'services' => array( 'sonorizacao-para-eventos', 'painel-de-led-para-eventos', 'iluminacao-para-eventos', 'streaming-para-eventos-corporativos' ),
			'content'  => '<p>Convenções reúnem lideranças e equipes para apresentar resultados, estratégias e reconhecimentos. A infraestrutura audiovisual precisa sustentar uma programação longa, com muitos palestrantes, vídeos, premiações e, muitas vezes, transmissão para quem não está presente.</p><h2>O que costuma fazer parte</h2><p>Palco com painel de LED, sonorização para plenária, microfones sem fio, iluminação cênica, monitores de retorno, captação de vídeo e transmissão ao vivo.</p>',
		),
		array(
			'slug'     => 'congressos',
			'title'    => 'Congressos e simpósios',
			'stock'    => 'evento-congressos',
			'excerpt'  => 'Plenárias e salas simultâneas com áudio claro e projeção de qualidade.',
			'page'     => true,
			'services' => array( 'sonorizacao-para-eventos', 'projecao-e-telas', 'painel-de-led-para-eventos', 'streaming-para-eventos-corporativos' ),
			'content'  => '<p>Congressos e simpósios combinam plenárias com grande público e várias salas simultâneas. Cada ambiente precisa de áudio claro, boa projeção e operação técnica para manter a grade de horários.</p><h2>O que costuma fazer parte</h2><p>Sonorização de plenária e salas, projeção ou LED, monitores de retorno, sistemas de microfones para mesas, gravação de palestras e transmissão para participantes remotos.</p>',
		),
		array(
			'slug'     => 'feiras-e-exposicoes',
			'title'    => 'Feiras e exposições',
			'stock'    => 'evento-feiras',
			'excerpt'  => 'Estandes e palcos de conteúdo que chamam a atenção no meio do pavilhão.',
			'page'     => true,
			'services' => array( 'painel-de-led-para-eventos', 'projecao-e-telas', 'sonorizacao-para-eventos', 'palcos-e-estruturas' ),
			'content'  => '<p>Em uma feira, o estande disputa a atenção do visitante com dezenas de outros. Painéis de LED, telas, som direcionado e iluminação ajudam a destacar a marca e a apresentar produtos.</p><h2>O que costuma fazer parte</h2><p>Painéis de LED para estandes, TVs e video walls, sonorização para palcos de conteúdo, iluminação e estruturas de suporte.</p>',
		),
		array(
			'slug'     => 'lancamentos-de-produtos',
			'title'    => 'Lançamentos de produtos',
			'stock'    => 'evento-lancamentos',
			'excerpt'  => 'Revelações marcantes com luz, vídeo e cenografia sincronizados.',
			'page'     => true,
			'services' => array( 'painel-de-led-para-eventos', 'iluminacao-para-eventos', 'cenografia-audiovisual', 'producao-audiovisual' ),
			'content'  => '<p>Um lançamento é o momento de apresentar algo novo ao mercado. A experiência precisa ser marcante para o público presente e gerar boas imagens para a imprensa e para as redes sociais.</p><h2>O que costuma fazer parte</h2><p>Painéis de LED, iluminação cênica com efeitos, cenografia personalizada, sonorização, captação de vídeo e fotografia.</p>',
		),
		array(
			'slug'     => 'premiacoes',
			'title'    => 'Premiações empresariais',
			'stock'    => 'evento-premiacoes',
			'excerpt'  => 'Cerimônias com clima de celebração e momentos de reconhecimento.',
			'page'     => false,
			'services' => array(),
			'content'  => '',
		),
		array(
			'slug'     => 'convencoes-de-vendas',
			'title'    => 'Convenções de vendas',
			'stock'    => 'evento-convencoes-de-vendas',
			'excerpt'  => 'Energia de palco para metas, reconhecimentos e campanhas.',
			'page'     => false,
			'services' => array(),
			'content'  => '',
		),
		array(
			'slug'     => 'treinamentos',
			'title'    => 'Treinamentos corporativos',
			'stock'    => 'evento-treinamentos',
			'excerpt'  => 'Salas equipadas para que o conteúdo seja visto e ouvido por todos.',
			'page'     => false,
			'services' => array(),
			'content'  => '',
		),
		array(
			'slug'     => 'palestras-e-workshops',
			'title'    => 'Palestras e workshops',
			'stock'    => 'evento-palestras',
			'excerpt'  => 'Áudio, projeção e gravação para conteúdos de qualquer porte.',
			'page'     => false,
			'services' => array(),
			'content'  => '',
		),
		array(
			'slug'     => 'eventos-institucionais',
			'title'    => 'Eventos institucionais',
			'stock'    => 'evento-institucionais',
			'excerpt'  => 'Cerimônias, posses e anúncios com a formalidade que pedem.',
			'page'     => false,
			'services' => array(),
			'content'  => '',
		),
		array(
			'slug'     => 'confraternizacoes',
			'title'    => 'Confraternizações empresariais',
			'stock'    => 'evento-confraternizacoes',
			'excerpt'  => 'Ambientação, som e luz para celebrar com as equipes.',
			'page'     => false,
			'services' => array(),
			'content'  => '',
		),
		array(
			'slug'     => 'eventos-hibridos',
			'title'    => 'Eventos híbridos',
			'stock'    => 'evento-hibridos',
			'excerpt'  => 'Público presencial e remoto com a mesma qualidade de experiência.',
			'page'     => true,
			'services' => array( 'streaming-para-eventos-corporativos', 'producao-audiovisual', 'sonorizacao-para-eventos', 'iluminacao-para-eventos' ),
			'content'  => '<p>Eventos híbridos reúnem participantes no local e a distância. A captação de áudio e vídeo, a iluminação para câmera e a direção de corte são tão importantes quanto o palco.</p><h2>O que costuma fazer parte</h2><p>Transmissão multicâmera, direção de corte, iluminação adequada para vídeo, captação de áudio dedicada, integração com plataformas e gravação do evento.</p>',
		),
		array(
			'slug'     => 'shows-corporativos',
			'title'    => 'Shows e apresentações corporativas',
			'stock'    => 'evento-shows',
			'excerpt'  => 'Estrutura técnica completa para atrações e apresentações artísticas.',
			'page'     => false,
			'services' => array(),
			'content'  => '',
		),
	);
}

function wg_default_segments() {
	return array(
		'corporativo'     => 'Corporativo',
		'congressos'      => 'Congressos',
		'feiras'          => 'Feiras',
		'lancamentos'     => 'Lançamentos',
		'convencoes'      => 'Convenções',
		'eventos-hibridos'=> 'Eventos híbridos',
	);
}

/**
 * Opções do campo "Serviços desejados" do formulário.
 */
function wg_form_service_options() {
	return array(
		'Sonorização',
		'Iluminação',
		'Painel de LED',
		'Projeção',
		'Palco e estruturas',
		'Cenografia',
		'Streaming',
		'Filmagem',
		'Fotografia',
		'Geradores',
		'Projeto audiovisual completo',
		'Ainda preciso de orientação',
	);
}

/**
 * Mapeia o slug de um serviço para a opção do formulário.
 */
function wg_service_form_map() {
	return array(
		'sonorizacao-para-eventos'           => 'Sonorização',
		'iluminacao-para-eventos'            => 'Iluminação',
		'painel-de-led-para-eventos'         => 'Painel de LED',
		'projecao-e-telas'                   => 'Projeção',
		'palcos-e-estruturas'                => 'Palco e estruturas',
		'streaming-para-eventos-corporativos'=> 'Streaming',
		'cenografia-audiovisual'             => 'Cenografia',
		'producao-audiovisual'               => 'Filmagem',
		'infraestrutura-complementar'        => 'Geradores',
	);
}
