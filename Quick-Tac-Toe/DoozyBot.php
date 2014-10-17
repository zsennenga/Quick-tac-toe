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
			//Get the center.
			case 0:
				return 4;
			//Get 2 in a row
			case 1:
				return $this->findVictoryMove();
			//Attempt to execute our victory strategy
			case 2:
				return $this->tryToWin();
				
			//If we can't win, block and go for a draw
			//Most of the time we can go random and be fine
			//Some edge cases require you to block, however
			default:
				$move = $this->findBlockMove();
				if ($move === -1)	{
					$move = $this->randomMove();
				}
				return $move;
		}
	}
	
	/**
	 * Move if the bot goes second. The bot blocks unless the human player doesn't try to win.
	 * @param unknown $lastPlayerMove
	 * @return number|unknown
	 */
	private function getMoveSecond($lastPlayerMove)	{
		switch(count($this->myMoves))	{
			//Take the center if possible, if not, the first corner.
			case 0:
				if ($lastPlayerMove != 4)	{
					return 4;
				}
				else 	{
					return 0;
				}
			//Try to block the opponent if they are close to victory.
			//If that is not necessary, try to win.
			default:
				if ($this->winMove !== -1)	{
					return $this->tryToWin();
				}
				$move = $this->findBlockMove();
				if ($move === -1)	{
					$move = $this->findVictoryMove();
				}
				return $move;
			/*//If we calculated a series of winning moves last turn, execute.
			//Otherwise, go for a draw.
			case 2:
				if ($this->winMove !== -1)	{
					return $this->tryToWin();
				}
				return $this->findBlockMove();
			default:
				return $this->randomMove();*/
		}
	}
	
	/**
	 * Tries to block the human player, if possible.
	 * @return integer 0-8
	 */
	private function findBlockMove()	{
		$key = (string) $this->oppMoves[0] . (string) $this->oppMoves[1];
		//Find the space we need to block in order to stop the opponent from winning
		$blockMove = $this->getVictorySpace($key);
		
		//If we don't need to block for some reason, try to win instead.
		if ($blockMove === -1)	{
			return $blockMove;
		}
		
		//If we're already blocking them, we can go for a win instead.
		if (in_array($blockMove,$this->myMoves))	{
			return $this->findVictoryMove();
		}
		
		//Check if, by blocking, we are in a position to win
		$key = (string) $this->myMoves[0] . (string) $blockMove;
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
		
		//Given our first move, find the first pair of adjacent spaces that are not blocked.
		//If no win is possible, return begin forcing a draw.
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
		return $this->findBlockMove();
	}
	
	/**
	 * Only called when a draw is guarenteed, or there's nothing else we can do.
	 * 
	 * rand is not very random, but at the point this is
	 * called, i'm fine with that. We're just trying to end the game.
	 * @return integer 0-8
	 */
	private function randomMove()	{
		//Find the set difference of the array of integers 0-8 and the moves already taken.
		//Select randomly from that set.
		$values = range(0,8);
		$taken = array_merge($this->myMoves, $this->oppMoves);
		$diff = array_values(array_diff($values, $taken));
		$rand = rand(0, count($diff)-1);
		return $diff[$rand];
	}
	
	public function reset($first)	{
		$this->winMove = -1;
		$this->myMoves = array();
		$this->oppMoves = array();
		$this->first = $first;
	}

}