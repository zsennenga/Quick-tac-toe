<?php

require_once 'util.php';
require_once 'DoozyBot.php';
require_once 'GameState.php';

//Terrible things will happen outside the CLI
if (php_sapi_name() !== 'cli')	{
	echo CLI_CHASTISE;
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
if (!$first)	{
	$gameState->printState();
}

$input = null;

//-1 is always an invalid move so we use that as a guard value for "exit the program"
while($input !== -1)	{
	
	$nextPiece = $gameState->getNextPlayer();
	
	switch($nextPiece)	{
		case PLAYER_ID:
			$input = parseCliInput();
			if ($input === -1)	{
				break;
			}
			try {
				$gameState->makeMove($input);
			}
			catch (Exception $e)	{
				echo CHASTISE_MESSAGE;
				$gameState->printState();
			}
			break;
		case BOT_ID:
			$botMove = $bot->getMove($input, $gameState);
			$gameState->makeMove($botMove);
			echo BOT_MOVE;
			$gameState->printState();
			break;
		default:
			//This gets called when there is no need for another move
			//Usually because the game is over
			//"Usually" here meaning, I have no idea how this'd get called otherwise.
			$winner = $gameState->getWinner();
			switch($winner)	{
				case BOT_ID:
					echo BOT_WIN;
					break;
				case PLAYER_ID:
					echo PLAYER_WIN;
					break;
				default:
					echo DRAW_WIN;
					break;
			}
			$gameState->reset();
			$first = $gameState->getNextPlayer() === BOT_ID;
			$bot->reset($first);
			echo NEW_GAME;
			if (!$first)	{
				$gameState->printState();
			}
			break;
	}
	
}

echo THANKS_MESSAGE;
return 0;