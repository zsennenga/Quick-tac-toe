<?php

class GameState	{
	
	private $board;
	private $currentPlayer;
	private $turnNumber;
	private $winner;
	private $pieces;
	
	/**
	 * Builds the initial gamestate. The pieces are the only values that don't change between games.
	 */
	function GameState()	{
		$this->pieces = array("X", "O");
		$this->reset();
	}
	
	/**
	 * Quick function to choose who starts.
	 * 
	 * I seperated this out into a function in case I need to cheat who goes first
	 * without mucking with fixing the seed or whatnot.
	 * 
	 */
	private function chooseStarter()	{
		return rand(0,1);
	}
	
	/**
	 * Make
	 * @param integer -1 to 8 $move
	 * @throws Exception for CHEATING
	 */
	public function makeMove($move)		{
		
		//Quick bounds check.
		if ($move === -1)	{
			return;
		}
		//CHEATER! The board already has a piece where you're trying to place yours.
		if ($this->board[$move] !== -1)	{
			throw new Exception("Cheater!");
		}
		
		$this->board[$move] = $currentPlayer;
		
		$this->turnNumber++;
		$this->checkForWinner($move);
		$this->setNextPlayer();
	}
	
	/**
	 * Alternates between 0 and 1.
	 */
	private function setNextPlayer()	{
		$this->currentPlayer = ($this->currentPlayer + 1) % 2;
	}
	
	/**
	 * Check the board for a winner. Only searches from the most recent move.
	 * 
	 * @param integer from 0-8; $lastMove
	 */
	private function checkForWinner($lastMove)	{
		//Quick check to skip checking boardstates without possible winners.
		if ($this->turnNumber <= 4)	{
			return;
		}
		
		//Another quick check. Draw if the board is full.
		if ($this->turnNumber === 9)	{
			$this->winner = -1;
			return;
		}
		

		//We don't check for a win on 4 because it will never come up due to how the bot AI is written.
		//Specifically, spot 4 is taken the bot's first turn if it hasn't already been taken, (so either turn 1 or 2) and this case 
		//is caught by the first quick check
		
		//The or-ing is because it'll short circuit if it finds a win (probably). 
		
		switch($lastMove)	{
			case 0:
				$this->checkWin(1,2);
				$this->checkWin(4,8);
				$this->checkWin(3,6);
				break;
			case 1:
				$this->checkWin(0,2);
				$this->checkWin(4,7);
				break;
			case 2:
				$this->checkWin(1,0);
				$this->checkWin(4,6);
				$this->checkWin(5,8);
				break;
			case 3:
				$this->checkWin(0,6);
				$this->checkWin(4,5);
				break;
			case 5:
				$this->checkWin(2,8);
				$this->checkWin(3,4);
				break;
			case 6:
				$this->checkWin(7,8);
				$this->checkWin(0,3);
				$this->checkWin(2,4);
				break;
			case 7:
				$this->checkWin(1,4);
				$this->checkWin(6,8);
				break;
			case 8:
				$this->checkWin(2,5);
				$this->checkWin(6,7);
				$this->checkWin(4,8);
				break;
		}
		
		return;
	}
	
	/**
	 * Checks if a specific "way" on the board is a win.
	 * 
	 * @param integer 0-8 $first
	 * @param integer 0-8 $second
	 * @return boolean
	 */
	private function checkWin($first, $second)	{
		$first = $this->board[$first];
		$second = $this->board[$second];
		if ($first === $second && $second === $this->currentPlayer)	{
			$this->winner = $this->currentPlayer;
			return true;
		}
		return false;
	}
	
	/**
	 * A huge pain. Prints the board to the console. There is PROBABLY a better way to do this using ncurses or something.
	 */
	public function printState()	{
		//TODO =(
	}
	
	/**
	 * Reset the gameState to what it would be in a new game.
	 */
	public function reset()	{
		$this->board = array_fill(0, 8, -1);
		$this->currentPlayer = $this->chooseRandomStarter();
		$this->winner = null;
		$this->turnNumber = 0;
	}
	
	/**
	 * Controls the execution path of main. Gives the next player, if there is one.
	 * 
	 * Returns the id of one of the players if there is another move possible.
	 * Returns null if the game is over.
	 * 
	 * @return 0, 1, or null 
	 */
	public function getNextPlayer()	{
		if ($winner === null)	{
			return $this->$currentPlayer;
		}
		return null;
	}
	
	/**
	 * Simple getter for $winner
	 * @return integer
	 */
	public function getWinner()	{
		return $winner;
	}
	
	
	
	
}