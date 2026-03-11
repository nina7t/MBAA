<?php

$monTape = $_GET['nom'];

// on cherche les artistes qui commencent parce qui est tapé

$sql = "SELECT nom FROM artistes WHERE nom LIKE :recherche LIMIT 5";
$stmt = $pdo->prepapre($sql);
$stmt->execute(['recherche' => $nomTape . '%']); // Le %à la fin = "commence par" 

$resultats = $stmt->fetchAll(PDO::FETCH_ASSOC);

// on renvoie le résultat au javascript au format Json

echo json_encode($resultats);



