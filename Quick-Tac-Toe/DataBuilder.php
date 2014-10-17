<?php
/**
 * Quick script to take the original "victoryPairs" playbook and build something else useful out of it.
 * 
 * VictorySpaces, given 2 spaces on the board, gives you the space that completes the row or diagonal.
 * 
 */
$playbook = json_decode(file_get_contents("playbook.json"), true);

$out = array();

foreach($playbook as $position => $pairs)	{
	foreach($pairs as $pair)	{
		$index = (string) $position . (string) $pair[0];
		$data = $pair[1];
		$out[$index] = $data;
		$index = (string) $position . (string) $pair[1];
		$data = $pair[0];
		$out[$index] = $data;
	}
}
echo json_encode($out);