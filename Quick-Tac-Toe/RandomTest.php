<?php
/**
 * This is a modification of the program to run a ton of iterations looking for gamestates where the bot loses
 */
require_once 'util.php';
require_once 'DoozyBot.php';
require_once 'GameState.php';

set_time_limit(0);
ini_set('max_execution_time', 0);

$playbook = json_decode(file_get_contents("victoryPairs.json"), true);
$playbook2 = json_decode(file_get_contents("victorySpaces.json"), true);
//Initialize Classes
$gameState = new GameState($playbook);
$first = $gameState->getNextPlayer() === BOT_ID;
$bot = new DoozyBot($first, $playbook, $playbook2);

$thisGame = array();
$games = 0;
$input = null;
while($games < 100000)	{
	
	$nextPiece = $gameState->getNextPlayer();
	
	switch($nextPiece)	{
		case PLAYER_ID:
			while($gameState->getNextPlayer() === PLAYER_ID)	{
				$input = mt_rand(0,8);
				
				try {
					$gameState->makeMove($input);
				}
				catch (Exception $e)	{
				}
			}
			array_push($thisGame, $input);
			
			break;
		case BOT_ID:
			$botMove = $bot->getMove($input, $gameState);
			try	{
				$gameState->makeMove($botMove);
			}
			catch (Exception $e)	{
				echo "Cheat bot??";
			}
			break;
		default:
			//This gets called when there is no need for another move
			//Usually because the game is over
			//"Usually" here meaning, I have no idea how this'd get called otherwise.
			$winner = $gameState->getWinner();
			switch($winner)	{
				case BOT_ID:
					break;
				case PLAYER_ID:
					echo "The bot lost by going ";
					if ($first)	{
						echo "first with: ";
					}
					else	{
						echo "second with: ";
					}
					echo print_r($thisGame, true) . "\n";
					break;
				default:
					break;
			}
			$thisGame = array();
			$gameState->reset();
			$first = $gameState->getNextPlayer() === BOT_ID;
			$bot->reset($first);
			$games++;
			gc_collect_cycles();
			$input = null;
			break;
	}
	
}

echo THANKS_MESSAGE;
return 0;