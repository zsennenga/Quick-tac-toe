<?php

require_once 'util.php';
require_once 'DoozyBot.php';
require_once 'GameState.php';

//Terrible things will happen outside the CLI
if (php_sapi_name() !== 'cli')	{
	echo "Please run this from the command line.";
	echo USAGE_STRING;
	return 0;
}

//The playbook contains, for each given space on the board, the other two spaces
//that need to match to win the game.
$playbook = json_decode(file_get_contents("victoryPairs.json"), true);
//Playbook2 contains, for each pair of adjacent spaces on the board, the third space
//that is necessary to win the game
$playbook2 = json_decode(file_get_contents("victorySpaces.json"), true);
//Initialize Classes
$gameState = new GameState($playbook);
$first = $gameState->getNextPlayer() === BOT_ID;
$bot = new DoozyBot($first, $playbook, $playbook2);

//Display a blank board and user input instructions
echo INPUT_INSTRUCTIONS;
$gameState->printState();

$input = null;

//-1 is always an invalid move so we use that as a guard value for "exit the program"
while($input !== '-1')	{
	
	$nextPiece = $gameState->getNextPlayer();
	
	switch($nextPiece)	{
		case PLAYER_ID:
			$input = parseCliInput();
			//If we get -1 for the input we simply do nothing
			try {
				$gameState->makeMove($input);
			}
			catch (Exception $e)	{
				echo "There's already a piece in that position. Try again.";
				return
			}
			break;
		case BOT_ID:
			$botMove = $bot->getMove($input, $gameState);
			$gameState->makeMove($botMove);
			$gameState->printState();
			break;
		default:
			//This gets called when there is no need for another move
			//Usually because the game is over
			$winner = $gameState->getWinner();
			$gameState->printState();
			//TODO State the winner somehow
			echo "Hey the game is over!\n\n\n\n\n\n\n\n\n";
			$gameState->reset();
			$first = $gameState->getNextPlayer() === BOT_ID;
			$bot->reset($first);
			break;
	}
	
}

echo "Thanks for playing!";
return 0;