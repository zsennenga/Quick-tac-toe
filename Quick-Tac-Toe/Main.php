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

//Initialize Classes
$bot = new DoozyBot();
$gameState = new GameState();

//Display a blank board and user input instructions
echo INPUT_INSTRUCTIONS;
$gameState->printState();

$input = "";

//-1 is always an invalid move so we use that as a guard value for "exit the program"
while($input !== '-1')	{
	
	$nextPiece = $gameState->getNextPlayer();
	
	switch($nextPiece)	{
		case PLAYER_PIECE:
			$input = parseCliInput();
			//If we get -1 for the input we simply do nothing
			$gameState->makeMove($input, PLAYER_PIECE);
			break;
		case BOT_PIECE:
			$botMove = $bot->getMove($playerMove, $gameState);
			$gameState->makeMove($botMove, BOT_PIECE);
			break;
		default:
			//This gets called when there is no need for another move
			//Usually because the game is over
			$winner = $gameState->getWinner();
			$gameState->reset();
			//TODO use winner to print some sort of message
			break;
	}
	
	$gameState->printState();
}

echo "Thanks for playing!";
return 0;