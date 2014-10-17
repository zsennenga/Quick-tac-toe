<?php
/**
 * This holds the state of the game, including current player, and position of the pieces.
 * 
 * It manages the rules of the game, checks for the game end state, and can print the board.
 * 
 * @author Zachary
 *
 */
class GameState	{
	
	private $board;
	private $currentPlayer;
	private $turnNumber;
	private $winner;
	private $pieces;
	private $victoryPairs;
	
	/**
	 * Builds the initial gamestate. The pieces are the only values that don't change between games.
	 */
	function GameState($victoryPairs)	{
		$this->pieces = array("X", "O");
		$this->victoryPairs = $victoryPairs;
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
		
		//CHEATER! The board already has a piece where you're trying to place yours.
		if ($this->board[$move] !== -1)	{
			throw new Exception("Cheater!");
		}
		
		$this->board[$move] = $this->currentPlayer;
		
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
		
		//victoryPairs contains, for each given space on the board, the set of pairs of spaces 
		//that need to match to win the game.
		$victoryPairs = $this->victoryPairs[$lastMove];
		foreach($victoryPairs as $pair)	{
			if($this->checkWin($pair))
				return;
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
	private function checkWin($positions)	{
		$first = $this->board[$positions[0]];
		$second = $this->board[$positions[1]];
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
		$part1 = $this->prettyUp(array_slice($this->board, 0, 3));
		$part2 = $this->prettyUp(array_slice($this->board, 3, 3));
		$part3 = $this->prettyUp(array_slice($this->board, 6, 3));
		echo "\t1\t2\t3\n";
		echo "a\t" . $part1 . "\n";
		echo "b\t" . $part2 . "\n";
		echo "c\t" . $part3 . "\n\n";
	}
	/**
	 * Replaces player ids with Xs and Os
	 * @param unknown $array
	 * @return unknown
	 */
	private function prettyUp($array)	{
		$line = implode("\t", $array);
		$line = str_replace("-1", " ", $line);
		$line = str_replace("0", $this->pieces[0], $line);
		$line = str_replace("1", $this->pieces[1], $line);
		return $line;
	}
	
	/**
	 * Reset the gameState to what it would be in a new game.
	 */
	public function reset()	{
		$this->board = array_fill(0, 9, -1);
		$this->currentPlayer = $this->chooseStarter();
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
		if ($this->winner === null)	{
			return $this->currentPlayer;
		}
		return -1;
	}
	
	/**
	 * Simple getter for $winner
	 * @return integer
	 */
	public function getWinner()	{
		return $this->winner;
	}
	
}