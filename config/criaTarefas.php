<?php

$rows = 0;

// Primeira etapa
$etapaInicial = array(
    "ADMINISTRATIVO" => array(
        "Cadastro de projeto no ERP, com anexação de todos os documentos",
        "Realizar compra de materiais conforme a lista da engenharia",
        "Conferir o recebimento dos materiais e armazená-los",
    ),
    "ENGENHARIA" => array(
        "Elaborar diagramas elétricos e layout",
        "Solicitar e acompanhar o processo de aprovação",
        "Gerar a lista de materiais para compras"
    )
);

//Segunda etapa
$etapaProducao = array(
    "FABRICA" => array(
        "Conferir os materiais recebidos conforme a lista técnica",
        "Executar a montagem dos painéis",
        "Entregar o registro das alterações de layout e diagramas realizadas durante a montagem",
        "Atualizar os desvios nos diagramas e layout",
        "Liberar o painel para testes e programação"
    ),
    "ENGENHARIA" => array(
        "Acompanhar a montagem (suporte técnico)"
    )
);

//Terceira etapa
$etapaSoftware = array(
    "SOFTWARE" => array(
        "Desenvolver o programa do CLP",
        "Desenvolver as telas de IHM",
        "Executar os testes operacionais e validar o funcionamento do sistema",
        "Solicitar a inspeção final (quando aplicável)",
        "Liberar o projeto para entrega",
        "Armazenar na rede os arquivos de programação na versão conforme construída",
    ),
    "ENGENHARIA" => array(
        "Atualizar os diagramas e o layout conforme construído"
    )
);

//Quarta etapa
$etapaFinalizacao = array(
    "FABRICA" => array(
        "Elaborar o relatório fotográfico do painel",
        "Preparar o painel para trasporte",
    ),
    "ADMINISTRATIVO" => array(
        "Contratar o frete ou solicitar coleta",
        "Emitir Nota Fiscal conforme a operação",
        "Emitir a cobrança (Boleto / Faturamento)",
        "Comunicar a engenharia sobre a finalização do projeto"
    ),
    "ENGENHARIA" => array(
        "Conferir os arquivos finais do projeto",
        "Movimentar os arquivos para a pasta de projetos concluídos (por ano)",
        "Encerrar o projeto (registrar data, atrasos e melhorias)"
    )
);

//Cada etapa terá a seguinte estrutura:
// AREA{
//    TAREFAS  
//} 


// Função auxiliar para armazenamento de tarefas no banco
function criarTarefa($idProj, $etapa, $tarefa, $area){
    include "config/conexao.php";
    $sqlTarefas = "INSERT INTO tarefas (tarefa, etapa, id_proj, area) VALUES(:tarefa, :etapa, :id_proj, :area)";
    $stmtInsTarefas = $conexao->prepare($sqlTarefas);
    $stmtInsTarefas->bindParam(':id_proj', $idProj, PDO::PARAM_INT);
    $stmtInsTarefas->bindParam(':tarefa', $tarefa, PDO::PARAM_STR);
    $stmtInsTarefas->bindParam(':etapa', $etapa, PDO::PARAM_STR);
    $stmtInsTarefas->bindParam(':area', $area, PDO::PARAM_STR);   

    if ($stmtInsTarefas->execute()) {
        $rows = $stmtInsTarefas->rowCount();
    }
    echo $rows;
}


//Módulo de chamada da operação de inserção de tarefas no banco, utiliza dos arrays de tarefas e é chamada sempre que um projeto é criado
function insertTarefas($idProj, $tarefas, $etapa){
foreach ($tarefas as $area => $lista) {
    foreach ($lista as $tarefa) {
        criarTarefa($idProj, $etapa, $tarefa, $area);
    }
}
}

?>