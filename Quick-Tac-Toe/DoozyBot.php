<?php
class DoozyBot	{
	
	private $myMoves;
	private $oppMoves;
	private $first;
	
	private $victoryPairs;
	
	private $winMove;
	
	function DoozyBot($first, $playbook, $playbook2)	{
		//The playbook contains, for each given space on the board, the other two spaces 
		//that need to match to win the game.
		$this->victoryPairs = $playbook;
		$this->victorySpaces = $playbook2;
		$this->reset($first);
	}
	
	/**
	 * Top level function to get a move from the bot
	 * 
	 * This branches here depending on if you are the first player or not.
	 * 
	 * @param integer 0-8 $lastPlayerMove
	 * @return integer 0-8
	 */
	public function getMove($lastPlayerMove)	{
		if ($lastPlayerMove !== null)	{
			array_push($this->oppMoves, $lastPlayerMove);
		}
		if ($this->first)	{
			$move = $this->getMoveFirst($lastPlayerMove);
		}
		else	{
			$move = $this->getMoveSecond($lastPlayerMove);
		}
		
		array_push($this->myMoves, $move);
		return $move;
	}
	
	/**
	 * Move if the bot goes first. The bot is "aggressive" and tries to win rather than block.
	 * 
	 * @param unknown $lastPlayerMove
	 * @return number
	 */
	private function getMoveFirst($lastPlayerMove)	{
		switch(count($this->myMoves))	{
			case 0:
				return 4;
			case 1:
				return $this->findVictoryMove();
			case 2:
				return $this->tryToWin();
			default:
				return $this->randomMove();
		}
	}
	
	/**
	 * Move if the bot goes second. The bot blocks unless the human player doesn't try to win.
	 * @param unknown $lastPlayerMove
	 * @return number|unknown
	 */
	private function getMoveSecond($lastPlayerMove)	{
		switch(count($this->myMoves))	{
			case 0:
				if ($lastPlayerMove != 4)	{
					return 4;
				}
				else 	{
					return 0;
				}
			case 1:
				$move = $this->findBlockMove();
				if ($move === -1)	{
					$move = $this->findVictoryMove();
				}
				return $move;
			case 2:
				if ($this->winMove !== -1)	{
					return $this->tryToWin();
				}
				return $this->randomMove();
			default:
				return $this->randomMove();
		}
	}
	
	/**
	 * Tries to block the human player, if possible.
	 * @return integer 0-8
	 */
	private function findBlockMove()	{
		$first = $this->oppMoves[0];
		$second = $this->oppMoves[1];
		$key = (string) $first . (string) $second;
		$blockMove = $this->getVictorySpace($key);
		
		if ($blockMove === -1)	{
			return $this->findVictoryMove();
		}
		
		//Check if we're already blocking them.
		if (in_array($blockMove,$this->myMoves))	{
			return $this->findVictoryMove();
		}
		
		//Check if, by blocking, we are in a position to win
		$myMove = $this->myMoves[0];
		$key = (string) $myMove . (string) $blockMove;
		$possibleVictorySpace = $this->getVictorySpace($key);
		if ($possibleVictorySpace !== -1)	{
			$this->winMove = $possibleVictorySpace;
		}
		return $blockMove;
	}
	
	/**
	 * Checks if, given a key created from the indexes of two other moves,
	 * there is a third move which wins the game.
	 * @param string $key
	 * @return number
	 */
	private function getVictorySpace($key)	{
		if (isset($this->victorySpaces[$key]))	{
			return $this->victorySpaces[$key];
		}
		return -1;
	}
	
	/**
	 * Finds a move sequence that wins us the game, and begins executing it.
	 * @return integer 0-8
	 */
	private function findVictoryMove()	{
		$myMove = $this->myMoves[0];
		$victoryPairs = $this->victoryPairs[$myMove];
		
		//For each pair of winning moves, given our first move, find the one that is not blocked.
		//If no win is possible, return a random move.
		foreach($victoryPairs as $pair)	{
			foreach($pair as $space)	{
				if (in_array($space, $this->oppMoves))	{
					continue 2;
				}
			}
			$this->winMove = $pair[0];
			return $pair[1];
		}
		return $this->randomMove();
	}
	/**
	 * Tries to use a move that wins us the game that we found before.
	 * 
	 * If it can't, it returns a random move, because a draw is guarenteed.
	 * @return integer 0-8
	 */
	private function tryToWin()	{
		if (!in_array($this->winMove, $this->oppMoves))	{
			return $this->winMove;
		}
		
		//We got blocked!
		return $this->randomMove();
	}
	
	/**
	 * Only called when a draw is guarenteed.
	 * 
	 * rand is not very random, but at the point this is
	 * called, i'm fine with that. We're just trying to end the game.
	 * @return integer 0-8
	 */
	private function randomMove()	{
		$values = range(0,8);
		$taken = array_merge($this->myMoves, $this->oppMoves);
		$diff = array_diff($values, $taken);
		$rand = rand(0, count($diff));
		return $diff[$rand];
	}
	
	public function reset($first)	{
		$this->winMove = -1;
		$this->myMoves = array();
		$this->oppMoves = array();
		$this->first = $first;
	}

}